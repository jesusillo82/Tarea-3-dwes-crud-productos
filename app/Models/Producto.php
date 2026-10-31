<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    /** Nombre de la tabla existente usada por el modelo. */
    protected $table = 'productos';

    /** La tabla de la tarea no contiene las marcas temporales de Laravel. */
    public $timestamps = false;

    /** Campos permitidos para asignación masiva durante altas y cambios. */
    protected $fillable = [
        'nombre',
        'nombre_corto',
        'descripcion',
        'pvp',
        'familia',
    ];

    /** Convierte el precio a decimal con dos posiciones al leerlo. */
    protected function casts(): array
    {
        return [
            'pvp' => 'decimal:2',
        ];
    }

    /** Familia asociada por productos.familia = familias.cod. */
    public function familiaRelacion(): BelongsTo
    {
        return $this->belongsTo(Familia::class, 'familia', 'cod');
    }
}
