<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductoCrudTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('familias', function (Blueprint $table): void {
            $table->string('cod', 6)->primary();
            $table->string('nombre', 100);
        });

        Schema::create('productos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('nombre', 200);
            $table->string('nombre_corto', 50);
            $table->text('descripcion')->nullable();
            $table->decimal('pvp', 10, 2);
            $table->string('familia', 6);
        });

        DB::table('familias')->insert([
            ['cod' => 'ORDENA', 'nombre' => 'Ordenadores'],
        ]);
    }

    public function test_product_list_is_displayed(): void
    {
        $this->createProduct();
        $this->assertSame('Ordenadores', \App\Models\Producto::with('familiaRelacion')->first()->familiaRelacion->nombre);

        $this->get(route('productos.index'))
            ->assertOk()
            ->assertSee('Portátil de prueba')
            ->assertSee('Ordenadores');
    }

    public function test_product_can_be_created_updated_and_deleted(): void
    {
        $this->post(route('productos.store'), [
            'nombre' => 'Producto nuevo',
            'nombre_corto' => 'Nuevo',
            'descripcion' => 'Descripción de prueba',
            'pvp' => '19.95',
            'familia' => 'ORDENA',
        ])->assertRedirect(route('productos.show', 1));

        $this->assertDatabaseHas('productos', [
            'id' => 1,
            'nombre' => 'Producto nuevo',
            'pvp' => '19.95',
            'familia' => 'ORDENA',
        ]);

        $this->put(route('productos.update', 1), [
            'nombre' => 'Producto actualizado',
            'nombre_corto' => 'Actualizado',
            'descripcion' => '',
            'pvp' => '25.00',
            'familia' => 'ORDENA',
        ])->assertRedirect(route('productos.show', 1));

        $this->assertDatabaseHas('productos', [
            'id' => 1,
            'nombre' => 'Producto actualizado',
            'pvp' => '25.00',
        ]);

        $this->delete(route('productos.destroy', 1))
            ->assertRedirect(route('productos.index'));

        $this->assertDatabaseMissing('productos', ['id' => 1]);
    }

    public function test_invalid_product_data_is_rejected(): void
    {
        $this->from(route('productos.create'))
            ->post(route('productos.store'), [
                'nombre' => '',
                'nombre_corto' => '',
                'descripcion' => '',
                'pvp' => '-1',
                'familia' => 'NOEXIST',
            ])
            ->assertRedirect(route('productos.create'))
            ->assertSessionHasErrors(['nombre', 'nombre_corto', 'pvp', 'familia']);

        $this->assertDatabaseCount('productos', 0);
    }

    private function createProduct(): void
    {
        DB::table('productos')->insert([
            'nombre' => 'Portátil de prueba',
            'nombre_corto' => 'Portátil',
            'descripcion' => 'Descripción',
            'pvp' => '999.99',
            'familia' => 'ORDENA',
        ]);
    }
}
