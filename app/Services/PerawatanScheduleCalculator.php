<?php

declare(strict_types=1);

namespace App\Services;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

class PerawatanScheduleCalculator
{
    private const SUPPORTED_TYPES = ['penyiraman', 'pemupukan', 'pemangkasan', 'inspeksi', 'lainnya'];

    /**
     * Calculate care dates from a planting date and the plant's care rules.
     *
     * Rules use the aturan_perawatan column names. A null interval_days creates
     * one occurrence; otherwise occurrences repeat until the end date or horizon.
     *
     * @param iterable<array<string, mixed>> $rules
     * @return list<array{aturan_perawatan_id: int, type: string, nama: string, instruksi: ?string, perawatan_at: string}>
     */
    public function calculate(
        DateTimeInterface $plantedAt,
        iterable $rules,
        DateTimeInterface $throughDate,
    ): array {
        $plantingDate = $this->dateOnly($plantedAt);
        $horizonDate = $this->dateOnly($throughDate);

        if ($horizonDate < $plantingDate) {
            return [];
        }

        $occurrences = [];

        foreach ($rules as $rule) {
            if (! is_array($rule)) {
                throw new InvalidArgumentException('Setiap aturan perawatan harus berupa array.');
            }

            if (! ($rule['is_active'] ?? true)) {
                continue;
            }

            $type = $rule['type'] ?? null;

            if (! is_string($type)) {
                throw new InvalidArgumentException('Setiap aturan perawatan harus memiliki type yang valid.');
            }

            if (! in_array($type, self::SUPPORTED_TYPES, true)) {
                continue;
            }

            $ruleId = $this->positiveInteger($rule['id'] ?? null, 'id');
            $startOffset = $this->nonNegativeInteger($rule['mulai_setelah_hari'] ?? null, 'mulai_setelah_hari', 0);
            $endOffset = $this->nullableNonNegativeInteger($rule['berakhir_setelah_hari'] ?? null, 'berakhir_setelah_hari');
            $interval = $this->nullablePositiveInteger($rule['interval_days'] ?? null, 'interval_days');

            if ($endOffset !== null && $endOffset < $startOffset) {
                throw new InvalidArgumentException('berakhir_setelah_hari tidak boleh sebelum mulai_setelah_hari.');
            }

            $date = $plantingDate->modify("+{$startOffset} days");
            $lastDate = $endOffset === null
                ? $horizonDate
                : min($horizonDate, $plantingDate->modify("+{$endOffset} days"));

            while ($date <= $lastDate) {
                $occurrences[] = [
                    'aturan_perawatan_id' => $ruleId,
                    'type' => $type,
                    'nama' => (string) ($rule['nama'] ?? ''),
                    'instruksi' => isset($rule['instruksi']) ? (string) $rule['instruksi'] : null,
                    'perawatan_at' => $date->format('Y-m-d'),
                ];

                if ($interval === null) {
                    break;
                }

                $date = $date->modify("+{$interval} days");
            }
        }

        usort(
            $occurrences,
            static fn (array $left, array $right): int =>
                [$left['perawatan_at'], $left['aturan_perawatan_id']]
                <=> [$right['perawatan_at'], $right['aturan_perawatan_id']],
        );

        return $occurrences;
    }

    private function dateOnly(DateTimeInterface $date): DateTimeImmutable
    {
        return new DateTimeImmutable($date->format('Y-m-d'));
    }

    private function positiveInteger(mixed $value, string $field): int
    {
        $integer = filter_var($value, FILTER_VALIDATE_INT);

        if ($integer === false || $integer < 1) {
            throw new InvalidArgumentException("{$field} harus berupa bilangan bulat positif.");
        }

        return $integer;
    }

    private function nonNegativeInteger(mixed $value, string $field, int $default): int
    {
        if ($value === null) {
            return $default;
        }

        $integer = filter_var($value, FILTER_VALIDATE_INT);

        if ($integer === false || $integer < 0) {
            throw new InvalidArgumentException("{$field} harus berupa bilangan bulat nol atau lebih.");
        }

        return $integer;
    }

    private function nullableNonNegativeInteger(mixed $value, string $field): ?int
    {
        return $value === null
            ? null
            : $this->nonNegativeInteger($value, $field, 0);
    }

    private function nullablePositiveInteger(mixed $value, string $field): ?int
    {
        return $value === null
            ? null
            : $this->positiveInteger($value, $field);
    }
}
