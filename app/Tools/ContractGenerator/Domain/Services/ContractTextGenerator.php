<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Domain\Services;

use App\Core\Money\BrazilianMoneyInWords;
use App\Tools\ContractGenerator\Domain\Data\ContractDraft;
use App\Tools\ContractGenerator\Domain\Data\ContractParty;
use App\Tools\ContractGenerator\Domain\Data\ContractText;
use App\Tools\ContractGenerator\Domain\Enums\ContractType;
use DateTimeImmutable;

final readonly class ContractTextGenerator
{
    public function __construct(private BrazilianMoneyInWords $moneyInWords) {}

    public function generate(ContractDraft $draft): ContractText
    {
        return match ($draft->type) {
            ContractType::ServiceProvision => $this->serviceProvision($draft),
            ContractType::MovableAssetSale => $this->movableAssetSale($draft),
        };
    }

    private function serviceProvision(ContractDraft $draft): ContractText
    {
        $title = 'CONTRATO PARTICULAR DE PRESTAÇÃO DE SERVIÇOS';
        $startDate = $this->datePtBr((string) $draft->specificTerms['start_date']);
        $endDate = $draft->specificTerms['end_date'] !== null
            ? $this->datePtBr((string) $draft->specificTerms['end_date'])
            : null;
        $noticeDays = (int) $draft->specificTerms['termination_notice_days'];
        $duration = $endDate !== null
            ? "O presente contrato vigorará de {$startDate} até {$endDate}, salvo encerramento antecipado na forma deste instrumento."
            : "O presente contrato vigorará por prazo indeterminado a partir de {$startDate}, salvo encerramento na forma deste instrumento.";
        $notice = $noticeDays === 0
            ? 'O contrato poderá ser encerrado por qualquer das partes mediante comunicação à outra parte, sem prejuízo das obrigações já vencidas.'
            : "O contrato poderá ser encerrado por qualquer das partes mediante aviso prévio escrito de {$noticeDays} dias, sem prejuízo das obrigações já vencidas.";

        $sections = [
            $title,
            '',
            $this->qualification('CONTRATANTE', $draft->firstParty),
            '',
            $this->qualification('CONTRATADO', $draft->secondParty),
            '',
            'As partes acima identificadas resolvem celebrar o presente contrato particular de prestação de serviços, mediante as cláusulas e condições seguintes.',
            '',
            'CLÁUSULA 1ª — DO OBJETO',
            'O CONTRATADO prestará ao CONTRATANTE os seguintes serviços:',
            (string) $draft->specificTerms['service_description'],
            '',
            'CLÁUSULA 2ª — DA REMUNERAÇÃO E DO PAGAMENTO',
            sprintf(
                'Pelos serviços contratados, o CONTRATANTE pagará ao CONTRATADO o valor de %s (%s).',
                $draft->amount->formatPtBr(),
                $this->moneyInWords->convert($draft->amount),
            ),
            'Condições de pagamento: '.$draft->paymentTerms,
            '',
            'CLÁUSULA 3ª — DO PRAZO',
            $duration,
            '',
            'CLÁUSULA 4ª — DAS OBRIGAÇÕES DAS PARTES',
            'O CONTRATADO compromete-se a executar os serviços descritos neste instrumento com diligência e de acordo com as condições ajustadas entre as partes.',
            'O CONTRATANTE compromete-se a fornecer as informações e condições necessárias à execução dos serviços e a efetuar os pagamentos nos termos acordados.',
            '',
            'CLÁUSULA 5ª — DO ENCERRAMENTO',
            $notice,
            'O encerramento não afasta responsabilidades ou pagamentos relativos a obrigações constituídas durante a vigência do contrato.',
            '',
            'CLÁUSULA 6ª — DAS DISPOSIÇÕES GERAIS',
            'Alterações deste contrato deverão ser acordadas entre as partes por meio que permita comprovar seu conteúdo.',
        ];

        if ($draft->additionalTerms !== null) {
            $sections[] = 'Condições adicionais: '.$draft->additionalTerms;
        }

        $sections = array_merge($sections, $this->closingSections($draft));

        return new ContractText($title, implode("\n", $sections));
    }

    private function movableAssetSale(ContractDraft $draft): ContractText
    {
        $title = 'CONTRATO PARTICULAR DE COMPRA E VENDA DE BEM MÓVEL';

        $sections = [
            $title,
            '',
            $this->qualification('VENDEDOR', $draft->firstParty),
            '',
            $this->qualification('COMPRADOR', $draft->secondParty),
            '',
            'As partes acima identificadas resolvem celebrar o presente contrato particular de compra e venda de bem móvel, mediante as cláusulas e condições seguintes.',
            '',
            'CLÁUSULA 1ª — DO OBJETO',
            'O VENDEDOR vende ao COMPRADOR o seguinte bem móvel:',
            (string) $draft->specificTerms['asset_description'],
            '',
            'CLÁUSULA 2ª — DO PREÇO E DO PAGAMENTO',
            sprintf(
                'O preço ajustado para a presente compra e venda é de %s (%s).',
                $draft->amount->formatPtBr(),
                $this->moneyInWords->convert($draft->amount),
            ),
            'Condições de pagamento: '.$draft->paymentTerms,
            '',
            'CLÁUSULA 3ª — DA ENTREGA',
            sprintf(
                'A entrega do bem ocorrerá em %s, no seguinte local: %s.',
                $this->datePtBr((string) $draft->specificTerms['delivery_date']),
                (string) $draft->specificTerms['delivery_location'],
            ),
            '',
            'CLÁUSULA 4ª — DAS OBRIGAÇÕES DAS PARTES',
            'O VENDEDOR compromete-se a entregar o bem descrito neste instrumento nas condições acordadas.',
            'O COMPRADOR compromete-se a receber o bem e a efetuar o pagamento conforme as condições estabelecidas neste contrato.',
            '',
            'CLÁUSULA 5ª — DAS DISPOSIÇÕES GERAIS',
            'Qualquer alteração das condições desta compra e venda deverá ser acordada entre as partes por meio que permita comprovar seu conteúdo.',
        ];

        if ($draft->additionalTerms !== null) {
            $sections[] = 'Condições adicionais: '.$draft->additionalTerms;
        }

        $sections = array_merge($sections, $this->closingSections($draft));

        return new ContractText($title, implode("\n", $sections));
    }

    /** @return list<string> */
    private function closingSections(ContractDraft $draft): array
    {
        return [
            '',
            'CLÁUSULA FINAL — DO FORO',
            sprintf(
                'Fica eleito o foro da comarca de %s/%s para dirimir questões decorrentes deste instrumento, observadas as regras legais aplicáveis.',
                $draft->jurisdictionCity,
                $draft->jurisdictionState,
            ),
            '',
            sprintf(
                'E, por estarem de acordo, as partes firmam o presente instrumento em %s, %s.',
                $draft->signingCity,
                $this->datePtBr($draft->signingDate),
            ),
            '',
            '________________________________________',
            $draft->firstParty->name,
            $draft->type->firstPartyLabel(),
            '',
            '________________________________________',
            $draft->secondParty->name,
            $draft->type->secondPartyLabel(),
            '',
            'TESTEMUNHAS',
            '',
            '1. ____________________________________    CPF: ______________________________',
            '',
            '2. ____________________________________    CPF: ______________________________',
        ];
    }

    private function qualification(string $role, ContractParty $party): string
    {
        return sprintf(
            '%s: %s, inscrito(a) no %s sob o nº %s, com endereço em %s, %s/%s.',
            $role,
            $party->name,
            $party->documentType->label(),
            $party->document,
            $party->address,
            $party->city,
            $party->state,
        );
    }

    private function datePtBr(DateTimeImmutable|string $date): string
    {
        $value = $date instanceof DateTimeImmutable ? $date : new DateTimeImmutable($date);
        $months = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril', 5 => 'maio', 6 => 'junho',
            7 => 'julho', 8 => 'agosto', 9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro',
        ];

        return sprintf('%d de %s de %s', (int) $value->format('j'), $months[(int) $value->format('n')], $value->format('Y'));
    }
}
