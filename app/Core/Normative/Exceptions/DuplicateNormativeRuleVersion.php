<?php

declare(strict_types=1);

namespace App\Core\Normative\Exceptions;

use App\Core\Exceptions\CoreDomainException;

final class DuplicateNormativeRuleVersion extends CoreDomainException {}
