<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Domain\Services;

use App\Core\Money\AmericanMoneyInWords;
use App\Tools\ContractGenerator\Domain\Data\ContractDraft;
use App\Tools\ContractGenerator\Domain\Data\ContractParty;
use App\Tools\ContractGenerator\Domain\Data\ContractText;
use App\Tools\ContractGenerator\Domain\Enums\ContractType;
use DateTimeImmutable;

final readonly class ContractTextGenerator
{
    public function __construct(private AmericanMoneyInWords $moneyInWords) {}

    public function generate(ContractDraft $draft): ContractText
    {
        return match ($draft->type) {
            ContractType::ServiceAgreement => $this->serviceAgreement($draft),
            ContractType::GoodsSaleAgreement => $this->goodsSaleAgreement($draft),
        };
    }

    private function serviceAgreement(ContractDraft $draft): ContractText
    {
        $title = 'SERVICE AGREEMENT';
        $start = $this->dateUs((string) $draft->specificTerms['start_date']);
        $end = $draft->specificTerms['end_date'] !== null ? $this->dateUs((string) $draft->specificTerms['end_date']) : null;
        $noticeDays = (int) $draft->specificTerms['termination_notice_days'];
        $term = $end !== null
            ? "This Agreement begins on {$start} and continues through {$end}, unless ended earlier under this Agreement."
            : "This Agreement begins on {$start} and continues until terminated under this Agreement.";
        $termination = $noticeDays === 0
            ? 'Either party may terminate this Agreement by written notice to the other party, subject to obligations already accrued.'
            : "Either party may terminate this Agreement by giving {$noticeDays} days' written notice to the other party, subject to obligations already accrued.";

        $sections = [
            $title, '',
            $this->qualification('CLIENT', $draft->firstParty), '',
            $this->qualification('SERVICE PROVIDER', $draft->secondParty), '',
            'The parties enter into this Service Agreement on the terms below.', '',
            '1. SERVICES',
            (string) $draft->specificTerms['service_description'], '',
            '2. COMPENSATION AND PAYMENT',
            sprintf('The Client will pay the Service Provider %s (%s).', $draft->amount->formatEnUs(), $this->moneyInWords->convert($draft->amount)),
            'Payment terms: '.$draft->paymentTerms, '',
            '3. TERM', $term, '',
            '4. RESPONSIBILITIES',
            'The Service Provider will perform the services with reasonable care and in accordance with the agreed scope.',
            'The Client will provide reasonably necessary information, access, and timely payment under the agreed terms.', '',
            '5. TERMINATION', $termination,
            'Termination does not eliminate payment or other obligations that accrued before termination.', '',
            '6. GENERAL TERMS',
            'Changes to this Agreement should be made in a written record accepted by both parties.',
            'This template is a general drafting aid and is not legal advice. State-specific requirements may apply.',
        ];

        $sections = array_merge($sections, $this->smartClauseSections($draft));
        if ($draft->additionalTerms !== null) { $sections[] = 'Additional terms: '.$draft->additionalTerms; }
        $sections = array_merge($sections, $this->closingSections($draft));

        return new ContractText($title, implode("\n", $sections));
    }

    private function goodsSaleAgreement(ContractDraft $draft): ContractText
    {
        $title = 'GOODS SALE AGREEMENT';
        $sections = [
            $title, '',
            $this->qualification('SELLER', $draft->firstParty), '',
            $this->qualification('BUYER', $draft->secondParty), '',
            'The parties enter into this Goods Sale Agreement on the terms below.', '',
            '1. GOODS',
            (string) $draft->specificTerms['asset_description'], '',
            '2. PRICE AND PAYMENT',
            sprintf('The purchase price is %s (%s).', $draft->amount->formatEnUs(), $this->moneyInWords->convert($draft->amount)),
            'Payment terms: '.$draft->paymentTerms, '',
            '3. DELIVERY',
            sprintf('Delivery is scheduled for %s at %s.', $this->dateUs((string) $draft->specificTerms['delivery_date']), (string) $draft->specificTerms['delivery_location']), '',
            '4. RESPONSIBILITIES',
            'The Seller will deliver the goods as described and according to the agreed delivery terms.',
            'The Buyer will accept conforming delivery and make payment according to this Agreement.', '',
            '5. GENERAL TERMS',
            'Changes to this Agreement should be made in a written record accepted by both parties.',
            'This template is a general drafting aid and is not legal advice. State-specific requirements, including UCC rules, may apply.',
        ];
        $sections = array_merge($sections, $this->smartClauseSections($draft));
        if ($draft->additionalTerms !== null) { $sections[] = 'Additional terms: '.$draft->additionalTerms; }
        $sections = array_merge($sections, $this->closingSections($draft));

        return new ContractText($title, implode("\n", $sections));
    }

    /** @return list<string> */
    private function smartClauseSections(ContractDraft $draft): array
    {
        if ($draft->smartClauses === []) { return []; }
        $sections = ['', 'SELECTED OPTIONAL CLAUSES'];
        foreach ($draft->smartClauses as $clause) {
            $sections[] = strtoupper($clause->label());
            $sections[] = $clause->text();
            $sections[] = '';
        }
        return $sections;
    }

    /** @return list<string> */
    private function closingSections(ContractDraft $draft): array
    {
        return [
            '', 'GOVERNING LAW AND VENUE',
            sprintf('This Agreement is governed by the laws of the State of %s, without regard to conflict-of-law rules. Subject to applicable law, the parties select courts located in %s, %s for disputes arising from this Agreement.', $draft->jurisdictionState, $draft->jurisdictionCity, $draft->jurisdictionState),
            '', sprintf('Agreed and signed in %s on %s.', $draft->signingCity, $this->dateUs($draft->signingDate)), '',
            '________________________________________', $draft->firstParty->name, $draft->type->firstPartyLabel(), '',
            '________________________________________', $draft->secondParty->name, $draft->type->secondPartyLabel(), '',
            'OPTIONAL WITNESSES', '',
            '1. ____________________________________', '',
            '2. ____________________________________',
        ];
    }

    private function qualification(string $role, ContractParty $party): string
    {
        return sprintf('%s: %s, %s %s, with an address at %s, %s, %s.', $role, $party->name, $party->documentType->label(), $party->document, $party->address, $party->city, $party->state);
    }

    private function dateUs(DateTimeImmutable|string $date): string
    {
        $value = $date instanceof DateTimeImmutable ? $date : new DateTimeImmutable($date);
        return $value->format('F j, Y');
    }
}
