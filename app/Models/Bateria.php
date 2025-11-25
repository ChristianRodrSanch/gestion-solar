<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bateria extends Model
{
    protected $table = 'baterias';
    protected $primaryKey = 'id_bateria';
    public $incrementing = true;

    protected $fillable = [
        'modelo',
        'capacidad',
        'autonomia',
        'id_producto',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
