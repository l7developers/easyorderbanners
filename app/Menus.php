<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menus extends Model{
	
    protected $table = 'menus';
	
	public function menu(){
		return $this->hasMany('\App\Menus','parent_id')->with('category','product','page')->orderBy('weight','ASC');
	}
	
	public function category(){
		return $this->belongsTo('\App\Category','category_id');
	}
	
	public function product(){
		return $this->belongsTo('\App\Products','product_id');
	}
	
	public function page(){
		return $this->belongsTo('\App\StaticPage','page_id');
	}
}
