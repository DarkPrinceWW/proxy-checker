<?php

declare(strict_types=1);

namespace App\Enums;

enum ProxyTypeEnum: string
{
    case Http = 'http';
    case Socks4 = 'socks4';
    case Socks5 = 'socks5';
}
