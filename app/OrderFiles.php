<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class OrderFiles extends Authenticatable
{
    use Notifiable;

	protected $table = 'order_files';
}
