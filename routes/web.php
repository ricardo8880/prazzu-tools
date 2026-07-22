<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\Analytics\AcquisitionAnalyticsController;
use App\Http\Controllers\Admin\Analytics\AnalyticsDashboardController;
use App\Http\Controllers\Admin\Analytics\AnalyticsReportController;
use App\Http\Controllers\Admin\Analytics\AudienceAnalyticsController;
use App\Http\Controllers\Admin\Analytics\CampaignAnalyticsController;
use App\Http\Controllers\Admin\Analytics\FunnelAnalyticsController;
use App\Http\Controllers\Admin\Analytics\InsightsAnalyticsController;
use App\Http\Controllers\Admin\Analytics\RealtimeAnalyticsController;
use App\Http\Controllers\Admin\Analytics\SeoAnalyticsController;
use App\Http\Controllers\Admin\Analytics\SeoPostAnalyticsController;
use App\Http\Controllers\Admin\Analytics\ToolAnalyticsController;
use App\Http\Controllers\Admin\Analytics\ToolDetailAnalyticsController;
use App\Http\Controllers\Admin\Blog\BlogAnalyticsController as AdminBlogAnalyticsController;
use App\Http\Controllers\Admin\Blog\BlogCategoryController;
use App\Http\Controllers\Admin\Blog\BlogPostAnalyticsController;
use App\Http\Controllers\Admin\Blog\BlogPostController;
use App\Http\Controllers\Admin\Acquisition\AcquisitionContextController;
use App\Http\Controllers\Acquisition\ClearAcquisitionContextController;
use App\Http\Controllers\Acquisition\ContinueAcquisitionContextController;
use App\Http\Controllers\Analytics\CaptureAudienceContextController;
use App\Http\Controllers\Analytics\TrackAcquisitionEventController;
use App\Http\Controllers\Analytics\TrackToolEventController;
use App\Http\Controllers\Analytics\TrackToolPresenceController;
use App\Http\Controllers\Auth\AccountController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Blog\BlogAnalyticsController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Organizations\InvitationAcceptanceController;
use App\Http\Controllers\Organizations\OrganizationController;
use App\Http\Controllers\Organizations\OrganizationInvitationController;
use App\Http\Controllers\Organizations\OrganizationMemberController;
use App\Http\Controllers\Organizations\OrganizationSeatController;
use App\Http\Controllers\Platform\ContentPageController;
use App\Http\Controllers\Platform\HomeController;
use App\Http\Controllers\Platform\NewsletterController;
use App\Http\Controllers\Platform\PageFeedbackController;
use App\Http\Controllers\Platform\SuggestToolController;
use App\Http\Controllers\Platform\ToolCatalogController;
use App\Http\Controllers\Seo\BlogSitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::post('/acquisition/context/clear', ClearAcquisitionContextController::class)
    ->name('acquisition.context.clear');

Route::post('/acquisition/context/continue', ContinueAcquisitionContextController::class)
    ->name('acquisition.context.continue');

Route::get('/ferramentas', [ToolCatalogController::class, 'index'])->name('tools.index');

Route::get('/ferramentas/{category}', [ToolCatalogController::class, 'index'])
    ->whereIn('category', array_keys(config('tools.categories', [])))
    ->name('tools.category');

require __DIR__.'/tools.php';

Route::get('/admin', AdminDashboardController::class)
    ->middleware('internal.admin')
    ->name('admin.index');

