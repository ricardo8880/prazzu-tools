<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

return new class extends Migration
{
    private const LEGACY_RULE_VERSION = 'legacy-simples-local-v1';

    private const TOOL_SLUG = 'calculadora-simples-nacional';

    public function up(): void
    {
        if (! Schema::hasTable('simples_nacional_calculations')) {
            return;
        }

        if (! Schema::hasTable('tool_runs')) {
            throw new RuntimeException('O histórico central deve existir antes da migração do histórico do Simples Nacional.');
        }

        DB::table('simples_nacional_calculations')
            ->whereNotNull('user_id')
            ->orderBy('id')
            ->chunkById(100, function (Collection $calculations): void {
                DB::transaction(function () use ($calculations): void {
                    foreach ($calculations as $calculation) {
                        $runId = $this->runId((int) $calculation->id);

                        if (DB::table('tool_runs')->where('id', $runId)->exists()) {
                            continue;
                        }

                        $referenceDate = substr((string) $calculation->reference_month, 0, 10);
                        $createdAt = (string) ($calculation->created_at ?? $referenceDate.' 00:00:00');
                        $finishedAt = (string) ($calculation->updated_at ?? $createdAt);
                        $result = $this->legacyResult($calculation);
                        $input = [
                            'reference_month' => substr($referenceDate, 0, 7),
                            'annex' => (string) $calculation->annex,
                            'rbt12' => $this->minorToDecimal((int) $calculation->rbt12_cents),
                            'monthly_revenue' => $this->minorToDecimal((int) $calculation->monthly_revenue_cents),
                            '_legacy_id' => (int) $calculation->id,
                            '_legacy_estimated_das_cents' => (int) $calculation->estimated_das_cents,
                            '_legacy_effective_rate' => (string) $calculation->effective_rate,
                        ];

                        DB::table('tool_runs')->insert([
                            'id' => $runId,
                            'user_id' => (int) $calculation->user_id,
                            'tool_slug' => self::TOOL_SLUG,
                            'tool_version' => '1.0.0',
                            'rule_version' => self::LEGACY_RULE_VERSION,
                            'reference_date' => $referenceDate,
                            'status' => 'succeeded',
                            'input_payload' => $this->encrypt($input),
                            'result_payload' => $this->encrypt($result),
                            'normative_references' => $this->encrypt([]),
                            'error_code' => null,
                            'started_at' => $createdAt,
                            'finished_at' => $finishedAt,
                            'expires_at' => CarbonImmutable::parse($finishedAt)->addDays(365)->format('Y-m-d H:i:s'),
                            'created_at' => $createdAt,
                            'updated_at' => $finishedAt,
                        ]);
                    }
                });
            });

        Schema::drop('simples_nacional_calculations');
    }

    public function down(): void
    {
        if (! Schema::hasTable('tool_runs')) {
            return;
        }

        if (! Schema::hasTable('simples_nacional_calculations')) {
            $this->createLegacyTable();
        }

        $runs = DB::table('tool_runs')
            ->where('tool_slug', self::TOOL_SLUG)
            ->where('rule_version', self::LEGACY_RULE_VERSION)
            ->orderBy('created_at')
            ->get();

        DB::transaction(function () use ($runs): void {
            foreach ($runs as $run) {
                $input = $this->decrypt((string) $run->input_payload);
                $result = $this->decrypt((string) $run->result_payload);
                $legacyId = (int) ($input['_legacy_id'] ?? 0);

                if ($legacyId < 1) {
                    continue;
                }

                DB::table('simples_nacional_calculations')->updateOrInsert(
                    ['id' => $legacyId],
                    [
                        'user_id' => (int) $run->user_id,
                        'session_key' => null,
                        'company_name' => 'Cenário migrado',
                        'reference_month' => (string) $run->reference_date,
                        'annex' => (string) ($input['annex'] ?? $result['annex'] ?? ''),
                        'rbt12_cents' => $this->decimalToMinor((string) ($input['rbt12'] ?? '0.00')),
                        'monthly_revenue_cents' => $this->decimalToMinor((string) ($input['monthly_revenue'] ?? '0.00')),
                        'estimated_das_cents' => (int) ($input['_legacy_estimated_das_cents'] ?? 0),
                        'effective_rate' => (string) ($input['_legacy_effective_rate'] ?? '0.0000'),
                        'payload' => json_encode($result, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                        'created_at' => $run->created_at,
                        'updated_at' => $run->updated_at,
                    ],
                );
            }

            DB::table('tool_runs')
                ->where('tool_slug', self::TOOL_SLUG)
                ->where('rule_version', self::LEGACY_RULE_VERSION)
                ->delete();
        });
    }

    private function createLegacyTable(): void
    {
        Schema::create('simples_nacional_calculations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_key', 120)->nullable()->index();
            $table->string('company_name', 160);
            $table->date('reference_month')->index();
            $table->string('annex', 5);
            $table->unsignedBigInteger('rbt12_cents');
            $table->unsignedBigInteger('monthly_revenue_cents');
            $table->unsignedBigInteger('estimated_das_cents');
            $table->decimal('effective_rate', 10, 4);
            $table->json('payload');
            $table->timestamps();

            $table->index(['user_id', 'reference_month']);
            $table->index(['session_key', 'reference_month']);
        });
    }

    private function legacyResult(object $calculation): array
    {
        $decoded = json_decode((string) $calculation->payload, true);
        $result = is_array($decoded) ? $decoded : [];
        unset($result['company_name']);

        return [
            ...$result,
            'annex' => $result['annex'] ?? (string) $calculation->annex,
            'rbt12' => $result['rbt12'] ?? $this->formatMoney((int) $calculation->rbt12_cents),
            'monthly_revenue' => $result['monthly_revenue'] ?? $this->formatMoney((int) $calculation->monthly_revenue_cents),
            'effective_rate' => $result['effective_rate'] ?? (string) $calculation->effective_rate.'%',
            'estimated_das' => $result['estimated_das'] ?? $this->formatMoney((int) $calculation->estimated_das_cents),
        ];
    }

    private function encrypt(array $payload): string
    {
        return Crypt::encryptString(json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }

    private function decrypt(string $payload): array
    {
        $decoded = json_decode(Crypt::decryptString($payload), true, 512, JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : [];
    }

    private function runId(int $legacyId): string
    {
        return Uuid::uuid5(Uuid::NAMESPACE_URL, 'prazzu-tools:simples-nacional-history:'.$legacyId)->toString();
    }

    private function minorToDecimal(int $minor): string
    {
        return intdiv($minor, 100).'.'.str_pad((string) ($minor % 100), 2, '0', STR_PAD_LEFT);
    }

    private function decimalToMinor(string $decimal): int
    {
        [$whole, $fraction] = array_pad(explode('.', $decimal, 2), 2, '0');

        return ((int) $whole * 100) + (int) str_pad(substr($fraction, 0, 2), 2, '0');
    }

    private function formatMoney(int $minor): string
    {
        $whole = preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', (string) intdiv($minor, 100));
        $fraction = str_pad((string) ($minor % 100), 2, '0', STR_PAD_LEFT);

        return 'R$ '.$whole.','.$fraction;
    }
};
