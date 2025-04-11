<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperCheckSession
 */
class CheckSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_proxies',
        'working_proxies',
        'duration',
    ];

    public function proxyChecks(): HasMany
    {
        return $this->hasMany(ProxyCheck::class);
    }
}
