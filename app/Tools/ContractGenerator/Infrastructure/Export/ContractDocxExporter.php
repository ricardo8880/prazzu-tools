<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Infrastructure\Export;

use App\Core\Export\Services\SimpleZipArchiveBuilder;
use Illuminate\Http\Response;

final class ContractDocxExporter
{
    public function __construct(
        private readonly SimpleZipArchiveBuilder $zip,
    ) {}

    public function download(string $title, string $content): Response
    {
        $filename = $this->filename($title).'.docx';
        $document = $this->build($title, $content);

        return response($document, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Length' => (string) strlen($document),
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }

    public function build(string $title, string $content): string
    {
        return $this->zip->build($this->package($title, $content));
    }

    /** @return array<string, string> */
    private function package(string $title, string $content): array
    {
        return [
            '[Content_Types].xml' => <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
  <Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>
  <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
  <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>
XML,
            '_rels/.rels' => <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>
XML,
            'word/_rels/document.xml.rels' => <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>
XML,
            'word/styles.xml' => $this->stylesXml(),
            'word/document.xml' => $this->documentXml($content),
            'docProps/core.xml' => $this->corePropertiesXml($title),
            'docProps/app.xml' => <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
  <Application>Prazzu Tools</Application>
</Properties>
XML,
        ];
    }

    private function documentXml(string $content): string
    {
        $lines = preg_split('/\R/u', str_replace("\r\n", "\n", trim($content))) ?: [];
        $paragraphs = [];
        $firstTextParagraph = true;

        foreach ($lines as $line) {
            $line = rtrim($line);

            if ($line === '') {
                $paragraphs[] = '<w:p/>';
                continue;
            }

            $text = $this->xml($line);
            $paragraphStyle = '';
            $runProperties = '';

            if ($firstTextParagraph) {
                $paragraphStyle = '<w:pPr><w:jc w:val="center"/><w:spacing w:after="240"/></w:pPr>';
                $runProperties = '<w:rPr><w:b/><w:sz w:val="28"/></w:rPr>';
                $firstTextParagraph = false;
            } else {
                $paragraphStyle = '<w:pPr><w:jc w:val="both"/><w:spacing w:after="120" w:line="360" w:lineRule="auto"/></w:pPr>';
            }

            $paragraphs[] = '<w:p>'.$paragraphStyle.'<w:r>'.$runProperties.'<w:t xml:space="preserve">'.$text.'</w:t></w:r></w:p>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            .'<w:body>'.implode('', $paragraphs)
            .'<w:sectPr><w:pgSz w:w="11906" w:h="16838"/><w:pgMar w:top="1134" w:right="1134" w:bottom="1134" w:left="1134" w:header="708" w:footer="708" w:gutter="0"/></w:sectPr>'
            .'</w:body></w:document>';
    }

    private function stylesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:style w:type="paragraph" w:default="1" w:styleId="Normal">
    <w:name w:val="Normal"/>
    <w:qFormat/>
    <w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr>
  </w:style>
</w:styles>
XML;
    }

    private function corePropertiesXml(string $title): string
    {
        $title = $this->xml($title);
        $createdAt = gmdate('Y-m-d\TH:i:s\Z');

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            .'<dc:title>'.$title.'</dc:title><dc:creator>Prazzu Tools</dc:creator>'
            .'<dcterms:created xsi:type="dcterms:W3CDTF">'.$createdAt.'</dcterms:created>'
            .'</cp:coreProperties>';
    }

    private function filename(string $title): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title);
        $ascii = $ascii === false ? $title : $ascii;
        $slug = strtolower(trim((string) preg_replace('/[^A-Za-z0-9]+/', '-', $ascii), '-'));

        return $slug !== '' ? $slug : 'contrato';
    }

    private function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
