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
            self::Confidentiality => 'Confidentiality',
            self::DataProtection => 'Privacy and data handling',
            self::IntellectualProperty => 'Intellectual property',
            self::LatePayment => 'Late payment',
            self::WarrantyAndDelivery => 'Warranty and delivery',
        };
    }

    public function text(): string
    {
        return match ($this) {
            self::Confidentiality => 'Each party will protect nonpublic information received in connection with this agreement and will use it only for the purposes of the agreement, except when disclosure is authorized or required by law.',
            self::DataProtection => 'Each party will handle personal information only for legitimate purposes related to this agreement, use reasonable security safeguards, and comply with privacy laws that apply to that party and the transaction.',
            self::IntellectualProperty => 'Pre-existing materials, methods, trademarks, content, and other intellectual property remain the property of their respective owners. Any assignment or license covering deliverables must be expressly stated in this agreement.',
            self::LatePayment => 'Past-due amounts may be subject only to late fees or interest expressly agreed by the parties and permitted by applicable law, without waiving the underlying payment obligation.',
            self::WarrantyAndDelivery => 'Delivery, inspection, and any agreed warranty will follow the terms stated in this agreement. Nothing in this clause waives nonwaivable rights or remedies under applicable law.',
        };
    }
}
