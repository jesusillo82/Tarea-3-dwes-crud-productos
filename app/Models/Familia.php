<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Familia extends Model
{
    protected $table = 'familias';

    protected $primaryKey = 'cod';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = ['cod', 'nombre'];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'familia', 'cod');
    }
}
