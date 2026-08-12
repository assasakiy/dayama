<?php

declare(strict_types=1);

namespace Modules\System\Models;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    protected $table = 'system_notifications';
}
