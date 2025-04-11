<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProxyStatusEnum;
use App\Enums\ProxyTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperProxy
 */
class Proxy extends Model
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
    ];

    protected function casts(): array
    {
        return [
            'type' => ProxyTypeEnum::class,
            'status' => ProxyStatusEnum::class,
        ];
    }

    public function checkSession(): BelongsTo
    {
        return $this->belongsTo(CheckSession::class);
    }
}
