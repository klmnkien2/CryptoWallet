<?php

namespace App\Contracts\Wallet;

interface AccountService
{
    /**
     * Generate a crypto address.
     *
     * @return mixed
     */
    public function generate();

    /**
     * Return balance of crypto store in address.
     *
     * @param  string  $address
     * @return string
     */
    public function getBalance($address);

    /**
     * Send crypto from address to address.
     *
     * @param  string  $fromAddress
     * @param  string  $toAddress
     * @param  string  $amount
     * @return mixed
     */
    public function send($fromAddress, $toAddress, $amount);
}