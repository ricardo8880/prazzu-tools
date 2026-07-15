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
        return $this->render('recursos');
    }

    public function resource(string $resource): View
    {
        return $this->render("recursos.{$resource}");
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
