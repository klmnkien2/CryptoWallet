import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import WalletRow from './WalletRow';
import WithdrawForm from './WithdrawForm';

var Web3 = require('web3');
var web3 = new Web3();

export default class WalletApp extends Component {
    constructor (props) {
        super(props)
        this.state = { wallets: [] }
    }

    componentDidMount () {
        axios.get(window.Laravel.baseUrl + '/api/wallets')
            .then(response => {
                this.setState({ wallets: response.data })
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    reloadWallet (key, balance) {
        const { wallets } = this.state;
        wallets[key].balance = balance;

        // update state
        this.setState({
            wallets
        });
    }

    addWallet () {
        const account = web3.eth.accounts.create();
        console.log(account.address);
        if (!account.address) {
            alert('Please try again..!')
        }
        var bodyFormData = new FormData();
        bodyFormData.set('address', account.address);
        bodyFormData.set('private', account.privateKey);
        axios({
            method: 'post',
            url: window.Laravel.baseUrl + '/api/wallets/create',
            data: bodyFormData,
            config: { headers: {'Content-Type': 'multipart/form-data' }}
            })
            .then(response => {
                const wallets = [...this.state.wallets, response.data];
                this.setState( {wallets} );
            })
            .catch(function (error) {
                if (error.response) {
                    // The request was made and the server responded with a status code
                    // that falls out of the range of 2xx
                    alert(error.response.data.message);
                }
            })
    }

    deleteWallet (key) {
        const wallets = [...this.state.wallets];
        wallets.splice(key, 1);
        this.setState( {wallets} );
    }

    getWallets () {
        const { wallets } = this.state
        const walletList = wallets.length ? (
            wallets.map((object, i) => {
                return <WalletRow obj={object} key={i} index={i} deleteWallet={ this.deleteWallet.bind(this) } reloadWallet={ this.reloadWallet.bind(this) } />
            })
        ) : (
            <tr>
                <td className="font-weight-bold" colSpan="3">There is no record.</td>
            </tr>
        );

        return (
            <table className='table table-hover'>
                <thead>
                <tr>
                    <td>Address</td>
                    <td>Balance</td>
                    <td>Actions</td>
                </tr>
                </thead>
                <tbody>
                {walletList}
                </tbody>
            </table>
        )
        // if (this.state.wallets instanceof Array) {
        //     return this.state.wallets.map( (object, i) => {
        //         return <WalletRow obj={object} key={i} index={i} deleteWallet={ this.deleteWallet.bind(this) } />
        //     })
        // }
    }

    getAddressesAsOption () {
        const { wallets } = this.state
        return (
            wallets.map((object, i) => {
                return (
                    <option key={object.address} value={object.address}>{object.address}</option>
                )
            })
        )
    }

    render() {
        return (
            <div className="container">
                <div className="justify-content-center">
                    <div className='clearfix'>
                        <a className='btn btn-success pull-right' href="#" onClick={ this.addWallet.bind(this) }>Add a wallet</a>
                    </div>
                    <br />

                    { this.getWallets() }

                    <WithdrawForm getOptions={ this.getAddressesAsOption.bind(this) } />
                </div>
            </div>
        );
    }
}

if (document.getElementById('app')) {
    ReactDOM.render(<WalletApp />, document.getElementById('app'));
}
