<?php

namespace App\Core\Tools\Enums;

enum ToolCategory: string
{
    case Generators = 'geradores';
    case Calculators = 'calculadoras';
    case Converters = 'conversores';
    case Validators = 'validadores';
    case Documents = 'documentos';
    case Fiscal = 'fiscal';
    case Labor = 'trabalhista';
    case Corporate = 'societario';
    case Other = 'outros';
}
