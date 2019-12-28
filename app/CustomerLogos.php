<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CustomerLogos extends Authenticatable
{
    use Notifiable;

	protected $table = 'customer_logos';
}
