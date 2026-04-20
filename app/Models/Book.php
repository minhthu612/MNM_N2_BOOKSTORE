<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';
    protected $primaryKey = 'book_id';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'author',
        'price',
        'discount',
        'description',
        'link_images',
        'category_id',
        'sold_quantity'
    ];

    public function orderDetails()
{
    return $this->hasMany(OrderDetail::class, 'book_id', 'book_id');
}
}