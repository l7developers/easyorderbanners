<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ProductShipping extends Authenticatable
{
    use Notifiable;

	protected $table = 'product_shipping';
}
