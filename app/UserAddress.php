<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use \App\UserAddress;

class UserAddress extends Model{
	
    protected $table = 'user_address';
	
	public static function getAddress($id){
		$data['billing'] =  UserAddress::where('user_id', $id)->where('type',1)->where('status',1)->orderBy('created_at','DESC')->get();
		$data['shipping'] =  UserAddress::where('user_id', $id)->where('type',2)->where('status',1)->orderBy('created_at','DESC')->get();
		return $data;
	}
}
