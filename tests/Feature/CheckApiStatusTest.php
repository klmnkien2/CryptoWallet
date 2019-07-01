<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckApiStatusTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testBaseWebUrl()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


    public function testGetWalletApi()
    {
        $response = $this->get('/api/wallets');

        $response->assertStatus(200);
        $resAsArray = (array) json_decode($response->content());

        if (!empty($resAsArray)) {
            $this->assertNotEmpty($resAsArray[0]->address);
        }
    }
}
