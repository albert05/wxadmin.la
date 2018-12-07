<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use SoftDeletes;
	
	protected $table = 'bank';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];

	protected $dates = [];
}
