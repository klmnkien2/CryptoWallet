<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewWalletRequest;
use App\Http\Requests\WithdrawRequest;
use App\Services\EthereumService;
use App\Wallet;
use App\Http\Resources\Wallet as WalletReource;
use Illuminate\Http\Request;
use Web3p\EthereumTx\Transaction;

class WalletController extends Controller
{

    protected $ethereumService;

    /**
     * WalletController constructor.
     * @param EthereumService $ethereumService
     */
    public function __construct(
        EthereumService $ethereumService
    )
    {
        $this->ethereumService = $ethereumService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wallets = Wallet::all();
        foreach ($wallets as $key => $aWallet) {
            $balance = number_format(floatval($this->ethereumService->getBalance($aWallet->address))/1000000000000000000, 5);
            $wallets[$key]->balance = $balance;
        }
        return response()
            ->json($wallets);
    }

    /**
     * Reload the balance to show.
     *
     * @param  string  $address
     * @return \Illuminate\Http\Response
     */
    public function reload($address)
    {
        $balance = number_format(floatval($this->ethereumService->getBalance($address))/1000000000000000000, 5);
        return response()
            ->json(['balance' => $balance]);
    }


    /**
     * @param NewWalletRequest $request
     */
    public function create(NewWalletRequest $request)
    {
        $wallet = Wallet::create(array_merge($request->all(), ['balance' => '0.00000']));
        return new WalletReource($wallet);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Wallet::destroy($id);
        return response()
            ->json(['status' => 'success']);
    }

    public function prepareTransaction(WithdrawRequest $request)
    {
        $fromAddress = $request->input('fromAddress');
        $toAddress = $request->input('toAddress');
        $amount = $request->input('amount');
        $gas = $request->input('gas');
        $gasPrice = $request->input('gasPrice');

        $wallet = Wallet::where('address', '=', $fromAddress)->first();
//        $gasPrice = $this->ethereumService->getGasPrice();
//        if (empty($gasPrice)) {
//            return response()
//                ->json(['error' => "Can't not get gas price"]);
//        }
//        $amount = floatval($amount) - floatval($gasPrice) * 21000 / 1000000000000000000;

        $response = $this->ethereumService->getTransactionCount($fromAddress);
        $ethereumNonce = $response['result'];

        $transaction = new Transaction([
            'nonce' => $ethereumNonce,
            'from' => $fromAddress,
            'to' => $toAddress,
            'gas' => $gas,
            'gasPrice' => $gasPrice,
            'value' => $amount,
            'data' => '',
            'chainId' => 3,
        ]);
        $privateKey = substr($wallet->private, 2);
        $rawData = '0x' . $transaction->sign($privateKey);

        return $this->sendTransaction($rawData);
    }

    private function sendTransaction($rawData)
    {
        if (empty($rawData)) {
            return response()->json([
                'error' => 'Transaction data is null.'
            ], 500);
        }
        $txHash = '';
        $response = $this->ethereumService->sendRaw($rawData);
        if ($response && empty($response['error']) && !empty($response['result'])) {
            $txHash =  $response['result'];
        }
        if (empty($txHash)) {
            return response()->json([
                'error' => 'Transaction could not be done.'
            ], 500);
        }
        return response()->json([
            'txHash' => $txHash
        ]);
    }
}
