<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class OrderAddress extends Authenticatable
{
    use Notifiable;

	protected $table = 'order_address';
}
