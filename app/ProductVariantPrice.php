<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ProductVariantPrice extends Authenticatable
{
    use Notifiable;

	protected $table = 'product_variant_price';
}
