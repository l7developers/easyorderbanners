<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class UploadQueue
 * @package App
 */
class UploadQueue extends Authenticatable
{
    use Notifiable;

	protected $table = 'upload_queue';
}
