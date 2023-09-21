<?php

	namespace App\Services;
	
	class CurrencyService
	{
			const RATES = [
					'usd' => 
					[
							'eur' => 0.98
					]
					// ,[
					// 	'brl' => 0.35
					// ]
			];

			public function convert(float $amount, string $currencyFrom, string $currencyTo): float
			{
				$rate = self::RATES[$currencyFrom][$currencyTo] ?? 0;

				return round (num: $amount * $rate, precision: 2);
				// return round ($amount * $rate, 2);
			}
	}