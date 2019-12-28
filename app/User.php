<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use DB;
use App\Http\Controllers\Controller;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
	
	/**
	 * Send the password reset notification.
	 *
	 * @param  string  $token
	 * @return void
	 */
	public function sendPasswordResetNotification($token)
	{
		$params = array(
						'slug'=>'forgot-password-link',
						'to'=>$this->email,
						'params'=>array(
									'{{name}}'=>$this->fname.' '.$this->lname,
									'{{link}}'=>url('/password/reset/'.$token),
									'{{ADMIN_NAME}}'=>config('constants.ADMIN_NAME'),
									'{{ADMIN_MAIL}}'=>config('constants.ADMIN_MAIL'),
									'{{SITE_NAME}}'=>config('constants.SITE_NAME'),
									)
						);
		
		$controller = new Controller;
		$controller->sendMail($params);
		
		return true;
	}
	
	public function user_add() {
		return $this->hasMany('\App\UserAddress','user_id')->where('status',1);
	}
	
	public function billing_add() {
		return $this->hasOne('\App\UserAddress','user_id')->where('type',1)->where('status',1);
	}
	
	public function shipping_add() {
		return $this->hasMany('\App\UserAddress','user_id')->where('type',2)->where('status',1);
	}
	
}
