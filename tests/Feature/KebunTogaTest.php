<?php

namespace Tests\Feature;

use App\Livewire\TanamanKatalog;
use App\Models\AktivitasPerawatan;
use App\Models\AturanPerawatan;
use App\Models\Panen;
use App\Models\Penanam;
use App\Models\Tanaman;
use App\Models\TanamanDitanam;
use App\Models\User;
use App\Services\PerawatanScheduleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class KebunTogaTest extends TestCase
{
    use RefreshDatabase;

    private Tanaman $tanaman;

    private Penanam $penanam;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tanaman = Tanaman::create([
            'nama' => 'Jahe Uji',
            'kategori' => 'Rimpang',
            'deskripsi' => 'Jahe untuk pengujian.',
            'kebutuhan_cahaya' => 'Cahaya pagi',
            'kebutuhan_air' => 'Siram saat media kering',
            'min_hari_panen' => 100,
            'max_hari_panen' => 120,
            'is_active' => true,
        ]);

        AturanPerawatan::create([
            'tanaman_id' => $this->tanaman->id,
            'nama' => 'Penyiraman rutin',
            'type' => 'penyiraman',
            'instruksi' => 'Siram secukupnya.',
            'interval_days' => 2,
            'mulai_setelah_hari' => 0,
            'is_active' => true,
        ]);

        $this->penanam = Penanam::create([
            'nama' => 'Penanam Uji',
            'access_code_hash' => Hash::make('rahasia-kebun-123'),
        ]);
    }

    public function test_public_catalog_and_plant_details_use_database_content(): void
    {
        $this->get(route('tanaman.index'))->assertOk()->assertSee('Jahe Uji');
        $this->get(route('tanaman.show', $this->tanaman))
            ->assertOk()
            ->assertSee('Cahaya pagi')
            ->assertSee('100–120 hari');
    }

    public function test_livewire_catalog_searches_and_filters_database_plants(): void
    {
        Tanaman::create([
            'nama' => 'Kunyit Uji',
            'kategori' => 'Rimpang',
            'kebutuhan_cahaya' => 'Terang',
            'kebutuhan_air' => 'Secukupnya',
            'is_active' => true,
        ]);

        Livewire::test(TanamanKatalog::class)
            ->assertSee('Jahe Uji')
            ->assertSee('Kunyit Uji')
            ->set('search', 'Jahe')
            ->assertSee('Jahe Uji')
            ->assertDontSee('Kunyit Uji')
            ->set('search', '')
            ->set('kategori', 'Rimpang')
            ->assertSee('Jahe Uji');
    }

    public function test_inactive_plants_are_not_visible_publicly(): void
    {
        $this->tanaman->update(['is_active' => false]);

        $this->get(route('tanaman.index'))->assertOk()->assertDontSee('Jahe Uji');
        $this->get(route('tanaman.show', $this->tanaman))->assertNotFound();
        $this->getJson('/api/v1/tanaman')->assertOk()->assertJsonMissing(['nama' => 'Jahe Uji']);
    }

    public function test_public_debug_routes_cannot_change_catalog_data(): void
    {
        $this->get('/hapus-jahe')->assertNotFound();
        $this->get('/cek-tanaman')->assertNotFound();
        $this->assertDatabaseHas('tanaman', ['id' => $this->tanaman->id]);
    }

    public function test_public_can_add_a_plant_and_schedule_is_generated_from_its_rules(): void
    {
        $response = $this->post(route('tanaman-ditanam.store'), [
            'penanam_id' => $this->penanam->id,
            'tanaman_id' => $this->tanaman->id,
            'ditanam_at' => today()->toDateString(),
            'fase_pertumbuhan' => 'bibit',
            'quantity' => 2,
            'location' => 'Teras',
        ]);

        $planting = TanamanDitanam::firstOrFail();
        $response->assertRedirect(route('kebun.show', $this->penanam));
        $this->assertDatabaseHas('tanaman_ditanam', ['id' => $planting->id, 'quantity' => 2]);
        $this->assertSame('bibit', $planting->fase_pertumbuhan);
        $this->assertGreaterThan(1, $planting->jadwalPerawatan()->count());
    }

    public function test_new_grower_name_is_created_once_without_a_pin(): void
    {
        $this->get(route('tanaman-ditanam.create'))
            ->assertOk()
            ->assertSee('Atau tulis nama penanam baru')
            ->assertDontSee('access_code')
            ->assertDontSee('type="password"', false);

        $this->post(route('tanaman-ditanam.store'), [
            'penanam_nama_baru' => 'Ibu Siti',
            'tanaman_id' => $this->tanaman->id,
            'ditanam_at' => today()->toDateString(),
            'fase_pertumbuhan' => 'anakan',
        ])->assertRedirect();

        $this->assertDatabaseCount('penanam', 2);
        $this->assertSame('Ibu Siti', Penanam::where('nama', 'Ibu Siti')->value('nama'));
        $planting = TanamanDitanam::latest('id')->firstOrFail();
        $this->assertSame('anakan', $planting->fase_pertumbuhan);

        $this->from(route('tanaman-ditanam.create'))
            ->post(route('tanaman-ditanam.store'), [
                'penanam_nama_baru' => '  ibu siti ',
                'tanaman_id' => $this->tanaman->id,
                'ditanam_at' => today()->toDateString(),
                'fase_pertumbuhan' => 'bibit',
            ])
            ->assertRedirect(route('tanaman-ditanam.create'))
            ->assertSessionHasErrors('penanam_nama_baru');
        $this->assertDatabaseCount('penanam', 2);
    }

    public function test_harvest_estimate_uses_the_selected_growth_phase(): void
    {
        $estimates = collect(['bibit', 'anakan', 'dewasa'])->map(function (string $phase) {
            $planting = TanamanDitanam::create([
                'penanam_id' => $this->penanam->id,
                'tanaman_id' => $this->tanaman->id,
                'ditanam_at' => today(),
                'catatan' => TanamanDitanam::catatanDenganFase(null, $phase),
            ]);

            return $planting->estimasiPanen();
        });

        $this->assertSame(today()->addDays(100)->toDateString(), $estimates[0]['mulai']->toDateString());
        $this->assertSame(today()->addDays(67)->toDateString(), $estimates[1]['mulai']->toDateString());
        $this->assertSame(today()->addDays(33)->toDateString(), $estimates[2]['mulai']->toDateString());
    }

    public function test_invalid_access_code_cannot_open_or_change_a_garden(): void
    {
        $planting = $this->createPlanting();
        $schedule = $planting->jadwalPerawatan()->firstOrFail();

        $this->withHeader('X-Kode-Akses', 'wrong-code')
            ->getJson('/api/v1/penamans/'.$this->penanam->id.'/garden')
            ->assertForbidden();

        $this->patchJson('/api/v1/jadwal-perawatan/'.$schedule->id.'/complete', ['access_code' => 'wrong-code'])
            ->assertForbidden();

        $this->assertDatabaseHas('jadwal_perawatan', ['id' => $schedule->id, 'status' => 'pending']);
    }

    public function test_completing_a_schedule_records_one_activity_for_the_right_grower(): void
    {
        $planting = $this->createPlanting();
        $schedule = $planting->jadwalPerawatan()->firstOrFail();
        $this->patch(route('kebun.complete', [$this->penanam, $schedule]))
            ->assertRedirect(route('kebun.show', $this->penanam));

        $this->assertDatabaseHas('jadwal_perawatan', ['id' => $schedule->id, 'status' => 'completed']);
        $this->assertDatabaseCount('aktivitas_perawatan', 1);

        $this->patch(route('kebun.complete', [$this->penanam, $schedule]));
        $this->assertDatabaseCount('aktivitas_perawatan', 1);
    }

    public function test_catalog_detail_shows_general_information_and_kebun_shows_care_activity(): void
    {
        AturanPerawatan::create([
            'tanaman_id' => $this->tanaman->id,
            'nama' => 'Pemupukan kompos',
            'type' => 'pemupukan',
            'instruksi' => 'Tambahkan kompos.',
            'interval_days' => 14,
            'mulai_setelah_hari' => 0,
            'is_active' => true,
        ]);

        $planting = $this->createPlanting();
        $wateringSchedule = $planting->jadwalPerawatan()
            ->whereHas('aturanPerawatan', fn ($query) => $query->where('type', 'penyiraman'))
            ->whereDate('perawatan_at', today())
            ->firstOrFail();
        $fertilizingSchedule = $planting->jadwalPerawatan()
            ->whereHas('aturanPerawatan', fn ($query) => $query->where('type', 'pemupukan'))
            ->whereDate('perawatan_at', today())
            ->firstOrFail();

        $this->patch(route('kebun.complete', [$this->penanam, $wateringSchedule]))
            ->assertRedirect(route('kebun.show', $this->penanam));

        $this->get(route('tanaman.show', $this->tanaman))
            ->assertOk()
            ->assertSee('Perkiraan panen')
            ->assertDontSee('Penanam Uji');

        $this->patch(route('kebun.complete', [$this->penanam, $fertilizingSchedule]))
            ->assertNotFound();
        $this->get(route('kebun.show', $this->penanam))
            ->assertOk()
            ->assertSee('Lihat riwayat penyiraman')
            ->assertSee('Sudah disiram');
    }

    public function test_public_garden_and_private_api_show_the_growers_plants(): void
    {
        $planting = $this->createPlanting();
        $this->get(route('kebun.show', $this->penanam))
            ->assertOk()
            ->assertSee('Perawatan: penyiraman')
            ->assertSee('Jahe Uji');

        $this->withHeader('X-Kode-Akses', 'rahasia-kebun-123')
            ->getJson('/api/v1/penamans/'.$this->penanam->id.'/garden')
            ->assertOk()
            ->assertJsonPath('data.0.tanaman.nama', 'Jahe Uji');

        $this->withHeader('X-Kode-Akses', 'rahasia-kebun-123')
            ->getJson('/api/v1/penamans/'.$this->penanam->id.'/perawatan?tanggal='.today()->toDateString())
            ->assertOk()
            ->assertJsonPath('data.0.status', 'pending');
    }

    public function test_api_contract_returns_resource_data_and_validation_errors(): void
    {
        $this->getJson('/api/v1/tanaman')
            ->assertOk()
            ->assertJsonStructure(['data'])
            ->assertJsonPath('data.0.nama', 'Jahe Uji')
            ->assertJsonMissingPath('links');

        $this->postJson('/api/v1/tanaman-tanaman', [])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Data yang diberikan tidak valid.')
            ->assertJsonStructure(['errors' => ['penanam_id', 'access_code', 'tanaman_id', 'ditanam_at']]);
    }

    public function test_admin_web_and_api_require_authentication(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
        $this->getJson('/api/v1/admin/tanaman')->assertUnauthorized();

        $admin = User::factory()->create(['email' => 'admin@example.test', 'role' => 'admin']);
        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->getJson('/api/v1/admin/tanaman')->assertOk();
    }

    public function test_admin_can_add_a_plant_with_the_redesigned_form_and_photo_upload(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.resources.create', 'tanaman'))
            ->assertOk()
            ->assertSee('Foto Tanaman')
            ->assertSee('Jenis / Kategori Tanaman')
            ->assertSee('Catatan Tambahan');

        $this->post(route('admin.resources.store', 'tanaman'), [
            'nama' => 'Kunyit Ungu',
            'kategori' => 'Rimpang',
            'nama_penanam' => 'Pak Rudi',
            'deskripsi' => 'Tanaman untuk pengujian.',
            'foto' => UploadedFile::fake()->create('kunyit-ungu.jpg', 10, 'image/jpeg'),
        ])->assertRedirect(route('admin.resources.index', 'tanaman'));

        $plant = Tanaman::where('nama', 'Kunyit Ungu')->firstOrFail();
        $this->assertSame('Belum ditentukan', $plant->kebutuhan_cahaya);
        $this->assertSame('Belum ditentukan', $plant->kebutuhan_air);
        $this->assertNotNull($plant->foto_path);
        Storage::disk('public')->assertExists($plant->foto_path);

        $this->get(route('tanaman.show', $plant))
            ->assertOk()
            ->assertSee(Storage::disk('public')->url($plant->foto_path), false);
    }

    public function test_admin_can_manage_catalog_growers_and_care_rules(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->post(route('admin.resources.store', 'tanaman'), [
            'nama' => 'Lengkuas',
            'kategori' => 'Rimpang',
            'kebutuhan_cahaya' => 'Matahari pagi',
            'kebutuhan_air' => 'Media lembap',
            'min_hari_panen' => 150,
            'max_hari_panen' => 210,
            'is_active' => 1,
        ])->assertRedirect(route('admin.resources.index', 'tanaman'));
        $catalogPlant = Tanaman::where('nama', 'Lengkuas')->firstOrFail();

        $this->put(route('admin.resources.update', ['tanaman', $catalogPlant->id]), [
            'nama' => 'Lengkuas',
            'kategori' => 'Rimpang obat',
            'kebutuhan_cahaya' => 'Matahari pagi',
            'kebutuhan_air' => 'Media lembap',
            'min_hari_panen' => 150,
            'max_hari_panen' => 210,
            'is_active' => 1,
        ])->assertRedirect(route('admin.resources.index', 'tanaman'));
        $this->assertDatabaseHas('tanaman', ['id' => $catalogPlant->id, 'kategori' => 'Rimpang obat']);

        $this->get(route('admin.resources.create', 'penamans'))
            ->assertOk()
            ->assertDontSee('access_code');

        $this->post(route('admin.resources.store', 'penamans'), [
            'nama' => 'Penanam Admin',
        ])->assertRedirect(route('admin.resources.index', 'penamans'));
        $grower = Penanam::where('nama', 'Penanam Admin')->firstOrFail();
        $this->assertNotEmpty($grower->access_code_hash);
        $this->from(route('admin.resources.create', 'penamans'))
            ->post(route('admin.resources.store', 'penamans'), ['nama' => 'penanam admin'])
            ->assertSessionHasErrors('nama');

        $this->post(route('admin.resources.store', 'aturan-perawatan'), [
            'tanaman_id' => $catalogPlant->id,
            'nama' => 'Pupuk kompos',
            'type' => 'pemupukan',
            'interval_days' => 30,
            'mulai_setelah_hari' => 15,
            'is_active' => 1,
        ])->assertRedirect(route('admin.resources.index', 'aturan-perawatan'));

        $rule = AturanPerawatan::where('nama', 'Pupuk kompos')->firstOrFail();
        $this->delete(route('admin.resources.destroy', ['aturan-perawatan', $rule->id]))
            ->assertRedirect();
        $this->assertDatabaseMissing('aturan_perawatan', ['id' => $rule->id]);
    }

    public function test_admin_api_supports_crud_and_returns_resource_envelopes(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->postJson('/api/v1/admin/penamans', [
            'nama' => ' penanam uji ',
            'access_code' => 'kode-aman-123',
        ])->assertUnprocessable()->assertJsonStructure(['errors' => ['nama']]);

        $this->getJson('/api/v1/admin/tanaman/'.$this->tanaman->id)
            ->assertOk()
            ->assertJsonPath('data.nama', 'Jahe Uji');

        $this->patchJson('/api/v1/admin/tanaman/'.$this->tanaman->id, [
            'kategori' => 'Rimpang terpilih',
        ])->assertOk()->assertJsonPath('data.kategori', 'Rimpang terpilih');

        $this->postJson('/api/v1/admin/aturan-perawatan', [
            'tanaman_id' => $this->tanaman->id,
            'nama' => 'Inspeksi API',
            'type' => 'inspeksi',
            'interval_days' => 7,
            'is_active' => true,
        ])->assertCreated()->assertJsonPath('data.nama', 'Inspeksi API');

        $rule = AturanPerawatan::where('nama', 'Inspeksi API')->firstOrFail();
        $this->deleteJson('/api/v1/admin/aturan-perawatan/'.$rule->id)->assertNoContent();
    }

    public function test_schedule_generation_is_idempotent(): void
    {
        $planting = $this->createPlanting();
        $before = $planting->jadwalPerawatan()->count();

        app(PerawatanScheduleService::class)->generateRollingHorizon($planting);

        $this->assertGreaterThan(0, $before);
        $this->assertSame($before, $planting->jadwalPerawatan()->count());
    }

    public function test_only_three_public_sections_are_available_and_admin_can_manage_care_rules(): void
    {
        $this->createPlanting();
        $this->get('/jadwal-perawatan')->assertNotFound();
        $this->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Unduh laporan PDF');
        $this->get(route('tanaman.index'))->assertOk()->assertSee('Katalog Tanaman');
        $this->get(route('penanam.index'))->assertOk()->assertSee('Pilih nama penanam');

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->get(route('admin.resources.index', 'aturan-perawatan'))
            ->assertOk();
    }

    public function test_dashboard_combines_public_recap_and_downloadable_pdf_report(): void
    {
        $planting = $this->createPlanting();
        $schedule = $planting->jadwalPerawatan()
            ->whereDate('perawatan_at', today())
            ->firstOrFail();
        $schedule->update(['status' => 'completed']);
        AktivitasPerawatan::create([
            'tanaman_ditanam_id' => $planting->id,
            'jadwal_perawatan_id' => $schedule->id,
            'performed_at' => now(),
        ]);

        $this->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('Laporan perawatan')
            ->assertSee('Aktivitas terbaru')
            ->assertSee('Jahe Uji')
            ->assertSee('Unduh laporan PDF');

        $this->get(route('laporan.index'))
            ->assertRedirect(route('user.dashboard'));

        $pdf = $this->get(route('laporan.pdf'));
        $pdf->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $pdf->getContent());
    }

    public function test_any_visitor_can_confirm_and_mark_a_watering_activity(): void
    {
        $planting = $this->createPlanting();
        $schedule = $planting->jadwalPerawatan()
            ->whereHas('aturanPerawatan', fn ($query) => $query->where('type', 'penyiraman'))
            ->firstOrFail();

        $this->get(route('kebun.show', $this->penanam))
            ->assertOk()
            ->assertSee("confirm('Yakin tanaman ini sudah disiram?')", false);

        $this->patch(route('kebun.complete', [$this->penanam, $schedule]))
            ->assertRedirect(route('kebun.show', $this->penanam));

        $this->assertDatabaseHas('jadwal_perawatan', [
            'id' => $schedule->id,
            'status' => 'completed',
        ]);
        $this->assertDatabaseHas('aktivitas_perawatan', [
            'jadwal_perawatan_id' => $schedule->id,
        ]);

        $this->get(route('kebun.show', $this->penanam))
            ->assertOk()
            ->assertSee('Sudah disiram');
    }

    public function test_activity_can_be_cancelled_only_during_the_same_week(): void
    {
        $planting = $this->createPlanting();
        $schedule = $planting->jadwalPerawatan()
            ->whereHas('aturanPerawatan', fn ($query) => $query->where('type', 'penyiraman'))
            ->firstOrFail();
        $schedule->update(['status' => 'completed']);
        $activity = AktivitasPerawatan::create([
            'tanaman_ditanam_id' => $planting->id,
            'jadwal_perawatan_id' => $schedule->id,
            'performed_at' => now(),
        ]);

        $this->delete(route('kebun.activity.cancel', [$this->penanam, $activity]))
            ->assertRedirect(route('kebun.show', $this->penanam));
        $this->assertDatabaseMissing('aktivitas_perawatan', ['id' => $activity->id]);
        $this->assertDatabaseHas('jadwal_perawatan', ['id' => $schedule->id, 'status' => 'pending']);

        $activity = AktivitasPerawatan::create([
            'tanaman_ditanam_id' => $planting->id,
            'jadwal_perawatan_id' => $schedule->id,
            'performed_at' => now()->subWeek(),
        ]);
        $this->delete(route('kebun.activity.cancel', [$this->penanam, $activity]))
            ->assertStatus(409);
    }

    public function test_harvested_or_dead_plants_do_not_keep_future_pending_schedules(): void
    {
        $planting = $this->createPlanting();
        $planting->update(['status' => 'dipanen']);

        app(PerawatanScheduleService::class)->generateRollingHorizon($planting);

        $this->assertSame(0, $planting->jadwalPerawatan()->where('status', 'pending')->count());
    }

    public function test_harvest_is_blocked_until_the_estimate_and_can_be_undone_this_week(): void
    {
        $planting = $this->createPlanting();
        $planting->update(['ditanam_at' => today()->subDays(99)]);

        $this->post(route('kebun.harvest', [$this->penanam, $planting]))
            ->assertStatus(409);
        $this->assertSame('bertumbuh', $planting->fresh()->status);

        $planting->update(['ditanam_at' => today()->subDays(100)]);
        $this->post(route('kebun.harvest', [$this->penanam, $planting]))
            ->assertRedirect(route('kebun.show', $this->penanam));

        $this->assertSame('dipanen', $planting->fresh()->status);
        $this->assertDatabaseCount('panen', 1);
        $this->assertSame(0, $planting->jadwalPerawatan()->where('status', 'pending')->count());

        $this->delete(route('kebun.harvest.cancel', [$this->penanam, $planting]))
            ->assertRedirect(route('kebun.show', $this->penanam));
        $this->assertSame('bertumbuh', $planting->fresh()->status);
        $this->assertDatabaseCount('panen', 0);
    }

    public function test_harvested_plant_is_hard_deleted_after_two_months(): void
    {
        $planting = $this->createPlanting();
        $planting->update(['status' => 'dipanen']);
        Panen::create([
            'tanaman_ditanam_id' => $planting->id,
            'dipanen_at' => today()->subMonths(2)->subDay(),
        ]);
        $recentPlanting = $this->createPlanting();
        $recentPlanting->update(['status' => 'dipanen']);
        Panen::create([
            'tanaman_ditanam_id' => $recentPlanting->id,
            'dipanen_at' => today()->subMonth(),
        ]);

        $this->artisan('kebun:hapus-panen-lama')->assertSuccessful();

        $this->assertDatabaseMissing('tanaman_ditanam', ['id' => $planting->id]);
        $this->assertDatabaseMissing('panen', ['tanaman_ditanam_id' => $planting->id]);
        $this->assertDatabaseMissing('aktivitas_perawatan', ['tanaman_ditanam_id' => $planting->id]);
        $this->assertDatabaseHas('tanaman_ditanam', ['id' => $recentPlanting->id]);
    }

    private function createPlanting(): TanamanDitanam
    {
        $planting = TanamanDitanam::create([
            'penanam_id' => $this->penanam->id,
            'tanaman_id' => $this->tanaman->id,
            'ditanam_at' => today(),
            'quantity' => 1,
            'status' => 'bertumbuh',
        ]);

        app(PerawatanScheduleService::class)->generateRollingHorizon($planting);

        return $planting;
    }
}
