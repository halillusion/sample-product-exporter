<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductExporterController extends Controller
{

	/**
	 * Product list
	 * It is presented here because the model layer is not used.
	 */
	public function getProducts() {

		$response = Http::withoutVerifying()
			->get('https://dummyjson.com/products');

		return $response->failed() ? [] : $response->json()['products'];
	}

	/**
	 *	Schema provider 
	 *  The schema can come from a separate method of the schema generator.
	 */
	public function getSchema() {

		$schema = [
			'facebook' => [
				'json' => [
					'item_detail' => [
						'name' => 'title',
						'description' => 'description',
						'price' => 'price',
						'discounted' => ['calculate' => ['price', 'discountPercentage'], 'type' => 'percentage'],
						'rating' => 'rating',
						'stock' => 'stock',
						'brand' => 'brand',
						'category' => 'category',
						'thumb' => 'thumbnail',
						'images' => 'images',
					]
				],
				'xml' => [],
				'csv' => [
					'item_detail' => [
						'name' => 'title',
						'description' => 'description',
						'price' => 'price',
						'discounted' => ['calculate' => ['price', 'discountPercentage'], 'type' => 'percentage'],
						'rating' => 'rating',
						'stock' => 'stock',
						'brand' => 'brand',
						'category' => 'category',
						'thumb' => 'thumbnail',
						'images' => 'images',
					]
				],
			],
			'google' => [
				'json' => [
					'group_tag' => 'g:products',
					'item_tag' => 'g:product',
					'item_detail' => [
						'g:name' => 'title',
						'g:description' => 'description',
						'g:price' => 'price',
						'g:discounted' => ['calculate' => ['price', 'discountPercentage'], 'type' => 'percentage'],
						'g:rating' => 'rating',
						'g:stock' => 'stock',
						'g:brand' => 'brand',
						'g:category' => 'category',
						'g:thumb' => 'thumbnail',
						'g:images' => 'images',
					]
				],
			],
			'n11' => [
				'json' => [
					'item_detail' => [
						'name' => 'title',
						'description' => 'description',
						'price' => 'price',
						'discounted' => ['calculate' => ['price', 'discountPercentage'], 'type' => 'percentage'],
						'rating' => 'rating',
						'stock' => 'stock',
						'brand' => 'brand',
						'category' => 'category',
						'thumb' => 'thumbnail',
						'images' => 'images',
					]
				],
				'csv' => [
					'item_detail' => [
						'name' => 'title',
						'description' => 'description',
						'price' => 'price',
						'discounted' => ['calculate' => ['price', 'discountPercentage'], 'type' => 'percentage'],
						'rating' => 'rating',
						'stock' => 'stock',
						'brand' => 'brand',
						'category' => 'category',
						'thumb' => 'thumbnail',
						'images' => 'images',
					]
				]
			],
			'cimri' => [
				'json' => [
					'item_detail' => [
						'name' => 'title',
						'description' => 'description',
						'price' => 'price',
						'discounted' => ['calculate' => ['price', 'discountPercentage'], 'type' => 'percentage'],
						'rating' => 'rating',
						'stock' => 'stock',
						'brand' => 'brand',
						'category' => 'category',
						'thumb' => 'thumbnail',
						'images' => 'images',
					]
				],
				'xml' => [
					'group_tag' => 'products',
					'item_tag' => 'product',
					'item_detail' => [
						'name' => 'title',
						'description' => 'description',
						'price' => 'price',
						'discounted' => ['calculate' => ['price', 'discountPercentage'], 'type' => 'percentage'],
						'rating' => 'rating',
						'stock' => 'stock',
						'brand' => 'brand',
						'category' => 'category',
						'thumb' => 'thumbnail',
						'images' => 'images',
					]
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
	public function exportProducts(Request $request)
	{
		$response = [
			'code' => 404,
			'status' => false,
			'message' => 'Schema is invalid!',
		];
		$platform = $request->input('platform');
		$format = $request->input('format');

		$schema = $this->getSchema();

		if (isset($schema[$platform]) !== false) {

			$response['message'] = 'Format is invalid!';
			if (isset($schema[$platform][$format]) !== false) {

				$response['format'] = $format;
				$response['code'] = 200;
				$response['status'] = true;

				$template = $schema[$platform][$format];
				$products = $this->getProducts();
				$output = [];

				foreach ($products as $product) {
							
					$output[] = $this->productDataFormatter(
						$product, 
						$schema[$platform][$format]['item_detail']
					);

				}

				if (isset($schema[$platform][$format]['item_tag']) !== false) {
					$itemTag = $schema[$platform][$format]['item_tag'];
					$_output = $output;
					$output = [];

					foreach ($_output as $item) {
						$output[$itemTag][] = $item;
					}
				}

				if (isset($schema[$platform][$format]['group_tag']) !== false) {
					$output = [
						$schema[$platform][$format]['group_tag'] => $output
					];
				}
				

				switch ($format) {
					case 'csv':
						$output = $this->toCSV($output);
						break;

					case 'xml':
						$tag = 'root';
						if (isset($schema[$platform][$format]['group_tag']) !== false) {
							$tag = $schema[$platform][$format]['group_tag'];
							$output = $output[$tag];
						}
						$output = \Spatie\ArrayToXml\ArrayToXml::convert($output, $tag);
						break;
				}

				$response['output'] = $output;
			}
		}


		if (isset($response['output']) !== false) {

			switch ($response['format']) {
				case 'csv':
					return response($response['output'], $response['code'])
						->header('Content-Type', 'text/csv');

				case 'xml':
					return response($response['output'], $response['code'])
						->header('Content-Type', 'text/xml');
				
				default:
					return response()
						->json($response['output'], $response['code']);
			}
			

		} else {

			return response()
				->json([
					'status' => $response['status'], 
					'message' => $response['message']
				], $response['code']);
		}
	}

	/**
	 * Product Data Formatter
	 * @param product <array>
	 * @return product <array>
	 */
	public function productDataFormatter($product, $details): array {

		$return = [];


		foreach ($details as $tag => $parameter) {
			
			$value = null;
			// price calculator
			if (is_array($parameter) AND isset($parameter['calculate']) !== false) {

				if (isset( $product[$parameter['calculate'][0]] ) !== false AND isset( $product[$parameter['calculate'][1]] ) !== false) {

					$price = (float) $product[$parameter['calculate'][0]];

					switch ($parameter['type']) {
						case 'percentage':
							$percentage = (float) $product[$parameter['calculate'][1]];
							$value = $price - (($price / 100) * $percentage);

							break;
						
						case 'subtract':
							$discount = (float)$parameter['calculate'][1];
							$value = $price - $discount;
							
							break;
					}
				}

			} elseif (is_string($parameter) AND isset($product[$parameter]) !== false) {

				$value = $product[$parameter];
			}

			$return[$tag] = $value;

		}

		return $return;

	}

	/**
	 * Array to CSV
	 * @param data <array>
	 * @return csv <string>
	 */
	public function toCSV($data): string {

		$file = 'temp_json_' . time();
		$fp = fopen($file, 'w');
		$header = false;
		foreach ($data as $row)
		{
			if (empty($header))
			{
				$header = array_keys($row);
				fputcsv($fp, $header);
				$header = array_flip($header);
			}

			$row = array_map(function ($r) {
				return is_array($r) ? implode(', ', $r) : $r;
			}, $row);

			fputcsv($fp, 
				array_merge(
					$header, 
					$row
				)
			);
		}
		fclose($fp);

		$csv = file_get_contents($file);
		unlink($file);
		return $csv;
	}
}
