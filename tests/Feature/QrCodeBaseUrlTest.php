<?php

namespace Tests\Feature;

use App\Models\Meja;
use App\Models\Role;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * QR Code hanya berguna kalau URL di dalamnya bisa dibuka HP pelanggan dari
 * Wi-Fi kafe. "localhost" di HP mengarah ke HP itu sendiri, jadi harus dihindari.
 */
class QrCodeBaseUrlTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        return User::factory()->create([
            'id_role' => Role::firstOrCreate(['role_name' => 'Admin'])->id_role,
        ]);
    }

    public function test_qr_uses_the_configured_base_url_when_set()
    {
        config(['qrcode.base_url' => 'http://192.168.1.100:8000']);

        $table = Meja::factory()->create();
        $service = app(QrCodeService::class);
        $qr = $service->generateForTable($table->id_meja, 24);

        $url = $service->scanUrlFor($qr);

        $this->assertStringStartsWith('http://192.168.1.100:8000/qrcode/scan/', $url);
        $this->assertStringNotContainsString('localhost', $url);
    }

    public function test_base_url_does_not_leak_into_other_generated_routes()
    {
        config(['qrcode.base_url' => 'http://192.168.1.100:8000']);

        $table = Meja::factory()->create();
        app(QrCodeService::class)->generateForTable($table->id_meja, 24);

        // route() biasa di request yang sama harus kembali ke host aslinya
        $this->assertStringStartsWith('http://localhost', route('menu.index'));
    }

    public function test_qr_falls_back_to_the_request_host_when_base_url_is_empty()
    {
        config(['qrcode.base_url' => '']);

        $table = Meja::factory()->create();
        $service = app(QrCodeService::class);
        $qr = $service->generateForTable($table->id_meja, 24);

        // Tanpa base_url, URL mengikuti host request yang sedang aktif
        $this->assertStringStartsWith('http://', $service->scanUrlFor($qr));
    }

    public function test_trailing_slash_in_base_url_does_not_double_up()
    {
        config(['qrcode.base_url' => 'http://192.168.1.100:8000/']);

        $table = Meja::factory()->create();
        $service = app(QrCodeService::class);
        $qr = $service->generateForTable($table->id_meja, 24);

        $url = $service->scanUrlFor($qr);

        $this->assertStringStartsWith('http://192.168.1.100:8000/qrcode/', $url);
        $this->assertStringNotContainsString('//qrcode', $url);
    }

    public function test_generated_png_encodes_the_configured_base_url()
    {
        config(['qrcode.base_url' => 'http://192.168.1.100:8000']);

        $table = Meja::factory()->create();
        $qr = app(QrCodeService::class)->generateForTable($table->id_meja, 24);

        $this->assertTrue(app(QrCodeService::class)->imageExists($qr));
        $this->assertStringStartsWith(
            'http://192.168.1.100:8000/',
            app(QrCodeService::class)->payloadFor($qr)
        );
    }
}
