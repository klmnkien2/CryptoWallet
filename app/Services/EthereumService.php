<?php

namespace App\Services;

use App\Contracts\Wallet\AccountService;
use GuzzleHttp\Client;

class EthereumService implements AccountService
{
    protected $url = 'https://ropsten.etherscan.io/api';

    protected $apiKey = 'P4V3KFV9VYDB76YD6V5YEGYBAYC8W5VIIE';

    public function __construct()
    {
         $this->url = config('wallet.ether_api_url');
         $this->apiKey = config('wallet.ether_api_key');
    }

    protected function request($aParameters = array(), $useApiKey = true)
    {
        $aResult = false;

        if (empty($aParameters)) {
            return $aResult;
        }

        if ($useApiKey) {
            $aParameters['apikey'] = $this->apiKey;
        }
        $url = $this->url . '?' . http_build_query($aParameters);

        $client = new Client();
        $response = $client->get($url);
        $json = $response->getBody();

        $aResult = json_decode($json, true);

        return $aResult;
    }

    public function getBalance($addr)
    {
        $params = [
            'module' => 'account',
            'action' => 'balance',
            'address' => $addr,
            'tag' => 'latest'
        ];

        $balance = '0';
        $balanceObj = $this->request($params);
        if (!empty($balanceObj['result'])) {
            $balance = $balanceObj['result'];
        }

        return $balance;
    }

    public function getGasPrice()
    {
        $params = [
            'module' => 'proxy',
            'action' => 'eth_gasPrice',
            'apikey' => 'noneedtouse'
        ];

        $response = $this->request($params, false);
        if (!empty($response['result'])) {
            $gasPrice = $response['result'];
            $gasPrice = hexdec($gasPrice);
            //dd($response['result'], $gasPrice);
            return $gasPrice;
        }
        return null;
    }

    public function getTransactions($addr, $page, $offset = 15)
    {
        $params = [
            'module' => 'account',
            'action' => 'txlist',
            'address' => $addr,
            'sort' => 'desc',
            'startblock' => 0,
            'endblock' => 99999999,
            'page' => $page,
            'offset' => $offset
        ];

        return $this->request($params);
    }

    public function sendRaw($rawTx)
    {
        $params = [
            'module' => 'proxy',
            'action' => 'eth_sendRawTransaction',
            'hex' => $rawTx
        ];
        return $this->request($params);
    }

    public function getTransactionCount($addr)
    {
        $params = [
            'module' => 'proxy',
            'action' => 'eth_getTransactionCount',
            'address' => $addr,
            'tag' => 'latest'
        ];

        return $this->request($params);
    }

    public function getTransactionStatus($txhash)
    {
        $params = [
            'module' => 'proxy',
            'action' => 'eth_getTransactionByHash',
            'txhash' => $txhash,
            'apikey' => 'YourApiKeyToken'
        ];

        return $this->request($params, false);
    }

    /**
     * Generate a crypto address.
     *
     * @return mixed
     */
    public function generate()
    {
        // TODO: Implement generate() method.
    }

    /**
     * Send crypto from address to address.
     *
     * @param  string $fromAddress
     * @param  string $toAddress
     * @param  string $amount
     * @return mixed
     */
    public function send($fromAddress, $toAddress, $amount)
    {
        // TODO: Implement send() method.
    }
}

