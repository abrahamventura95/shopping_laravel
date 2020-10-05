
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'name','description','image','amount','price','shop'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'shop'
    ];
}
