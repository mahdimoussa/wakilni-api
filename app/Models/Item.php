<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number',
        'sold',
        'product_type_id',
    ];
    protected $cast = [
        'sold' => 'boolean'
    ];

    public function product_type()
    {
        return $this->belongsTo('App\Models\ProductType');
    }
}
