<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'image'
    ];
    protected $appends = [
        'image_url',
        'count'
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image != null)
            return Storage::url($this->image);
        return null;
    }
    public function getcountAttribute()
    {
        return $this->items->where('sold', false)->count();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function items()
    {
        return $this->hasMany('App\Models\Item');
    }
}
