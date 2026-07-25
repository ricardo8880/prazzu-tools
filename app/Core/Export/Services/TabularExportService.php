<?php

declare(strict_types=1);

namespace App\Core\Export\Services;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class TabularExportService
{
    public function __construct(private readonly SimpleZipArchiveBuilder $zip) {}

    /**
     * @param list<string> $headers
     * @param iterable<int, list<string|int|float|bool|null>> $rows
     */
    public function csv(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            $stream = fopen('php://output', 'wb');
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, $headers, ';');

            foreach ($rows as $row) {
                fputcsv($stream, array_map([$this, 'stringify'], $row), ';');
            }

            fclose($stream);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Exporta SpreadsheetML, formato XML aberto pelo Excel sem dependências adicionais.
     *
     * @param list<string> $headers
     * @param iterable<int, list<string|int|float|bool|null>> $rows
     */
    public function excel(string $filename, array $headers, iterable $rows, string $worksheet = 'Resultados'): Response
    {
        $allRows = [$headers];
        foreach ($rows as $row) {
            $allRows[] = array_map([$this, 'stringify'], $row);
        }

        $xmlRows = '';
        foreach ($allRows as $row) {
            $cells = '';
            foreach ($row as $value) {
                $cells .= '<Cell><Data ss:Type="String">'.htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</Data></Cell>';
            }
            $xmlRows .= '<Row>'.$cells.'</Row>';
        }

        $content = '<?xml version="1.0" encoding="UTF-8"?>'
            .'<?mso-application progid="Excel.Sheet"?>'
            .'<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" '
            .'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'
            .'<Worksheet ss:Name="'.htmlspecialchars($worksheet, ENT_XML1 | ENT_QUOTES, 'UTF-8').'">'
            .'<Table>'.$xmlRows.'</Table></Worksheet></Workbook>';

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Gera uma planilha OOXML real, sem dependências externas.
     *
     * @param list<string> $headers
     * @param iterable<int, list<string|int|float|bool|null>> $rows
     */
    public function xlsx(string $filename, array $headers, iterable $rows, string $worksheet = 'Resultados'): Response
    {
        $sheetRows = [$headers];
        foreach ($rows as $row) {
            $sheetRows[] = array_map([$this, 'stringify'], $row);
        }

        $xmlRows = '';
        foreach ($sheetRows as $rowIndex => $row) {
            $cells = '';
            foreach (array_values($row) as $columnIndex => $value) {
                $reference = $this->columnName($columnIndex + 1).($rowIndex + 1);
                $style = $rowIndex === 0 ? ' s="1"' : '';
                $cells .= '<c r="'.$reference.'" t="inlineStr"'.$style.'><is><t xml:space="preserve">'
                    .$this->xml((string) $value)
                    .'</t></is></c>';
            }
            $xmlRows .= '<row r="'.($rowIndex + 1).'">'.$cells.'</row>';
        }

        $safeWorksheet = mb_substr(trim($worksheet) !== '' ? trim($worksheet) : 'Resultados', 0, 31);
        $archive = $this->zip->build([
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                .'<Default Extension="xml" ContentType="application/xml"/>'
                .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
                .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
                .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
                .'</Types>',
            '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
                .'</Relationships>',
            'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
                .'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                .'<sheets><sheet name="'.$this->xml($safeWorksheet).'" sheetId="1" r:id="rId1"/></sheets></workbook>',
            'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
                .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
                .'</Relationships>',
            'xl/styles.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
                .'<fonts count="2"><font/><font><b/></font></fonts>'
                .'<fills count="1"><fill><patternFill patternType="none"/></fill></fills>'
                .'<borders count="1"><border/></borders>'
                .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
                .'<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
                .'<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/></cellXfs>'
                .'</styleSheet>',
            'xl/worksheets/sheet1.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
                .'<sheetData>'.$xmlRows.'</sheetData></worksheet>',
        ]);

        return response($archive, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Length' => (string) strlen($archive),
        ]);
    }

    private function stringify(string|int|float|bool|null $value): string
    {
        return match (true) {
            $value === null => '',
            is_bool($value) => $value ? 'Sim' : 'Não',
            default => (string) $value,
        };
    }

    private function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function columnName(int $index): string
    {
        $name = '';
        while ($index > 0) {
            $index--;
            $name = chr(65 + ($index % 26)).$name;
            $index = intdiv($index, 26);
        }

        return $name;
    }
}
