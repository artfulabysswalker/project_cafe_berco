<?php

namespace Tests\Feature;

use App\Models\Meja;
use App\Models\Role;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LocalNetworkAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: string}>
     */
    public static function localAddressProvider(): array
    {
        return [
            'loopback' => ['127.0.0.1'],
            'rfc1918 10/8' => ['10.0.0.7'],
            'rfc1918 172.16/12' => ['172.16.4.9'],
            'rfc1918 192.168/16' => ['192.168.1.100'],
            'cgnat 100.64/10' => ['100.64.0.1'],
            'ipv6 loopback' => ['::1'],
            'ipv6 unique local' => ['fd12:3456::1'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function publicAddressProvider(): array
    {
        return [
            'public ipv4' => ['8.8.8.8'],
            'public ipv4 far' => ['203.0.113.45'],
            'public ipv6' => ['2001:4860:4860::8888'],
        ];
    }

    protected function makeGuestUser(): User
    {
        return User::factory()->create(['is_guest' => true]);
    }

    #[DataProvider('localAddressProvider')]
    public function test_customer_menu_is_reachable_from_local_addresses(string $ip)
    {
        $this->makeGuestUser();

        $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->get('/menu')
            ->assertOk();
    }

    #[DataProvider('publicAddressProvider')]
    public function test_customer_menu_is_blocked_from_public_addresses(string $ip)
    {
        $this->makeGuestUser();

        $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->get('/menu')
            ->assertForbidden();
    }

    public function test_cart_and_checkout_are_blocked_from_public_addresses()
    {
        $this->makeGuestUser();

        foreach (['/cart', '/checkout'] as $uri) {
            $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])
                ->get($uri)
                ->assertForbidden();
        }
    }

    public function test_qr_scan_is_blocked_from_public_addresses()
    {
        $table = Meja::factory()->create();
        $qr = app(QrCodeService::class)->generateForTable($table->id_meja);

        $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])
            ->get(app(QrCodeService::class)->scanUrlFor($qr))
            ->assertForbidden();
    }

    public function test_qr_scan_works_from_local_address()
    {
        $table = Meja::factory()->create();
        $qr = app(QrCodeService::class)->generateForTable($table->id_meja);

        $this->withServerVariables(['REMOTE_ADDR' => '192.168.1.55'])
            ->get(app(QrCodeService::class)->scanUrlFor($qr))
            ->assertOk();
    }

    public function test_admin_routes_stay_reachable_from_public_addresses()
    {
        $admin = User::factory()->create([
            'is_guest' => false,
            'id_role' => Role::firstOrCreate(['role_name' => 'Admin'])->id_role,
        ]);

        $this->actingAs($admin)
            ->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])
            ->get('/admin/tables')
            ->assertOk();
    }

    public function test_staff_login_page_stays_reachable_from_public_addresses()
    {
        $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])
            ->get('/staff-login')
            ->assertOk();
    }
}
