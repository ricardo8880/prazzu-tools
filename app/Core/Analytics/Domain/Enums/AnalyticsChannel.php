<?php

namespace App\Core\Analytics\Domain\Enums;

enum AnalyticsChannel: string
{
    case Platform = 'platform';
    case Blog = 'blog';
    case Tool = 'tool';
    case Account = 'account';
    case Subscription = 'subscription';
    case System = 'system';
}
