<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductExporterController extends Controller
{

	// It is presented here because the model layer is not used.
	public function getProducts() {

		$response = Http::withoutVerifying()
			->get('https://dummyjson.com/products');

		return $response->failed() ? [] : $response->json();
	}

	/**
	 *	Schema provider 
	 *  It is presented here because the model layer is not used.
	 */
	public function getSchema() {

		$schema = [
			'facebook' => [
				'json' => [],
				'xml' => [],
				'csv' => [],
			],
			'google' => [
				'json' => [
				]
			],
			'n11' => [
				'json' => [
				]
				'csv' => [
				]
			],
			'cimri' => [
				'json' => [
				]
				'xml' => [
					'element' => '<MerchantItems xmlns="http://www.cimri.com/schema/merchant/upload" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" />',
				],
			],



		];

		return $schema;
	}

	public function api()
	{
		return response()->json([
			'api' => '1.0.0',
			'name' => env('APP_NAME')
		]);
	}

	/**
	 * Product list
	 * @return <string>
	 */
	public function products()
	{
		return response()->json($this->getProducts());
	}

	/**
	 * Product list export
	 * @return <string>
	 */
	public function exportProducts()
	{
		return response()->json($this->getProducts());
	}
}
