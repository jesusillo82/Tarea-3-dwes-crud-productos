<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_homepage_redirects_to_the_product_list(): void
    {
        $this->get('/')
            ->assertRedirect(route('productos.index'));
    }
}
