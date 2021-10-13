<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'price', 'categories_id', 'tags', 'user_id'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function galleries(){
        return $this->hasMany(ProductGallery::class, 'products_id', 'id');
    }

    public function category(){
        return $this->belongsTo(ProductCatgory::class, 'categories_id', 'id');
    }
}
