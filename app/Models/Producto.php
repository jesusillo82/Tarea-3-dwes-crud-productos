<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    protected $table = 'productos';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'nombre_corto',
        'descripcion',
        'pvp',
        'familia',
    ];

    protected function casts(): array
    {
        return [
            'pvp' => 'decimal:2',
        ];
    }

    public function familiaRelacion(): BelongsTo
    {
        return $this->belongsTo(Familia::class, 'familia', 'cod');
    }
}
