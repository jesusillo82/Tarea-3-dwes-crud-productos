<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Familia extends Model
{
    /** Nombre de la tabla existente usada por el modelo. */
    protected $table = 'familias';

    /** Código alfanumérico de la familia, en lugar de la clave id por defecto. */
    protected $primaryKey = 'cod';

    /** El código de familia lo aporta el catálogo y no lo genera la base de datos. */
    public $incrementing = false;

    /** El código de familia se maneja como texto. */
    protected $keyType = 'string';

    /** La tabla del catálogo no contiene las marcas temporales de Laravel. */
    public $timestamps = false;

    /** Campos permitidos para asignación masiva. */
    protected $fillable = ['cod', 'nombre'];

    /** Productos cuya clave familia coincide con el código de esta familia. */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'familia', 'cod');
    }
}
