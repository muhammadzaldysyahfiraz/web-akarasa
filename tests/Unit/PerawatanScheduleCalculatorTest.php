<?php

namespace Tests\Unit;

use App\Services\PerawatanScheduleCalculator;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PerawatanScheduleCalculatorTest extends TestCase
{
    public function test_it_calculates_repeating_and_one_time_care_rules(): void
    {
        $rules = [
            [
                'id' => 1,
                'type' => 'penyiraman',
                'nama' => 'Penyiraman',
                'interval_days' => 2,
                'mulai_setelah_hari' => 0,
                'berakhir_setelah_hari' => 5,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'type' => 'pemupukan',
                'nama' => 'Pemupukan',
                'interval_days' => null,
                'mulai_setelah_hari' => 3,
                'is_active' => true,
            ],
        ];

        $schedules = (new PerawatanScheduleCalculator())->calculate(
            new DateTimeImmutable('2026-10-13'),
            $rules,
            new DateTimeImmutable('2026-10-17'),
        );

        $this->assertSame(
            [
                ['aturan_perawatan_id' => 1, 'type' => 'penyiraman', 'nama' => 'Penyiraman', 'instruksi' => null, 'perawatan_at' => '2026-10-13'],
                ['aturan_perawatan_id' => 1, 'type' => 'penyiraman', 'nama' => 'Penyiraman', 'instruksi' => null, 'perawatan_at' => '2026-10-15'],
                ['aturan_perawatan_id' => 2, 'type' => 'pemupukan', 'nama' => 'Pemupukan', 'instruksi' => null, 'perawatan_at' => '2026-10-16'],
                ['aturan_perawatan_id' => 1, 'type' => 'penyiraman', 'nama' => 'Penyiraman', 'instruksi' => null, 'perawatan_at' => '2026-10-17'],
            ],
            $schedules,
        );
    }

    public function test_it_skips_inactive_rules_and_calculates_all_care_types(): void
    {
        $schedules = (new PerawatanScheduleCalculator())->calculate(
            new DateTimeImmutable('2026-10-13'),
            [
                ['id' => 1, 'type' => 'penyiraman', 'interval_days' => 1, 'is_active' => false],
                ['id' => 2, 'type' => 'penyiraman', 'interval_days' => 1, 'is_active' => true],
                ['id' => 3, 'type' => 'inspeksi', 'interval_days' => 1, 'is_active' => true],
            ],
            new DateTimeImmutable('2026-10-14'),
        );

        $this->assertCount(4, $schedules);
        $this->assertSame(
            ['penyiraman', 'inspeksi', 'penyiraman', 'inspeksi'],
            array_column($schedules, 'type'),
        );
    }

    public function test_it_rejects_invalid_rule_intervals(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new PerawatanScheduleCalculator())->calculate(
            new DateTimeImmutable('2026-10-13'),
            [['id' => 1, 'type' => 'penyiraman', 'interval_days' => 0]],
            new DateTimeImmutable('2026-10-20'),
        );
    }
}
