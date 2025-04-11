<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProxyStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'finished_at',
    ];

    protected $casts = [
        'finished_at' => 'datetime',
    ];

    public function proxies(): HasMany
    {
        return $this->hasMany(Proxy::class);
    }

    protected function totalProxies(): Attribute
    {
        return Attribute::make(
            get: function(): int {
                return $this->proxies->count();
            }
        );
    }

    protected function checkedProxies(): Attribute
    {
        return Attribute::make(
            get: function(): int {
                return $this->proxies->where('status', '!=', ProxyStatusEnum::Pending)->count();
            }
        );
    }

    protected function workingProxies(): Attribute
    {
        return Attribute::make(
            get: function(): int {
                return $this->proxies->where('status', ProxyStatusEnum::Valid)->count();
            }
        );
    }

    protected function duration(): Attribute
    {
        return Attribute::make(
            get: function(): int {
                $endTime = $this->finished_at ?? now();

                return (int)$endTime->diffInSeconds($this->created_at, true);
            }
        );
    }
}
