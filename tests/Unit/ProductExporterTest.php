<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;

class ProductExporterTest extends TestCase
{
    /**
     * GET API Home
     *
     * @return void
     */
    public function test_get_api_home()
    {
        $this->get('/')
            ->assertJson([
                "api" => "1.0.0"
            ])
            ->assertStatus(200);
    }

    /**
     * GET API Products
     *
     * @return void
     */
    public function test_get_api_products()
    {
        $this->get('/products')
             ->assertJson(fn (AssertableJson $json) =>
                $json->hasAll([30])
            )
            ->assertStatus(200);
    }

    /**
     * POST API Product Export
     *
     * @return void
     */
    public function test_post_api_export_google_json_without_param()
    {
        $this->post('/products/export')
            ->assertJson([
                "status" => false
            ])
            ->assertStatus(404);
    }

    /**
     * POST API Product Export
     *
     * @return void
     */
    public function test_post_api_export_google_json_with_param()
    {
        $this->post('/products/export', [
            'platform' => 'google',
            'format' => 'json'
        ])
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('g:products')
            )
            ->assertStatus(200);
    }

    /**
     * POST API Product Export
     *
     * @return void
     */
    public function test_post_api_export_cimri_xml_with_param()
    {
        $this->post('/products/export', [
            'platform' => 'cimri',
            'format' => 'xml'
        ])->assertOk();
    }


    /**
     * POST API Product Export
     *
     * @return void
     */
    public function test_post_api_export_n11_csv_with_param()
    {
        $this->post('/products/export', [
            'platform' => 'n11',
            'format' => 'csv'
        ])->assertOk();
    }
}
