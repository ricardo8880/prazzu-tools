<?php

namespace App\Core\Tools\Data;

/**
 * @deprecated Use ToolManifest. Mantido apenas como ponte de compatibilidade
 * durante a consolidação arquitetural.
 */
class_alias(ToolManifest::class, __NAMESPACE__.'\\ToolDefinition');
