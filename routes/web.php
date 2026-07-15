<?php

use App\Http\Controllers\Admin\Analytics\AcquisitionAnalyticsController;
use App\Http\Controllers\Admin\Analytics\AnalyticsDashboardController;
use App\Http\Controllers\Admin\Analytics\AnalyticsReportController;
use App\Http\Controllers\Admin\Analytics\AudienceAnalyticsController;
use App\Http\Controllers\Admin\Analytics\FunnelAnalyticsController;
use App\Http\Controllers\Admin\Analytics\InsightsAnalyticsController;
use App\Http\Controllers\Admin\Analytics\RealtimeAnalyticsController;
use App\Http\Controllers\Admin\Analytics\SeoAnalyticsController;
use App\Http\Controllers\Admin\Analytics\SeoPostAnalyticsController;
use App\Http\Controllers\Admin\Analytics\ToolAnalyticsController;
use App\Http\Controllers\Admin\Analytics\ToolDetailAnalyticsController;
use App\Http\Controllers\Analytics\TrackToolEventController;
use App\Http\Controllers\Analytics\CaptureAudienceContextController;
use App\Http\Controllers\Admin\Blog\BlogAnalyticsController as AdminBlogAnalyticsController;
use App\Http\Controllers\Admin\Blog\BlogPostAnalyticsController;
use App\Http\Controllers\Admin\Blog\BlogCategoryController;
use App\Http\Controllers\Admin\Blog\BlogPostController;
use App\Http\Controllers\Blog\BlogAnalyticsController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Platform\ContentPageController;
use App\Http\Controllers\Platform\HomeController;
use App\Http\Controllers\Platform\NewsletterController;
use App\Http\Controllers\Platform\SuggestToolController;
use App\Http\Controllers\Platform\ToolCatalogController;
use App\Http\Controllers\Platform\ToolPageController;
use App\Http\Controllers\Seo\BlogSitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/ferramentas', [ToolCatalogController::class, 'index'])->name('tools.index');

Route::get('/ferramentas/{category}', [ToolCatalogController::class, 'index'])
    ->whereIn('category', array_keys(config('tools.categories', [])))
    ->name('tools.category');

require __DIR__.'/tools.php';

Route::get('/ferramentas/{tool}', [ToolPageController::class, 'show'])
    ->name('tools.show');

Route::prefix('admin/analytics')
    ->name('admin.analytics.')
    ->middleware('internal.admin')
    ->group(function (): void {
        Route::get('/', AnalyticsDashboardController::class)->name('index');
        Route::get('/acquisition', AcquisitionAnalyticsController::class)->name('acquisition');
        Route::get('/audience', AudienceAnalyticsController::class)->name('audience');
        Route::get('/funnels', [FunnelAnalyticsController::class, 'index'])->name('funnels');
        Route::post('/funnels', [FunnelAnalyticsController::class, 'store'])->name('funnels.store');
        Route::delete('/funnels/{funnel}', [FunnelAnalyticsController::class, 'destroy'])->name('funnels.destroy');
        Route::get('/insights', [InsightsAnalyticsController::class, 'index'])->name('insights');
        Route::post('/insights/generate', [InsightsAnalyticsController::class, 'generate'])->name('insights.generate');
        Route::patch('/insights/{insight}/status', [InsightsAnalyticsController::class, 'status'])->name('insights.status');
        Route::get('/realtime', [RealtimeAnalyticsController::class, 'index'])->name('realtime');
        Route::get('/realtime/data', [RealtimeAnalyticsController::class, 'data'])->name('realtime.data');
        Route::get('/reports', [AnalyticsReportController::class, 'index'])->name('reports');
        Route::get('/reports/export', [AnalyticsReportController::class, 'export'])->name('reports.export');
        Route::post('/reports/schedules', [AnalyticsReportController::class, 'storeSchedule'])->name('reports.schedules.store');
        Route::patch('/reports/schedules/{schedule}/toggle', [AnalyticsReportController::class, 'toggleSchedule'])->name('reports.schedules.toggle');
        Route::delete('/reports/schedules/{schedule}', [AnalyticsReportController::class, 'destroySchedule'])->name('reports.schedules.destroy');
        Route::get('/reports/schedules/{schedule}/download', [AnalyticsReportController::class, 'downloadSchedule'])->name('reports.schedules.download');
        Route::get('/seo', SeoAnalyticsController::class)->name('seo');
        Route::get('/tools', ToolAnalyticsController::class)->name('tools');
        Route::get('/tools/{tool}', ToolDetailAnalyticsController::class)->name('tools.show');
        Route::get('/seo/posts/{post}', [SeoPostAnalyticsController::class, 'show'])->name('seo.posts.show');
        Route::post('/seo/posts/{post}/metrics', [SeoPostAnalyticsController::class, 'store'])->name('seo.posts.metrics.store');
    });

Route::prefix('admin/blog')
    ->name('admin.blog.')
    ->middleware('internal.admin')
    ->group(function (): void {
        Route::get('/analytics', AdminBlogAnalyticsController::class)
            ->name('analytics');

        Route::get('/analytics/posts/{post}', BlogPostAnalyticsController::class)
            ->name('analytics.posts.show');

        Route::get('/posts/{post}/preview', [BlogPostController::class, 'preview'])
            ->name('posts.preview');

        Route::resource('categories', BlogCategoryController::class)
            ->except('show');

        Route::resource('posts', BlogPostController::class)
            ->except('show');
    });

Route::get('/blog', [BlogController::class, 'index'])
    ->name('blog.index');

Route::get('/sitemap-blog.xml', BlogSitemapController::class)
    ->name('blog.sitemap');

Route::post('/analytics/audience', CaptureAudienceContextController::class)
    ->middleware('throttle:30,1')
    ->name('analytics.audience.capture');

Route::post('/analytics/tools', TrackToolEventController::class)
    ->middleware('throttle:120,1')
    ->name('analytics.tools.track');

Route::post('/blog/analytics', [BlogAnalyticsController::class, 'store'])
    ->middleware('throttle:120,1')
    ->name('blog.analytics');

Route::get('/blog/{slug}', [BlogController::class, 'show'])
    ->name('blog.show');

Route::get('/planos', [ContentPageController::class, 'plans'])
    ->name('plans');

Route::get('/recursos', [ContentPageController::class, 'resources'])
    ->name('resources.index');

Route::get('/recursos/{resource}', [ContentPageController::class, 'resource'])
    ->whereIn('resource', ['guias', 'modelos', 'novidades'])
    ->name('resources.show');

Route::get('/sobre', [ContentPageController::class, 'about'])
    ->name('about');

Route::get('/entrar', [ContentPageController::class, 'login'])
    ->name('login.placeholder');

Route::get('/criar-conta', [ContentPageController::class, 'register'])
    ->name('register.placeholder');

Route::get('/prazzu', [ContentPageController::class, 'prazzu'])
    ->name('prazzu');

Route::get('/sugerir-ferramenta', [SuggestToolController::class, 'create'])
    ->name('tools.suggest');

Route::post('/sugerir-ferramenta', [SuggestToolController::class, 'store'])
    ->name('tools.suggest.store');

Route::post('/newsletter', [NewsletterController::class, 'store'])
    ->name('newsletter.store');
