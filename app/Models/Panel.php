<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panel extends Model
{
    protected $table = 'paneles';
    protected $primaryKey = 'id_panel';
    public $incrementing = true;

    protected $fillable = [
        'modelo',
        'eficiencia',
        'superficie',
        'produccion',
        'id_producto',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
