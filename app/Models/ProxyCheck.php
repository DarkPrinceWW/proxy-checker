<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperProxyCheck
 */
class ProxyCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_session_id',
        'ip',
        'port',
        'type',
        'country',
        'city',
        'status',
        'response_time',
        'external_ip',
        'error_count',
    ];
}
