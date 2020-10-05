<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offers';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'status','price','until'
    ];
}
