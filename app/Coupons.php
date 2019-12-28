<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Coupons extends Authenticatable
{
    use Notifiable;

	protected $table = 'coupons';
}
