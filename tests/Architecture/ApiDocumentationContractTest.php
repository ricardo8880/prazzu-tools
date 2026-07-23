<?php

declare(strict_types=1);

namespace Tests\Architecture;

use App\Core\Tools\Api\Contracts\HasApiActions;
use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class ApiDocumentationContractTest extends TestCase
{
    public function test_openapi_and_postman_files_are_valid_json(): void
    {
        foreach ([
            base_path('docs/api/openapi.json'),
            base_path('docs/api/prazzu-tools.postman_collection.json'),
        ] as $path) {
            $this->assertFileExists($path);
            $decoded = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
            $this->assertIsArray($decoded);
        }
    }

    public function test_every_published_api_action_is_present_in_the_integration_materials(): void
    {
        $openApi = (string) file_get_contents(base_path('docs/api/openapi.json'));
        $postman = (string) file_get_contents(base_path('docs/api/prazzu-tools.postman_collection.json'));
        $guide = (string) file_get_contents(base_path('docs/api/README.md'));

        foreach (app(ToolRegistry::class)->modules() as $module) {
            if (! $module instanceof HasApiActions) {
                continue;
            }

            $slug = $module->manifest()->slug;

            foreach ($module->apiActions() as $actionClass) {
                $action = app($actionClass);
                $identifier = $slug.'/'.$action->name();

                $this->assertStringContainsString($slug, $openApi, $identifier.' ausente do OpenAPI.');
                $this->assertStringContainsString($identifier, $postman, $identifier.' ausente do Postman.');
                $this->assertStringContainsString($slug, $guide, $identifier.' ausente do guia.');
            }
        }
    }

    public function test_openapi_documents_the_private_security_and_versioned_routes(): void
    {
        $document = json_decode(
            (string) file_get_contents(base_path('docs/api/openapi.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        $this->assertSame('3.1.0', $document['openapi']);
        $this->assertArrayHasKey('bearerAuth', $document['components']['securitySchemes']);
        $this->assertArrayHasKey('/api/v1', $document['paths']);
        $this->assertArrayHasKey('/api/v1/tools/{tool}/{action}', $document['paths']);
    }
}
