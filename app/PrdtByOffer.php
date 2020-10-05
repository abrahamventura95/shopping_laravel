<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrdtByOffer extends Model{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prdt_by_offer';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'product_id','offer_id'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'product_id','offer_id'
    ];
}
