<?php

declare(strict_types=1);

namespace App\Tools\TaxReformSimulator\Application\Actions; use App\Core\Tools\Data\ToolManifest; use App\Tools\TaxReformSimulator\Tool; final readonly class ShowToolPage { public function __construct(private Tool $tool){} public function execute():array{return ['tool'=>$this->tool->manifest()];} }
