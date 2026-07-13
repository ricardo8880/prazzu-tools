<?php

declare(strict_types=1);

namespace App\Core\Money;

use App\Core\Exceptions\CoreDomainException;

final class CurrencyMismatch extends CoreDomainException
{
}
