import React, { Component } from 'react'
import axios from 'axios'

class WalletRow extends Component {
    constructor (props) {
        super(props);
        this.handleDelete = this.handleDelete.bind(this)
        this.handleReload = this.handleReload.bind(this)
    }
    handleReload (e) {
        e.preventDefault()
        let url = window.Laravel.baseUrl + '/api/wallets/reload/' + this.props.obj.address
        axios.get(url)
            .then(response => {
                this.props.reloadWallet(this.props.index, response.data.balance)
            })
            .catch(function (error) {
                console.log(error)
            })
    }
    handleDelete (e) {
        e.preventDefault()
        if (!confirm('Are your sure you want to delete this item?')) {
            return false
        }

        let url = window.Laravel.baseUrl + '/api/wallets/destroy/' + this.props.obj.id
        axios.delete(url)
            .then(response => {
                this.props.deleteWallet(this.props.index)
            })
            .catch(function (error) {
                console.log(error)
            })
    }
    render () {
        return (
            <tr id={this.props.obj.id}>
                <td>
                    {this.props.obj.address}
                </td>
                <td>
                    {this.props.obj.balance}
                </td>
                <td>
                    <button className='btn btn-primary' onClick={this.handleReload}>Reload</button>&nbsp;
                    <button className='btn btn-danger' onClick={this.handleDelete}>Delete</button>
                </td>
            </tr>
        )
    }
}

export default WalletRow