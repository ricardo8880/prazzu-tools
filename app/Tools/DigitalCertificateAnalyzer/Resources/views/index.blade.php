@extends('layouts.app')
@section('title', 'Analisador de Certificado Digital A1 — Prazzu Tools')
@section('meta_description', 'Analise um certificado digital A1 .pfx/.p12, confira validade, titular, emissor e dados técnicos sem persistir arquivo ou senha.')
@section('content')
<x-tools.page title="Analisador de Certificado Digital A1" description="Confira rapidamente os principais dados de um A1 e identifique vencimento, titular, emissor e informações técnicas sem transformar o Prazzu em emissor de certificados." icon="shield-lock" slug="analisador-certificado-digital-a1">
    <div class="alert alert-info small" role="note"><strong>Privacidade:</strong> o arquivo e a senha são usados apenas durante esta requisição. Esta ferramenta não possui histórico, não salva a chave privada e não emite certificados ICP-Brasil.</div>

    <form data-testid="tool-form-panel" method="post" enctype="multipart/form-data" action="{{ route('tools.analisador-certificado-digital-a1.calculate') }}" class="d-grid gap-4">
        @csrf
        <x-tools.form-panel title="Certificado A1" description="Envie um arquivo PKCS#12 (.pfx ou .p12) de até 5 MB. A senha é necessária somente para abrir o arquivo nesta análise.">
            <div class="row g-3">
                <div class="col-12 col-lg-7">
                    <label class="form-label" for="certificate_file">Arquivo .pfx ou .p12</label>
                    <input data-testid="field-certificate-file" data-e2e-fixture="app/Tools/DigitalCertificateAnalyzer/Tests/Fixtures/certificate-e2e.p12" class="form-control @error('certificate_file') is-invalid @enderror" id="certificate_file" name="certificate_file" type="file" accept=".pfx,.p12,application/x-pkcs12" required>
                    <div class="form-text">O arquivo pode conter chave privada. Não o compartilhe fora de ambientes confiáveis.</div>
                </div>
                <div class="col-12 col-lg-5">
                    <label class="form-label" for="password">Senha do certificado</label>
                    <input data-testid="field-certificate-password" data-e2e-secret="PRAZZU_E2E_CERTIFICATE_PASSWORD" class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="off" maxlength="512" required>
                    <div class="form-text">A senha não é reapresentada nem persistida.</div>
                </div>
            </div>
        </x-tools.form-panel>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-primary" type="submit">Analisar certificado</button>
            @if($plusEnabled ?? true)
                <button class="btn btn-outline-danger" data-testid="download-pdf" type="submit" formaction="{{ route('tools.analisador-certificado-digital-a1.export') }}">Gerar relatório técnico PDF <span class="badge text-bg-primary ms-1">Plus</span></button>
            @endif
        </div>
    </form>

    @isset($result)
        @php($d = $result->details)
        <div data-testid="tool-result" class="mt-5">
            <x-tools.result-panel title="Diagnóstico do certificado">
                <div class="row g-3 mb-4">
                    @foreach($result->summary as $item)
                        <div class="col-12 col-md-6 col-xl-3"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" icon="shield-check" /></div>
                    @endforeach
                </div>

                @foreach($result->warnings as $warning)<div class="alert alert-light border small">{{ $warning->message }}</div>@endforeach

                <h3 class="h5 mt-4">Identidade e emissão</h3>
                <div class="table-responsive"><table class="table table-sm align-middle"><tbody>
                    <tr><th style="width:32%">Titular (CN)</th><td>{{ $d['holder']['common_name'] ?: 'Não informado' }}</td></tr>
                    <tr><th>Organização</th><td>{{ $d['holder']['organization'] ?: 'Não informado' }}</td></tr>
                    <tr><th>Documento identificado</th><td>{{ $d['holder']['document'] ? (($d['holder']['document_type'] ?? '').' '.$d['holder']['document']) : 'Não identificado nos campos lidos' }}</td></tr>
                    <tr><th>Emissor (CN)</th><td>{{ $d['issuer']['common_name'] ?: ($d['issuer']['organization'] ?: 'Não informado') }}</td></tr>
                    <tr><th>Início da validade</th><td>{{ $d['valid_from'] ? \Illuminate\Support\Carbon::parse($d['valid_from'])->format('d/m/Y H:i:s') : 'Não informado' }}</td></tr>
                    <tr><th>Fim da validade</th><td>{{ $d['valid_to'] ? \Illuminate\Support\Carbon::parse($d['valid_to'])->format('d/m/Y H:i:s') : 'Não informado' }}</td></tr>
                    <tr><th>Dias restantes</th><td>{{ $d['days_remaining'] === null ? 'Indeterminado' : $d['days_remaining'].' dia(s)' }}</td></tr>
                    <tr><th>Certificados adicionais no arquivo</th><td>{{ $d['chain_certificates_in_file'] }}</td></tr>
                </tbody></table></div>

                @if(isset($d['technical']))
                    <section data-plus-feature="technical_report">
                        <h3 class="h5 mt-4">Diagnóstico técnico <span class="badge text-bg-primary">Plus</span></h3>
                        <div class="table-responsive"><table class="table table-sm align-middle"><tbody>
                            <tr><th style="width:32%">Número de série</th><td class="text-break"><code>{{ $d['technical']['serial_number'] ?: 'Não informado' }}</code></td></tr>
                            <tr><th>Algoritmo de assinatura</th><td>{{ $d['technical']['signature_algorithm'] ?: 'Não informado' }}</td></tr>
                            <tr><th>Chave pública</th><td>{{ $d['technical']['public_key_type'] }}{{ $d['technical']['public_key_bits'] > 0 ? ' — '.$d['technical']['public_key_bits'].' bits' : '' }}</td></tr>
                            <tr><th>Fingerprint SHA-256</th><td class="text-break"><code>{{ $d['technical']['sha256_fingerprint'] }}</code></td></tr>
                            <tr><th>Subject Alternative Name</th><td class="text-break">{{ $d['technical']['subject_alt_name'] ?: 'Não informado' }}</td></tr>
                        </tbody></table></div>
                    </section>
                @endif
            </x-tools.result-panel>
        </div>
    @endisset
</x-tools.page>
@endsection
