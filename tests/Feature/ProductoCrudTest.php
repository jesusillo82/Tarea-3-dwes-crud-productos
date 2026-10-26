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
            ->assertSee('<h1 class="h3 mb-3">Listado de productos</h1>', false)
            ->assertSee('<table class="table table-light table-striped align-middle">', false)
            ->assertSee('Gestión de productos. Todos los derechos reservados.')
            ->assertSee('Portátil de prueba')
            ->assertSee('Ordenadores');
    }

    public function test_product_description_displays_characteristic_names_in_bold(): void
    {
        DB::table('productos')->insert([
            'nombre' => 'Ordenador de prueba',
            'nombre_corto' => 'Ordenador',
            'descripcion' => "Tipo de Procesador: Core i5\nModelo de Procesador: i5-650\nNúcleo de Procesador: Dual-core",
            'pvp' => '499.99',
            'familia' => 'ORDENA',
        ]);

        $this->get(route('productos.show', 1))
            ->assertOk()
            ->assertSeeHtml('<strong>Tipo de Procesador:</strong> Core i5')
            ->assertSeeHtml('<strong>Modelo de Procesador:</strong> i5-650')
            ->assertSeeHtml('<strong>Núcleo de Procesador:</strong> Dual-core');
    }

    public function test_edit_product_shows_description_in_styled_editable_field_without_preview(): void
    {
        DB::table('productos')->insert([
            'nombre' => 'Ordenador de prueba',
            'nombre_corto' => 'Ordenador',
            'descripcion' => "Tipo de Procesador: Core i5\nModelo de Procesador: i5-650",
            'pvp' => '499.99',
            'familia' => 'ORDENA',
        ]);

        $this->get(route('productos.edit', 1))
            ->assertOk()
            ->assertSee('<article class="card">', false)
            ->assertSee('<div class="card-header">', false)
            ->assertSee('<div class="card-body">', false)
            ->assertDontSee('Crear producto')
            ->assertSee('card-body')
            ->assertSee('textarea')
            ->assertSee('Tipo de Procesador: Core i5')
            ->assertDontSee('Vista previa');
    }

    public function test_create_product_button_is_hidden_on_product_detail(): void
    {
        $this->createProduct();

        $this->get(route('productos.show', 1))
            ->assertOk()
            ->assertDontSee('Crear producto')
            ->assertDontSee('>Editar</a>', false)
            ->assertSee('Volver al listado');
    }

    public function test_create_product_button_is_hidden_on_create_page(): void
    {
        $this->get(route('productos.create'))
            ->assertOk()
            ->assertDontSee('btn btn-light text-primary fw-semibold px-3', false);
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
