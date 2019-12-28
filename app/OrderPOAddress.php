<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

class OrderPOAddress extends Authenticatable
{
    use Notifiable;

	protected $table = 'order_po_address';

    public $timestamps = false;

}
