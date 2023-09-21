<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Services\CurrencyService;

class CurrencyTest extends TestCase
{
    /**
     * @test
     */
    public function convert_usd_to_eur_successful(): void
    {
        $result = (new CurrencyService())->convert(amount:100, currencyFrom:'usd', currencyTo:'eur');
        
        $this->assertEquals(expected: 98, actual: $result);
    }

    /**
    @test
    */
    public function convert_usd_to_yen_zero(): void
    {
        $result = (new CurrencyService())->convert(amount:100, currencyFrom:'usd', currencyTo:'yen');
        
        $this->assertEquals(expected: 0, actual: $result);
    }
}
