<?php

use App\Http\Controllers\Platform\ContentPageController;
use App\Http\Controllers\Platform\HomeController;
use App\Http\Controllers\Platform\NewsletterController;
use App\Http\Controllers\Platform\SuggestToolController;
use App\Http\Controllers\Platform\ToolCatalogController;
use App\Http\Controllers\Platform\ToolPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/ferramentas', [ToolCatalogController::class, 'index'])->name('tools.index');
Route::get('/ferramentas/{category}', [ToolCatalogController::class, 'index'])
    ->whereIn('category', array_keys(config('tools.categories', [])))
    ->name('tools.category');
require __DIR__.'/tools.php';

Route::get('/ferramentas/{tool}', [ToolPageController::class, 'show'])->name('tools.show');

Route::get('/blog', [ContentPageController::class, 'blog'])->name('blog.index');
Route::get('/blog/{article}', [ContentPageController::class, 'article'])->name('blog.show');
Route::get('/planos', [ContentPageController::class, 'plans'])->name('plans');
Route::get('/recursos', [ContentPageController::class, 'resources'])->name('resources.index');
Route::get('/recursos/{resource}', [ContentPageController::class, 'resource'])
    ->whereIn('resource', ['guias', 'modelos', 'novidades'])
    ->name('resources.show');
Route::get('/sobre', [ContentPageController::class, 'about'])->name('about');
Route::get('/entrar', [ContentPageController::class, 'login'])->name('login.placeholder');
Route::get('/criar-conta', [ContentPageController::class, 'register'])->name('register.placeholder');
Route::get('/prazzu', [ContentPageController::class, 'prazzu'])->name('prazzu');

Route::get('/sugerir-ferramenta', [SuggestToolController::class, 'create'])->name('tools.suggest');
Route::post('/sugerir-ferramenta', [SuggestToolController::class, 'store'])->name('tools.suggest.store');
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');
