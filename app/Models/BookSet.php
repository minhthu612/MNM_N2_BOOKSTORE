<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookSet extends Model
{
    protected $table = 'book_sets';

    protected $primaryKey = 'set_id';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'price',
        'discount',
        'link_images',
        'sold_quantity'
    ];
}