<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ContentPageController extends Controller
{
    public function plans(): View
    {
        return view('pages.plans');
    }

    public function resources(): View
    {
        $sections = config('resources.sections', []);
        $items = collect(config('resources.items', []));

        return view('pages.resources.index', compact('sections', 'items'));
    }

    public function resource(string $resource): View
    {
        $section = config("resources.sections.{$resource}");

        if (! is_array($section)) {
            throw new NotFoundHttpException('Página não encontrada.');
        }

        $items = collect(config('resources.items', []))
            ->where('type', $resource)
            ->values();

        return view('pages.resources.listing', compact('resource', 'section', 'items'));
    }

    public function resourceItem(string $resource, string $slug): View
    {
        $section = config("resources.sections.{$resource}");
        $item = collect(config('resources.items', []))
            ->first(fn (array $candidate): bool => $candidate['type'] === $resource && $candidate['slug'] === $slug);

        if (! is_array($section) || ! is_array($item) || $item['status'] !== 'published' || empty($item['view'])) {
            throw new NotFoundHttpException('Recurso não encontrado.');
        }

        $relatedSlugs = $item['related_slugs'] ?? [];
        $relatedItems = collect(config('resources.items', []))
            ->filter(fn (array $candidate): bool => in_array($candidate['slug'], $relatedSlugs, true))
            ->where('status', 'published')
            ->values();

        return view($item['view'], compact('section', 'item', 'relatedItems'));
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function login(): View
    {
        return $this->render('entrar');
    }

    public function register(): View
    {
        return $this->render('criar-conta');
    }

    public function prazzu(): View
    {
        return $this->render('prazzu');
    }

    private function render(string $key): View
    {
        $pages = config('platform.pages', []);
        $content = $pages[$key] ?? data_get($pages, $key);

        if (! is_array($content) || ! isset($content['title'], $content['description'], $content['icon'], $content['eyebrow'])) {
            throw new NotFoundHttpException('Página não encontrada.');
        }

        return view('pages.content', ['page' => $content]);
    }
}
