<?php

declare(strict_types=1);

namespace App\Core\Analytics\Application\Services;

use Illuminate\Support\Collection;

final class AnalyticsReportFileBuilder
{
    /** @param Collection<int, object> $rows */
    public function build(string $format, Collection $rows, string $title): string
    {
        return match ($format) {
            'csv' => $this->csv($rows),
            'excel' => $this->excel($rows, $title),
            'pdf' => $this->pdf($rows, $title),
            default => throw new \InvalidArgumentException('Formato de relatório inválido.'),
        };
    }

    /** @return list<string> */
    public function headers(): array
    {
        return [
            'ID interno', 'Event ID', 'Data/hora', 'Evento', 'Versão do schema', 'Canal',
            'Tipo do objeto', 'ID do objeto', 'Slug do objeto', 'Visitor ID', 'Analytics Session ID',
            'User ID', 'Session ID Laravel', 'URL', 'Página', 'Referrer', 'Origem', 'Mídia', 'Campanha',
            'Contexto de aquisição', 'Palavra-chave de aquisição', 'Identificador da campanha',
            'Ferramenta principal da aquisição', 'UTM Source', 'UTM Medium', 'UTM Campaign', 'UTM Term',
            'UTM Content', 'Dispositivo', 'Navegador', 'Sistema operacional', 'Idioma', 'Fuso horário',
            'Resolução', 'País', 'Estado/região', 'Cidade', 'IP hash', 'User agent', 'Metadados JSON',
        ];
    }

    /** @param Collection<int, object> $rows */
    private function csv(Collection $rows): string
    {
        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, "\xEF\xBB\xBF");
        fputcsv($stream, $this->headers(), ';');
        foreach ($rows as $row) {
            fputcsv($stream, $this->values($row), ';');
        }
        rewind($stream);
        $content = stream_get_contents($stream) ?: '';
        fclose($stream);

        return $content;
    }

    /** @param Collection<int, object> $rows */
    private function excel(Collection $rows, string $title): string
    {
        $xmlRows = '';
        foreach (collect([$this->headers()])->concat($rows->map(fn ($row) => $this->values($row))) as $row) {
            $cells = collect($row)->map(fn ($value) => '<Cell><Data ss:Type="String">'.htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</Data></Cell>')->implode('');
            $xmlRows .= '<Row>'.$cells.'</Row>';
        }

        return '<?xml version="1.0" encoding="UTF-8"?><?mso-application progid="Excel.Sheet"?>'
            .'<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'
            .'<Worksheet ss:Name="'.htmlspecialchars(mb_substr($title, 0, 31), ENT_XML1 | ENT_QUOTES, 'UTF-8').'"><Table>'.$xmlRows.'</Table></Worksheet></Workbook>';
    }

    /** PDF simples e válido, sem dependências externas. @param Collection<int, object> $rows */
    private function pdf(Collection $rows, string $title): string
    {
        $lines = [$title, 'Gerado em '.now()->format('d/m/Y H:i'), str_repeat('-', 100)];
        foreach ($rows->take(250) as $row) {
            $lines[] = implode(' | ', array_slice($this->values($row), 0, 7));
        }
        if ($rows->count() > 250) {
            $lines[] = 'Relatório limitado a 250 registros no PDF. Use CSV ou Excel para a base completa.';
        }

        $pages = array_chunk($lines, 46);
        $objects = [];
        $pageIds = [];
        $nextId = 3;
        foreach ($pages as $pageLines) {
            $pageId = $nextId++;
            $contentId = $nextId++;
            $pageIds[] = $pageId;
            $stream = "BT /F1 9 Tf 36 806 Td 12 TL\n";
            foreach ($pageLines as $line) {
                $safe = $this->pdfText((string) $line);
                $stream .= '('.$safe.") Tj T*\n";
            }
            $stream .= 'ET';
            $objects[$pageId] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 1 0 R >> >> /Contents {$contentId} 0 R >>";
            $objects[$contentId] = '<< /Length '.strlen($stream)." >>\nstream\n{$stream}\nendstream";
        }
        $objects[1] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objects[2] = '<< /Type /Pages /Kids ['.implode(' ', array_map(fn ($id) => "$id 0 R", $pageIds)).'] /Count '.count($pageIds).' >>';
        $catalogId = $nextId;
        $objects[$catalogId] = '<< /Type /Catalog /Pages 2 0 R >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "$id 0 obj\n$body\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 ".($catalogId + 1)."\n0000000000 65535 f \n";
        for ($id = 1; $id <= $catalogId; $id++) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$id] ?? 0)."\n";
        }
        $pdf .= 'trailer << /Size '.($catalogId + 1)." /Root {$catalogId} 0 R >>\nstartxref\n{$xref}\n%%EOF";

        return $pdf;
    }

    /** @return list<string|int> */
    private function values(object $row): array
    {
        return [
            $row->id ?? '', $row->event_id ?? '', $row->occurred_at?->format('d/m/Y H:i:s') ?? '',
            $row->event_name ?? '', $row->schema_version ?? '', $row->channel ?? '',
            $row->subject_type ?? '', $row->subject_id ?? '', $row->subject_slug ?? '',
            $row->visitor_id ?? '', $row->analytics_session_id ?? '', $row->user_id ?? '', $row->session_id ?? '',
            $row->url ?? '', $row->path ?? '', $row->referrer ?? '', $row->source ?? '', $row->medium ?? '',
            $row->campaign ?? '', $row->acquisition_context_id ?? '', $row->acquisition_keyword ?? '',
            $row->acquisition_campaign_identifier ?? '', $row->acquisition_primary_tool_slug ?? '',
            $row->utm_source ?? '', $row->utm_medium ?? '', $row->utm_campaign ?? '', $row->utm_term ?? '',
            $row->utm_content ?? '', $row->device_type ?? '', $row->browser ?? '', $row->operating_system ?? '',
            $row->language ?? '', $row->timezone ?? '', $row->screen_resolution ?? '', $row->country_code ?? '',
            $row->region ?? '', $row->city ?? '', $row->ip_hash ?? '', $row->user_agent ?? '',
            json_encode($row->metadata ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];
    }

    private function pdfText(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
        $value = preg_replace('/[^\x20-\x7E]/', '', $value) ?? '';

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], mb_substr($value, 0, 180));
    }
}
