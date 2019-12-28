<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ProductPrice extends Authenticatable
{
    use Notifiable;

	protected $table = 'product_price';
}
