<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EtherServiceTest extends TestCase
{

    protected $ethereumService;

    public function setUp() {

        parent::setUp();

        $this->ethereumService = $this->app->make('App\Services\EthereumService');
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetBalance()
    {
        $response = $this->ethereumService->getBalance('0xE8B0e79aEbeb2046d25997a23013BaF4113D2793');

        $this->assertIsString($response);
        $this->assertRegExp('/^([0-9])*$/', $response);
    }
}
