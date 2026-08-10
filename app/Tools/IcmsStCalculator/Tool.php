<?php

declare(strict_types=1);

namespace App\Tools\IcmsStCalculator;

use App\Core\ToolIntegration\Data\ToolIntegrationManifest;
use App\Core\Tools\Analytics\Contracts\HasAnalyticsJourney;
use App\Core\Tools\Analytics\Data\ToolAnalyticsField;
use App\Core\Tools\Analytics\Data\ToolAnalyticsForm;
use App\Core\Tools\Analytics\Data\ToolAnalyticsJourney;
use App\Core\Tools\Contracts\HasToolIntegrations;
use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Contracts\HasWebRoutes;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Data\ToolFeature;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolAccess;
use App\Core\Tools\Enums\ToolCapability;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Core\Tools\History\Contracts\HasHistoryPolicy;
use App\Core\Tools\History\Data\ToolHistoryPolicy;
use App\Core\Tools\Infrastructure\Data\ToolExportPolicy;
use App\Core\Tools\Infrastructure\Data\ToolPersistencePolicy;
use App\Core\Tools\Infrastructure\Data\ToolSensitiveDataPolicy;
use App\Core\Tools\Infrastructure\Data\ToolSharingPolicy;

final class Tool implements HasAnalyticsJourney, HasHistoryPolicy, HasToolIntegrations, HasViews, HasWebRoutes, ToolModule
{
    public const SLUG='calculadora-icms-st';
    public function integrations(): ToolIntegrationManifest { return new ToolIntegrationManifest(publishes:[],accepts:[]); }
    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug:self::SLUG,name:'Calculadora de ICMS-ST',description:'Estime ICMS-ST por MVA, com MVA ajustada, FCP, operações interestaduais, múltiplos itens e memória de cálculo.',category:ToolCategory::Fiscal,icon:'bi-box-seam',routeName:'tools.calculadora-icms-st.index',vertical:'contabilidade',version:'1.0.0',access:ToolAccess::Free,status:ToolStatus::Beta,position:13,featured:true,supportsHistory:true,storesSensitiveData:false,
            keywords:['ICMS-ST','substituição tributária','MVA','MVA ajustada','FCP','CEST','ICMS interestadual'],
            capabilities:[ToolCapability::History,ToolCapability::VersionedPersistence,ToolCapability::Export],
            features:[
                new ToolFeature('calculate','ICMS-ST estimado de uma operação com MVA informada',ToolFeatureTier::Essential),
                new ToolFeature('memory','Memória de cálculo e premissas fiscais',ToolFeatureTier::Essential),
                new ToolFeature('adjusted_mva','MVA ajustada em operação interestadual',ToolFeatureTier::Plus),
                new ToolFeature('fcp','FCP-ST parametrizado',ToolFeatureTier::Plus),
                new ToolFeature('interstate','Operações interestaduais com alíquota própria',ToolFeatureTier::Plus),
                new ToolFeature('multiple_items','Múltiplos itens na mesma operação',ToolFeatureTier::Plus),
                new ToolFeature('export','Relatório em PDF e planilha',ToolFeatureTier::Plus),
                new ToolFeature('history','Histórico autenticado',ToolFeatureTier::Plus),
            ],
            persistence:new ToolPersistencePolicy(true,1,365,1),export:new ToolExportPolicy(true,['pdf','xlsx','json']),sharing:ToolSharingPolicy::disabled(),sensitiveData:ToolSensitiveDataPolicy::none(),
        );
    }
    public function historyPolicy(): ToolHistoryPolicy { return new ToolHistoryPolicy(true,365,['competence','operation_type','origin_uf','destination_uf','merchandise_value','freight','insurance','other_charges','ipi','discount','original_mva','internal_rate','interstate_rate','adjust_mva','fcp_rate','own_icms_override','items'],['st_base','icms_st','fcp_st','total','summary','details','warnings','calculation_memory'],[]); }
    public function webRoutesPath(): string { return __DIR__.'/Routes/web.php'; }
    public function viewsPath(): string { return __DIR__.'/Resources/views'; }
    public function viewsNamespace(): string { return 'tools-calculadora-icms-st'; }
    public function analyticsJourney(): ToolAnalyticsJourney
    {
        return new ToolAnalyticsJourney(toolSlug:self::SLUG,forms:[new ToolAnalyticsForm(key:'main',steps:['input','advanced'],fields:[
            new ToolAnalyticsField('competence','input',selector:'[name="competence"]'),new ToolAnalyticsField('operation_type','input',selector:'[name="operation_type"]'),new ToolAnalyticsField('merchandise_value','input',selector:'[name="merchandise_value"]'),new ToolAnalyticsField('original_mva','input',selector:'[name="original_mva"]'),new ToolAnalyticsField('internal_rate','input',selector:'[name="internal_rate"]'),new ToolAnalyticsField('interstate_rate','advanced',selector:'[name="interstate_rate"]'),new ToolAnalyticsField('adjust_mva','advanced',selector:'[name="adjust_mva"]'),new ToolAnalyticsField('fcp_rate','advanced',selector:'[name="fcp_rate"]'),
        ],actions:['calculate','export'],selector:'form[action*="calculadora-icms-st"]',resultSelector:'[data-analytics-result="main"]')]);
    }
}
