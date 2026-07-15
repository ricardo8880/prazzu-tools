<?php

namespace App\Http\Requests\Admin\Blog;

use App\Blog\Enums\BlogPostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class SaveBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $postId = $this->route('post')?->getKey();

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('blog_posts', 'slug')->ignore($postId)],
            'excerpt' => ['required', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'category_id' => ['required', 'integer', Rule::exists('blog_categories', 'id')],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'cover_image_alt' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::enum(BlogPostStatus::class)],
            'is_featured' => ['nullable', 'boolean'],
            'published_at' => [Rule::requiredIf($this->input('status') === BlogPostStatus::Scheduled->value), 'nullable', 'date'],
            'content_updated_at' => ['nullable', 'date'],
            'primary_keyword' => ['nullable', 'string', 'max:255'],
            'related_keywords' => ['nullable', 'string', 'max:1000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'canonical_url' => ['nullable', 'url', 'max:2048'],
            'social_image' => ['nullable', 'image', 'max:4096'],
            'should_index' => ['nullable', 'boolean'],
            'related_tools' => ['nullable', 'array'],
            'related_tools.*' => ['string', 'max:120'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'slug.regex' => 'O slug deve conter apenas letras minúsculas, números e hífens.',
            'slug.unique' => 'Já existe uma postagem com este slug.',
            'category_id.required' => 'Selecione uma categoria para a postagem.',
            'category_id.exists' => 'A categoria selecionada não está disponível.',
            'published_at.required' => 'Informe a data e hora para agendar a postagem.',
            'cover_image.image' => 'A imagem de capa deve ser um arquivo de imagem válido.',
            'social_image.image' => 'A imagem social deve ser um arquivo de imagem válido.',
        ];
    }
}
