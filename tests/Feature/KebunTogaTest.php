<?php

namespace Tests\Feature;

use App\Models\AturanPerawatan;
use App\Models\JadwalPerawatan;
use App\Models\Penanam;
use App\Models\Tanaman;
use App\Models\TanamanDitanam;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use App\Livewire\TanamanKatalog;
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

    public function test_public_can_add_a_plant_and_schedule_is_generated_from_its_rules(): void
    {
        $response = $this->post(route('tanaman-ditanam.store'), [
            'penanam_nama' => $this->penanam->nama,
            'access_code' => 'rahasia-kebun-123',
            'tanaman_id' => $this->tanaman->id,
            'ditanam_at' => today()->toDateString(),
            'quantity' => 2,
            'location' => 'Teras',
        ]);

        $planting = TanamanDitanam::firstOrFail();
        $response->assertRedirect(route('kebun.show', $this->penanam));
        $this->assertDatabaseHas('tanaman_ditanam', ['id' => $planting->id, 'quantity' => 2]);
        $this->assertGreaterThan(1, $planting->jadwalPerawatan()->count());
    }

    public function test_invalid_access_code_cannot_open_or_change_a_garden(): void
    {
        $planting = $this->createPlanting();
        $schedule = $planting->jadwalPerawatan()->firstOrFail();

        $this->post(route('kebun.access', $this->penanam), ['access_code' => 'wrong-code'])
            ->assertForbidden();

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
        $this->withSession(['garden_access.'.$this->penanam->id => true])
            ->patch(route('kebun.complete', [$this->penanam, $schedule]))
            ->assertRedirect(route('kebun.show', $this->penanam));

        $this->assertDatabaseHas('jadwal_perawatan', ['id' => $schedule->id, 'status' => 'completed']);
        $this->assertDatabaseCount('aktivitas_perawatan', 1);

        $this->withSession(['garden_access.'.$this->penanam->id => true])
            ->patch(route('kebun.complete', [$this->penanam, $schedule]));
        $this->assertDatabaseCount('aktivitas_perawatan', 1);
    }

    public function test_dashboard_and_private_api_show_only_the_growers_schedule(): void
    {
        $planting = $this->createPlanting();
        $this->withSession(['garden_access.'.$this->penanam->id => true])
            ->get(route('kebun.show', $this->penanam))
            ->assertOk()
            ->assertSee('Jadwal perawatan')
            ->assertSee('Perlu dilakukan hari ini');

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

        $admin = User::factory()->create(['email' => 'admin@example.test']);
        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->getJson('/api/v1/admin/tanaman')->assertOk();
    }

    public function test_admin_can_manage_catalog_growers_and_care_rules(): void
    {
        $this->actingAs(User::factory()->create());

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

        $this->post(route('admin.resources.store', 'penamans'), [
            'nama' => 'Penanam Admin',
            'access_code' => 'kode-aman-123',
        ])->assertSessionHas('new_access_code', 'kode-aman-123');
        $grower = Penanam::where('nama', 'Penanam Admin')->firstOrFail();
        $this->assertTrue(Hash::check('kode-aman-123', $grower->access_code_hash));

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
        $this->actingAs(User::factory()->create());

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

        app(\App\Services\PerawatanScheduleService::class)->generateRollingHorizon($planting);

        $this->assertGreaterThan(0, $before);
        $this->assertSame($before, $planting->jadwalPerawatan()->count());
    }

    public function test_harvested_or_dead_plants_do_not_keep_future_pending_schedules(): void
    {
        $planting = $this->createPlanting();
        $planting->update(['status' => 'dipanen']);

        app(\App\Services\PerawatanScheduleService::class)->generateRollingHorizon($planting);

        $this->assertSame(0, $planting->jadwalPerawatan()->where('status', 'pending')->count());
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

        app(\App\Services\PerawatanScheduleService::class)->generateRollingHorizon($planting);

        return $planting;
    }
}
