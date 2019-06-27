import React, { Component } from 'react'
import axios from "axios";
var util = require('ethereumjs-util');
var etherTx = require('ethereumjs-tx');

class WithdrawForm extends Component {
    constructor (props) {
        super(props);
        this.state = {
            fromAddress: '',
            toAddress: '',
            amount: '',
            txHash: '',
            disableWithdraw: false
        }

        console.log(this.props)
        this.handleChange = this.handleChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
    }

    handleChange (e) {
        this.setState({
            [e.target.id]: e.target.value
        })
    }

    handleSubmit (e) {
        e.preventDefault();
        this.setState(prevState => ({
            disableWithdraw: true
        }))

        var bodyFormData = new FormData();
        bodyFormData.set('fromAddress', this.state.fromAddress);
        bodyFormData.set('toAddress', this.state.toAddress);
        bodyFormData.set('amount', this.state.amount);
        axios({
            method: 'post',
            url: window.Laravel.baseUrl + '/api/wallets/transaction/prepare',
            data: bodyFormData,
            config: { headers: {'Content-Type': 'multipart/form-data' }}
        })
            .then(response => {
                this.sendRawTransaction(response.data)
            })
            .catch(function (error) {
                if (error.response) {
                    // The request was made and the server responded with a status code
                    // that falls out of the range of 2xx
                    alert(error.response.data.error);
                    this.setState(prevState => ({
                        disableWithdraw: false
                    }))
                }
            })
    }

    sendRawTransaction(data) {
        const rawTransaction = {
            "from": data.fromAddress,
            "gas": web3.toHex(100000),
            "gasPrice": web3.toHex(30000),
            "to": data.toAddress,
            "value": web3.toHex(web3.toWei(data.amount, 'ether')),
            "nonce": data.ethereumNonce,
            "data": "",
        };

        const tx = new etherTx(rawTransaction);
        const privateKey = new Buffer.from(data.privateKey, 'hex');
        tx.sign(privateKey);
        const serializedTx = tx.serialize();
        console.log(serializedTx.toString('hex'))
        var bodyFormData = new FormData();
        bodyFormData.set('rawData', '0x' + serializedTx.toString('hex'));
        axios({
            method: 'post',
            url: window.Laravel.baseUrl + '/api/wallets/transaction/send',
            data: bodyFormData,
            config: { headers: {'Content-Type': 'multipart/form-data' }}
        })
            .then(response => {
                this.setState(prevState => ({
                    txHash: response.data.txHash
                }))
                this.setState(prevState => ({
                    disableWithdraw: false
                }))
            })
            .catch(function (error) {
                if (error.response) {
                    // The request was made and the server responded with a status code
                    // that falls out of the range of 2xx
                    alert(error.response.data.error);
                }
                this.setState(prevState => ({
                    disableWithdraw: false
                }))
            })
    }

    render() {
        const linkToTx = 'https://ropsten.etherscan.io/tx/' + this.state.txHash
        const messageResult = this.state.txHash ? (
            <div className="align-content-center">
                Your transaction was sent with ID: &nbsp;
                <a href={linkToTx} target="_blank">{this.state.txHash}</a>
                <br />
            </div>
            ) : ( <br /> )
        const addresses = this.props.getOptions()
        return (
            <form onSubmit={this.handleSubmit}>
                <div className='form-group'>
                    <label htmlFor='fromAddress'>Address</label>
                    <select className='form-control' id='fromAddress' onChange={this.handleChange} required>
                        <option>Choose one</option>
                        {addresses}
                    </select>
                </div>
                <div className='form-group'>
                    <label htmlFor='toAddress'>To Address</label>
                    <input type='text' className='form-control' id='toAddress' placeholder='To Address'
                           onChange={this.handleChange} required />
                </div>
                <div className='form-group'>
                    <label htmlFor='amount'>Amount</label>
                    <input type='text' className='form-control' id='amount' placeholder='Amount'
                           onChange={this.handleChange} required />
                </div>
                <button type='submit' className='btn btn-primary' disabled={this.state.disableWithdraw}>Withdraw</button>
                {messageResult}
            </form>
        )
    }
}

export default WithdrawForm