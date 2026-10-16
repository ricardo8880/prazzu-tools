<?php

declare(strict_types=1);
namespace App\Tools\BusinessDocumentValidator;
use App\Core\ToolIntegration\Data\ToolIntegrationManifest;
use App\Core\Tools\Analytics\Contracts\HasAnalyticsJourney;
use App\Core\Tools\Analytics\Data\{ToolAnalyticsField,ToolAnalyticsForm,ToolAnalyticsJourney};
use App\Core\Tools\Api\Contracts\HasApiActions;
use App\Core\Tools\Contracts\{HasToolIntegrations,HasViews,HasWebRoutes,ToolModule};
use App\Core\Tools\Data\{ToolFeature,ToolManifest};
use App\Core\Tools\Enums\{ToolAccess,ToolCategory,ToolFeatureTier,ToolStatus};
use App\Core\Tools\Infrastructure\Data\{ToolExportPolicy,ToolPersistencePolicy,ToolSensitiveDataPolicy,ToolSharingPolicy};
use App\Tools\BusinessDocumentValidator\Api\Actions\ValidateApiAction;
final class Tool implements HasAnalyticsJourney,HasApiActions,HasToolIntegrations,HasViews,HasWebRoutes,ToolModule
{
    public const SLUG='us-tax-id-validator';
    public function apiActions():array{return [ValidateApiAction::class];}
    public function integrations():ToolIntegrationManifest{return new ToolIntegrationManifest(publishes:[],accepts:[]);}
    public function analyticsJourney():ToolAnalyticsJourney{return new ToolAnalyticsJourney(toolSlug:self::SLUG,forms:[new ToolAnalyticsForm(key:'main',steps:['input'],fields:[new ToolAnalyticsField('document_number','input',selector:'[name="document_number"]')],actions:['calculate'],selector:'form[action*="validate"]',resultSelector:'[data-analytics-result="main"]')]);}
    public function manifest():ToolManifest{return new ToolManifest(slug:self::SLUG,name: 'EIN Format Validator',description: 'Check whether a U.S. Employer Identification Number matches the basic nine-digit EIN format.',category:ToolCategory::Validators,icon:'bi-shield-check',routeName:'tools.us-tax-id-validator.index',vertical:'contabilidade',version:'2.0.0',access:ToolAccess::Free,status:ToolStatus::Active,position:30,featured:true,supportsHistory:false,storesSensitiveData:false,keywords: ['EIN', 'employer identification number', 'tax ID', 'business tax ID', 'EIN format'],features:[new ToolFeature('validate_document','EIN format validation',ToolFeatureTier::Essential),new ToolFeature('batch_validation','Batch EIN format validation',ToolFeatureTier::Plus)],persistence:ToolPersistencePolicy::disabled(),export:ToolExportPolicy::disabled(),sharing:ToolSharingPolicy::disabled(),sensitiveData:ToolSensitiveDataPolicy::none());}
    public function webRoutesPath():string{return __DIR__.'/Routes/web.php';}
    public function viewsPath():string{return __DIR__.'/Resources/views';}
    public function viewsNamespace():string{return 'tools-us-tax-id-validator';}
}
