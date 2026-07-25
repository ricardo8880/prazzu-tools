<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Imports\Contracts\ImportDatasetStore;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsureTabularImportFeatureAccess
{
    public function __construct(
        private ImportDatasetStore $datasets,
        private EnsureToolFeatureAccess $features,
    ) {}

    public function handle(
        Request $request,
        Closure $next,
        string $toolSlug,
        string $ownerNamespace,
        string $csvFeatureKey,
        string $xlsxFeatureKey,
    ): Response {
        $uploadedFile = $request->file('import_file');
        if ($uploadedFile instanceof UploadedFile) {
            $featureKey = $this->featureForFormat(
                $uploadedFile->getClientOriginalExtension(),
                $csvFeatureKey,
                $xlsxFeatureKey,
            );

            return $featureKey === null
                ? $next($request)
                : $this->features->handle($request, $next, $toolSlug, $featureKey);
        }

        $token = $request->input('import_token');
        if (! is_string($token) || $token === '') {
            return $next($request);
        }

        $dataset = $this->datasets->get($token, $this->ownerKey($request, $ownerNamespace));
        if ($dataset === null) {
            return $next($request);
        }

        $featureKey = $this->featureForFormat($dataset->format, $csvFeatureKey, $xlsxFeatureKey);
        abort_if($featureKey === null, 422, 'O formato armazenado para esta importação não é suportado.');

        return $this->features->handle($request, $next, $toolSlug, $featureKey);
    }

    private function featureForFormat(string $format, string $csvFeatureKey, string $xlsxFeatureKey): ?string
    {
        return match (strtolower(trim($format))) {
            'csv' => $csvFeatureKey,
            'xlsx' => $xlsxFeatureKey,
            default => null,
        };
    }

    private function ownerKey(Request $request, string $ownerNamespace): string
    {
        $identity = $request->user() !== null
            ? 'user:'.$request->user()->getAuthIdentifier()
            : 'ip:'.($request->ip() ?? 'unknown');

        return rtrim($ownerNamespace, ':').':'.$identity;
    }
}
