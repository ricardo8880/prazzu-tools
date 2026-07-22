<?php

namespace App\Core\Acquisition\Domain\Data;

final readonly class AcquisitionAnalyticsSnapshot
{
    public function __construct(
        public int $contextId,
        public string $contextName,
        public string $keyword,
        public ?string $campaignIdentifier,
        public ?string $primaryToolSlug,
    ) {}

    public static function fromContext(AcquisitionContext $context): self
    {
        return new self(
            contextId: $context->id,
            contextName: $context->name,
            keyword: $context->keyword,
            campaignIdentifier: $context->campaignIdentifier,
            primaryToolSlug: $context->primaryToolSlug,
        );
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): ?self
    {
        $contextId = filter_var($data['context_id'] ?? null, FILTER_VALIDATE_INT);
        $contextName = trim((string) ($data['context_name'] ?? ''));
        $keyword = trim((string) ($data['keyword'] ?? ''));

        if ($contextId === false || $contextId < 1 || $contextName === '' || $keyword === '') {
            return null;
        }

        return new self(
            contextId: $contextId,
            contextName: $contextName,
            keyword: $keyword,
            campaignIdentifier: self::nullableString($data['campaign_identifier'] ?? null),
            primaryToolSlug: self::nullableString($data['primary_tool_slug'] ?? null),
        );
    }

    /** @return array{context_id:int,context_name:string,keyword:string,campaign_identifier:?string,primary_tool_slug:?string} */
    public function toArray(): array
    {
        return [
            'context_id' => $this->contextId,
            'context_name' => $this->contextName,
            'keyword' => $this->keyword,
            'campaign_identifier' => $this->campaignIdentifier,
            'primary_tool_slug' => $this->primaryToolSlug,
        ];
    }

    private static function nullableString(mixed $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }
}
