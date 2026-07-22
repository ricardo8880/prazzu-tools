<?php

namespace App\Http\Requests\Admin\Acquisition;

use App\Core\Acquisition\Domain\Enums\AcquisitionContextStatus;
use App\Core\Tools\ToolCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class SaveAcquisitionContextRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $contextId = $this->route('context');
        $toolSlugs = app(ToolCatalog::class)->all(false)->pluck('slug')->all();

        return [
            'name' => ['required', 'string', 'max:255'],
            'keyword' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('acquisition_contexts', 'keyword')->ignore($contextId),
            ],
            'campaign_identifier' => ['nullable', 'string', 'max:255'],
            'campaign_source' => ['nullable', 'string', 'max:120'],
            'campaign_medium' => ['nullable', 'string', 'max:120'],
            'content_identifier' => ['nullable', 'string', 'max:255'],
            'video_identifier' => ['nullable', 'string', 'max:255'],
            'banner_identifier' => ['nullable', 'string', 'max:255'],
            'cta_identifier' => ['nullable', 'string', 'max:255'],
            'monthly_investment' => ['nullable', 'regex:/^\d{1,10}(?:[.,]\d{1,2})?$/'],
            'investment_currency' => ['required_with:monthly_investment', 'string', 'size:3', 'in:BRL'],
            'status' => ['required', Rule::enum(AcquisitionContextStatus::class)],
            'hero_title_before' => ['nullable', 'string', 'max:255'],
            'hero_title_line' => ['nullable', 'string', 'max:255'],
            'hero_title_highlight' => ['nullable', 'string', 'max:255'],
            'hero_description' => ['nullable', 'string', 'max:3000'],
            'hero_search_placeholder' => ['nullable', 'string', 'max:255'],
            'tools_section_title' => ['nullable', 'string', 'max:255'],
            'cta_title' => ['nullable', 'string', 'max:255'],
            'cta_description' => ['nullable', 'string', 'max:3000'],
            'cta_label' => ['nullable', 'string', 'max:255'],
            'cta_url' => ['nullable', 'url', 'max:2048'],
            'cta_tool_slug' => ['nullable', 'string', Rule::in($toolSlugs)],
            'contextual_message' => ['nullable', 'string', 'max:255'],
            'contextual_continue_label' => ['nullable', 'string', 'max:80'],
            'contextual_continue_url' => ['nullable', 'url', 'max:2048'],
            'contextual_continue_tool_slug' => ['nullable', 'string', Rule::in($toolSlugs)],
            'primary_tool_slug' => ['nullable', 'string', Rule::in($toolSlugs)],
            'featured_tools' => ['nullable', 'array'],
            'featured_tools.*' => ['string', 'distinct', Rule::in($toolSlugs)],
            'recommended_tools' => ['nullable', 'array'],
            'recommended_tools.*' => ['string', 'distinct', Rule::in($toolSlugs)],
            'articles' => ['nullable', 'array'],
            'articles.*' => ['string', 'distinct', 'exists:blog_posts,slug'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'keyword.regex' => 'A palavra-chave deve conter apenas letras minúsculas, números e hífens.',
            'keyword.unique' => 'Já existe um contexto com esta palavra-chave.',
            'monthly_investment.regex' => 'Informe o investimento mensal no formato 1500,00.',
            'investment_currency.in' => 'A moeda suportada neste momento é BRL.',
            'cta_url.url' => 'Informe uma URL completa e válida para o CTA.',
            'contextual_continue_url.url' => 'Informe uma URL completa e válida para a continuação contextual.',
            'featured_tools.*.distinct' => 'Uma ferramenta não pode aparecer duas vezes nos destaques.',
            'recommended_tools.*.distinct' => 'Uma ferramenta não pode aparecer duas vezes nas recomendações.',
            'articles.*.exists' => 'Um dos artigos selecionados não está mais disponível.',
        ];
    }
}
