<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Designers extends Authenticatable
{
    use Notifiable;

	protected $table = 'designers';
}
