<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Domain\Enums;

enum SmartClause: string
{
    case Confidentiality = 'confidentiality';
    case DataProtection = 'data_protection';
    case IntellectualProperty = 'intellectual_property';
    case LatePayment = 'late_payment';
    case WarrantyAndDelivery = 'warranty_delivery';

    public function label(): string
    {
        return match ($this) {
            self::Confidentiality => 'Confidencialidade',
            self::DataProtection => 'Proteção de dados',
            self::IntellectualProperty => 'Propriedade intelectual',
            self::LatePayment => 'Atraso de pagamento',
            self::WarrantyAndDelivery => 'Garantia e entrega',
        };
    }

    public function text(): string
    {
        return match ($this) {
            self::Confidentiality => 'As partes deverão preservar o caráter confidencial das informações não públicas recebidas em razão deste contrato, utilizando-as somente para sua execução, ressalvadas divulgações exigidas por lei ou autorizadas pela parte titular.',
            self::DataProtection => 'As partes deverão tratar dados pessoais acessados na execução deste contrato apenas para finalidades legítimas relacionadas ao objeto contratado, adotando medidas razoáveis de segurança e observando as obrigações legais aplicáveis.',
            self::IntellectualProperty => 'Materiais, métodos, marcas, conteúdos e ativos intelectuais preexistentes permanecem de titularidade de seus respectivos proprietários. Eventual cessão ou licença sobre entregáveis deverá decorrer de disposição expressa deste instrumento.',
            self::LatePayment => 'O atraso de pagamento sujeita a parte inadimplente aos encargos expressamente previstos entre as partes e admitidos pela legislação aplicável, sem afastar a cobrança do valor principal vencido.',
            self::WarrantyAndDelivery => 'A entrega e a verificação do bem deverão observar as condições descritas neste instrumento. Garantias eventualmente oferecidas devem ser comprovadas e não afastam direitos legalmente inderrogáveis quando aplicáveis.',
        };
    }
}