Route::prefix('admin/analytics')
    ->name('admin.analytics.')
    ->middleware('internal.admin')
    ->group(function (): void {
        Route::get('/', AnalyticsDashboardController::class)->name('index');
        Route::get('/acquisition', AcquisitionAnalyticsController::class)->name('acquisition');
        Route::get('/campaigns', CampaignAnalyticsController::class)->name('campaigns');
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

Route::prefix('admin/acquisition')
    ->name('admin.acquisition.')
    ->middleware('internal.admin')
    ->group(function (): void {
        Route::patch('/contexts/{context}/toggle', [AcquisitionContextController::class, 'toggle'])
            ->name('contexts.toggle');
        Route::post('/contexts/{context}/duplicate', [AcquisitionContextController::class, 'duplicate'])
            ->name('contexts.duplicate');
        Route::resource('contexts', AcquisitionContextController::class)
            ->except('show');
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

Route::post('/analytics/acquisition', TrackAcquisitionEventController::class)
    ->middleware('throttle:120,1')
    ->name('analytics.acquisition.track');

Route::post('/analytics/tools', TrackToolEventController::class)
    ->middleware('throttle:120,1')
    ->name('analytics.tools.track');

Route::post('/analytics/tools/presence', TrackToolPresenceController::class)
    ->middleware('throttle:240,1')
    ->name('analytics.tools.presence');

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

Route::middleware('guest')->group(function (): void {
    Route::get('/entrar', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/entrar', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('login.store');

    Route::get('/criar-conta', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/criar-conta', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('register.store');

    Route::get('/esqueci-minha-senha', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/esqueci-minha-senha', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.email');
    Route::get('/redefinir-senha/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/redefinir-senha', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::get('/convites/empresa/{token}', [InvitationAcceptanceController::class, 'show'])
    ->name('organizations.invitations.show');

Route::middleware('auth')->group(function (): void {
    Route::get('/empresas/criar', [OrganizationController::class, 'create'])->name('organizations.create');
    Route::post('/empresas', [OrganizationController::class, 'store'])->name('organizations.store');
    Route::get('/empresas/{organization}', [OrganizationController::class, 'show'])->name('organizations.show');
    Route::patch('/empresas/{organization}', [OrganizationController::class, 'update'])->name('organizations.update');
    Route::patch('/empresas/{organization}/membros/{member}', [OrganizationMemberController::class, 'update'])
        ->name('organizations.members.update');
    Route::post('/empresas/{organization}/membros/{member}/vaga', [OrganizationSeatController::class, 'store'])
        ->name('organizations.seats.store');
    Route::delete('/empresas/{organization}/vagas/{seat}', [OrganizationSeatController::class, 'destroy'])
        ->name('organizations.seats.destroy');
    Route::post('/empresas/{organization}/convites', [OrganizationInvitationController::class, 'store'])
        ->name('organizations.invitations.store');
    Route::delete('/empresas/{organization}/convites/{invitation}', [OrganizationInvitationController::class, 'destroy'])
        ->name('organizations.invitations.destroy');
    Route::patch('/empresas/{organization}/convites/{invitation}/restaurar', [OrganizationInvitationController::class, 'restore'])
        ->name('organizations.invitations.restore');
    Route::delete('/empresas/{organization}/convites/{invitation}/apagar', [OrganizationInvitationController::class, 'purge'])
        ->name('organizations.invitations.purge');
    Route::post('/convites/empresa/{token}/aceitar', [InvitationAcceptanceController::class, 'accept'])
        ->name('organizations.invitations.accept');
    Route::get('/minha-conta', AccountController::class)->name('account.show');
    Route::get('/confirmar-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('/confirmar-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/confirmar-email/reenviar', EmailVerificationNotificationController::class)
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::put('/minha-conta/senha', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('password.update');
    Route::post('/sair', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::get('/prazzu', [ContentPageController::class, 'prazzu'])
    ->name('prazzu');

Route::get('/sugerir-ferramenta', [SuggestToolController::class, 'create'])
    ->name('tools.suggest');

Route::post('/sugerir-ferramenta', [SuggestToolController::class, 'store'])
    ->name('tools.suggest.store');

Route::post('/newsletter', [NewsletterController::class, 'store'])
    ->name('newsletter.store');

Route::post('/feedback/pagina', [PageFeedbackController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('feedback.page.store');
