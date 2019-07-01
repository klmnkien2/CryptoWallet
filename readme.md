## Tech-stack
- User Laravel 5.7 with Reactjs
- Tested in  Windows 64-bit with Node.js 10.x only. Please copy node_modules from my .zip file. Because there are some problems with Ethereum lib version. 
- Single-page web, every interaction call api only.

## Requirement
1. Manage Ethereum Wallet
- Generate an address correctly, which can use in ethereum system
- Could manage addresses, view balance, information. Delete from your wallets
2. Can do withdrawal
- From your wallet, pick any ethereum address. Input an address that will receive ETH, input amount. Then you can transfer ETH
- You can only transfer from addresses in your wallet (that you manage)
- Show transaction id (and link) to validate transaction

## Installation Guide
(PHP part)
- Clone github repo: https://github.com/klmnkien2/CryptoWallet.git
- composer install
- cp .env.example .env
- edit .env file, config your DB info
- php artisan migrate

(If you need an ethereum address that have balance to test)
- php artisan db:seed --class=WalletsTableSeeder  (You may need to rum composer dump-autoload before)

- For unit test run: vendor/bin/phpunit tests/Feature/ (check files for more details)

(Reactjs part)
- npm install
- npm run development

## How to use
- Go to browser , access localhost
- You can see a table of addresses and a form to withdraw
- If you click to 'Add a wallet' - an address will be generated ( you can also go to https://ropsten.etherscan.io and access that address too )
- If you want to withdraw. Choose an address in your wallet (you can choose only these address) -> Then input an address (any address in ropsten network is accepted) -> Choose value (float, and use . as decimal char) -> click Withdraw button
- NOTED: There should be some validation. But haven't added yet

