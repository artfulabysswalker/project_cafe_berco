<?php

use App\Exceptions\TableQrCodeException;
use App\Models\Meja;
use App\Models\Role;
use App\Models\TableQrCode;
use App\Models\User;
use App\Services\QrCodeService;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

function adminUser(): User
{
    $role = Role::firstOrCreate(['role_name' => 'Admin']);

    return User::factory()->create(['id_role' => $role->id_role]);
}

beforeEach(function () {
    Storage::fake('public');
    config()->set('qrcode.disk', 'public');
});

test('generating a qr code persists an active record and png', function () {
    $admin = adminUser();
    $meja = Meja::factory()->create();

    $this->actingAs($admin);

    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja);

    expect($qr->token)->toBeString()->not->toBeEmpty();
    expect($qr->version)->toBe(1);
    expect($qr->is_active)->toBeTrue();
    expect($qr->generated_by)->toBe($admin->id_user);
    expect($qr->expired_at)->not->toBeNull();

    Storage::disk('public')->assertExists($qr->image_path);
});

test('generate honours a custom expiry window', function () {
    $this->freezeTime();
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();

    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja, 6);

    expect($qr->expired_at->timestamp)->toBe(now()->addHours(6)->timestamp);
});

test('zero expiry means the qr never expires', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();

    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja, 0);

    expect($qr->expired_at)->toBeNull();
    expect($qr->isExpired())->toBeFalse();
});

test('generating for an unknown table throws', function () {
    app(QrCodeService::class)->generateForTable(99999);
})->throws(TableQrCodeException::class, 'Meja tidak ditemukan');

test('validate token accepts a live token', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja);

    $resolved = app(QrCodeService::class)->validateToken($qr->token);

    expect($resolved->id_qr_code)->toBe($qr->id_qr_code);
});

test('validate token rejects unknown revoked and expired tokens', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $service = app(QrCodeService::class);

    $live = $service->generateForTable($meja->id_meja);

    $revoked = $service->generateForTable($meja->id_meja);
    $service->revoke($revoked->token);

    $expired = $service->generateForTable($meja->id_meja, 1);
    $expired->forceFill(['expired_at' => now()->subMinute()])->save();

    $expectations = [
        'deadbeef-dead-beef-dead-beefdeadbeef' => 'QR Code tidak valid',
        $revoked->token => 'QR Code telah dinonaktifkan',
        $expired->token => 'QR Code telah kedaluwarsa',
    ];

    foreach ($expectations as $token => $message) {
        try {
            $service->validateToken($token);
            $this->fail('Expected TableQrCodeException for '.$token);
        } catch (TableQrCodeException $e) {
            expect($e->getMessage())->toBe($message);
        }
    }

    expect($live->token)->not->toBeEmpty();
});

test('revoke marks the record inactive', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $service = app(QrCodeService::class);
    $qr = $service->generateForTable($meja->id_meja);

    $service->revoke($qr->token);

    expect($qr->fresh()->is_active)->toBeFalse();
});

test('revoke throws for an unknown token', function () {
    app(QrCodeService::class)->revoke('nope');
})->throws(TableQrCodeException::class, 'QR Code tidak ditemukan');

test('regenerate revokes old tokens and bumps the version', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $service = app(QrCodeService::class);

    $first = $service->generateForTable($meja->id_meja);
    $second = $service->regenerate($meja->id_meja);

    expect($first->fresh()->is_active)->toBeFalse();
    expect($second->is_active)->toBeTrue();
    expect($second->version)->toBe(2);
    expect($second->token)->not->toBe($first->token);
    expect(TableQrCode::forTable($meja->id_meja)->valid()->count())->toBe(1);
});

test('payload can be encrypted instead of signed and round-trips', function () {
    config()->set('qrcode.encode_as_signed_url', false);
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();

    $service = app(QrCodeService::class);
    $qr = $service->generateForTable($meja->id_meja);

    $payload = $service->decryptPayload(Crypt::encryptString(json_encode([
        'table_id' => $meja->id_meja,
        'token' => $qr->token,
        'version' => 1,
        'issued_at' => now()->toIso8601String(),
        'expired_at' => $qr->expired_at->toIso8601String(),
    ])));

    expect($payload['table_id'])->toBe($meja->id_meja);
    expect($payload['token'])->toBe($qr->token);
    expect($payload)->toHaveKeys(['table_id', 'token', 'version', 'issued_at', 'expired_at']);
});

test('decrypting a tampered payload throws', function () {
    app(QrCodeService::class)->decryptPayload('not-a-valid-cipher-text');
})->throws(TableQrCodeException::class, 'Tidak dapat mendekripsi payload QR Code');

test('admin can view the tables index', function () {
    Meja::factory()->count(3)->create();

    $this->actingAs(adminUser())
        ->get(route('admin.tables.index'))
        ->assertOk();
});

test('non admins cannot reach the qr code pages', function () {
    $meja = Meja::factory()->create();
    $kasir = User::factory()->create([
        'id_role' => Role::firstOrCreate(['role_name' => 'Kasir'])->id_role,
    ]);

    $this->actingAs($kasir)
        ->get(route('admin.tables.qrcode', $meja->id_meja))
        ->assertForbidden();
});

test('admin can generate and revoke through the endpoints', function () {
    $admin = adminUser();
    $meja = Meja::factory()->create();

    $this->actingAs($admin)
        ->post(route('admin.tables.qrcode.generate', $meja->id_meja), ['expires_in_hours' => 12])
        ->assertRedirect(route('admin.tables.qrcode', $meja->id_meja));

    expect(TableQrCode::forTable($meja->id_meja)->valid()->count())->toBe(1);

    $this->actingAs($admin)
        ->post(route('admin.tables.qrcode.revoke', $meja->id_meja))
        ->assertRedirect(route('admin.tables.qrcode', $meja->id_meja));

    expect(TableQrCode::forTable($meja->id_meja)->valid()->count())->toBe(0);
});

test('generate endpoint validates the expiry window', function () {
    $meja = Meja::factory()->create();

    $this->actingAs(adminUser())
        ->post(route('admin.tables.qrcode.generate', $meja->id_meja), ['expires_in_hours' => 9999])
        ->assertSessionHasErrors('expires_in_hours');
});

test('the customer scan view renders the table details', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create(['nama_meja' => 'VIP 7']);
    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja);

    $url = URL::temporarySignedRoute('customer.qrcode.scan', $qr->expired_at, ['token' => $qr->token]);

    $this->get($url)
        ->assertOk()
        ->assertSee('VIP 7')
        ->assertSee(route('menu.index'))
        ->assertSee('Berlaku sampai');
});

test('the admin tables index lists every meja with its qr state', function () {
    Meja::factory()->create(['nama_meja' => 'Meja Alpha']);
    Meja::factory()->create(['nama_meja' => 'Meja Beta']);

    $this->actingAs(adminUser())
        ->get(route('admin.tables.index'))
        ->assertOk()
        ->assertSee('Meja Alpha')
        ->assertSee('Meja Beta')
        ->assertSee('Tidak Aktif');
});

test('the qr page shows status and actions for a generated code', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja);

    $this->get(route('admin.tables.qrcode', $meja->id_meja))
        ->assertOk()
        ->assertSee('Aktif')
        ->assertSee($qr->token)
        ->assertSee('Download PNG')
        ->assertSee('Revoke QR Code');
});

test('the qr page offers generate when no code exists yet', function () {
    $meja = Meja::factory()->create();

    $this->actingAs(adminUser())
        ->get(route('admin.tables.qrcode', $meja->id_meja))
        ->assertOk()
        ->assertSee('Generate QR Code')
        ->assertDontSee('Regenerate (Revoke Lama)');
});

test('the qr page exposes the payload for verification', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja);

    $response = $this->get(route('admin.tables.qrcode', $meja->id_meja));

    $response->assertOk()
        ->assertSee('Isi QR (Payload)')
        ->assertSee('signature=')
        ->assertSee('Salin Payload')
        ->assertSee('Buka Halaman Scan');

    // Payload yang ditampilkan harus persis sama dengan yang dipindai customer
    expect(app(QrCodeService::class)->scanUrlFor($qr))
        ->toBe(app(QrCodeService::class)->payloadFor($qr));
});

test('the payload matches the url the customer actually scans', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja);

    $scanUrl = app(QrCodeService::class)->payloadFor($qr);

    // URL yang tampil di halaman admin harus bisa dipindai dan berhasil
    $this->get($scanUrl)
        ->assertOk()
        ->assertSee($meja->nama_meja);
});

test('an encrypted payload page shows ciphertext and still scans', function () {
    config()->set('qrcode.encode_as_signed_url', false);
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja);

    $payload = app(QrCodeService::class)->payloadFor($qr);

    // Tidak boleh memuat ID meja dalam bentuk polos
    expect($payload)->not->toContain('table_id')
        ->not->toContain($meja->nama_meja)
        ->not->toContain('&');

    // -Rhanya ciphertext base64
    expect($payload)->toMatch('/^[A-Za-z0-9+\/=]+$/');

    $this->get(route('admin.tables.qrcode', $meja->id_meja))
        ->assertOk()
        ->assertSee('payload terenkripsi (AES)');

    // Ciphertext yang ditampilkan harus tetap bisa didekripsi
    $this->get(route('customer.qrcode.scan-payload', ['payload' => $payload]))
        ->assertOk()
        ->assertSee($meja->nama_meja);
});

test('the qr page reflects a revoked code', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $service = app(QrCodeService::class);
    $qr = $service->generateForTable($meja->id_meja);
    $service->revoke($qr->token);

    $this->get(route('admin.tables.qrcode', $meja->id_meja))
        ->assertOk()
        ->assertSee('Revoked')
        ->assertSee('Regenerate (Revoke Lama)');
});

test('the qr page reflects an expired code', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja, 1);
    $qr->forceFill(['expired_at' => now()->subMinute()])->save();

    $this->get(route('admin.tables.qrcode', $meja->id_meja))
        ->assertOk()
        ->assertSee('Expired');
});

test('scanning an unknown token returns 404', function () {
    $url = URL::temporarySignedRoute(
        'customer.qrcode.scan',
        now()->addHour(),
        ['token' => (string) Str::uuid()]
    );

    $this->get($url)->assertNotFound();
});

test('repeated regenerates keep bumping the version', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $service = app(QrCodeService::class);

    $service->generateForTable($meja->id_meja);
    $second = $service->regenerate($meja->id_meja);
    $third = $service->regenerate($meja->id_meja);

    expect($second->version)->toBe(2);
    expect($third->version)->toBe(3);
    expect(TableQrCode::forTable($meja->id_meja)->count())->toBe(3);
    expect(TableQrCode::forTable($meja->id_meja)->valid()->count())->toBe(1);
});

test('version numbers never collide within a table', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $service = app(QrCodeService::class);

    $service->generateForTable($meja->id_meja);
    $service->regenerate($meja->id_meja);
    $service->regenerate($meja->id_meja);

    $versions = TableQrCode::forTable($meja->id_meja)->pluck('version')->all();

    expect(array_unique($versions))->toHaveCount(count($versions));
    expect($versions)->toEqualCanonicalizing([1, 2, 3]);
});

test('failed rendering leaves no orphan png behind', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();

    config()->set('qrcode.magick_path', '/nonexistent/magick');
    config()->set('qrcode.magick_fallbacks', ['/nonexistent/magick']);

    try {
        app(QrCodeService::class)->generateForTable($meja->id_meja);
        $this->fail('Expected TableQrCodeException');
    } catch (TableQrCodeException $e) {
        expect($e->getMessage())->toContain('Gagal menyimpan file QR Code');
    }

    // Tidak ada record tersimpan
    expect(TableQrCode::forTable($meja->id_meja)->count())->toBe(0);

    // Tidak ada file PNG tersisa di disk
    $dir = Storage::disk('public')->path('qrcodes/'.$meja->id_meja);
    $files = is_dir($dir) ? glob($dir.'/*.png') : [];
    expect($files)->toBeEmpty();
});

test('generation fails loudly when imagemagick is unavailable', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();

    config()->set('qrcode.magick_path', '/nonexistent/magick');
    config()->set('qrcode.magick_fallbacks', ['/nonexistent/magick']);

    $this->post(route('admin.tables.qrcode.generate', $meja->id_meja))
        ->assertRedirect()
        ->assertSessionHasErrors('error');
});

test('revoke removes the png from disk', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $service = app(QrCodeService::class);
    $qr = $service->generateForTable($meja->id_meja);

    Storage::disk('public')->assertExists($qr->image_path);

    $service->revoke($qr->token, deleteImage: true);

    Storage::disk('public')->assertMissing($qr->image_path);
});

test('regenerate cleans up the previous png', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $service = app(QrCodeService::class);

    $first = $service->generateForTable($meja->id_meja);
    $second = $service->regenerate($meja->id_meja);

    Storage::disk('public')->assertMissing($first->image_path);
    Storage::disk('public')->assertExists($second->image_path);
});

test('the stored png really encodes the signed url', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja);

    $expectedUrl = URL::temporarySignedRoute(
        'customer.qrcode.scan',
        $qr->expired_at,
        ['token' => $qr->token]
    );

    $svg = (new Writer(new ImageRenderer(new RendererStyle(256, 4), new SvgImageBackEnd)))
        ->writeString(
            $expectedUrl,
            Encoder::DEFAULT_BYTE_MODE_ENCODING,
            ErrorCorrectionLevel::M()
        );

    $tmpSvg = tempnam(sys_get_temp_dir(), 'qr').'.svg';
    $tmpPng = tempnam(sys_get_temp_dir(), 'qr').'.png';
    file_put_contents($tmpSvg, $svg);

    $magick = config('qrcode.magick_path');
    exec(escapeshellarg($magick).' convert -density 300 '.escapeshellarg($tmpSvg).' -background none '.escapeshellarg($tmpPng).' 2>&1', $out, $code);

    $storedPath = Storage::disk('public')->path($qr->image_path);

    // %# = signature berbasis piksel, jadi perbandingan tahan beda metadata PNG
    exec(escapeshellarg($magick).' identify -format "%#" '.escapeshellarg($storedPath), $storedSig, $storedCode);
    exec(escapeshellarg($magick).' identify -format "%#" '.escapeshellarg($tmpPng), $refSig, $refCode);

    unlink($tmpSvg);
    unlink($tmpPng);

    expect($code)->toBe(0, 'ImageMagick gagal mengonversi SVG');
    expect($storedCode)->toBe(0);
    expect($refCode)->toBe(0);
    expect(trim(implode('', $storedSig)))
        ->toBe(trim(implode('', $refSig)), 'PNG yang tersimpan berbeda dari QR atas signed URL');
});

test('image path falls back to the token layout when not stored', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja);
    $service = app(QrCodeService::class);

    expect($service->imagePath($qr))->toBe($qr->image_path);

    $qr->forceFill(['image_path' => null])->save();

    expect($service->imagePath($qr->fresh()))->toBe("qrcodes/{$meja->id_meja}/{$qr->token}.png");
});

test('imageExists reports whether the file is on disk', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $service = app(QrCodeService::class);
    $qr = $service->generateForTable($meja->id_meja);

    expect($service->imageExists($qr))->toBeTrue();

    Storage::disk('public')->delete($qr->image_path);

    expect($service->imageExists($qr))->toBeFalse();
});

test('the qr page reports a missing file instead of a broken image', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja);

    Storage::disk('public')->delete($qr->image_path);

    $this->get(route('admin.tables.qrcode', $meja->id_meja))
        ->assertOk()
        ->assertSee('File QR hilang');
});

test('download returns the png attachment', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja);

    $this->get(route('admin.tables.qrcode.download', $meja->id_meja))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png')
        ->assertHeader('Content-Disposition', 'attachment; filename="qr-meja-'.str()->slug($meja->nama_meja).'-'.$qr->token.'.png"');
});

test('the image endpoint streams the png inline for admins', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja);

    $response = $this->get(route('admin.tables.qrcode.image', $meja->id_meja));

    $response->assertOk()
        ->assertHeader('Content-Type', 'image/png')
        ->assertHeader('Content-Disposition', 'inline; filename="'.$qr->token.'.png"')
        ->assertHeader('Cache-Control', 'no-store, private');

    expect($response->getContent() ?? $response->streamedContent())->not->toBeEmpty();
});

test('the image endpoint requires a signed in admin', function () {
    $meja = Meja::factory()->create();

    $this->get(route('admin.tables.qrcode.image', $meja->id_meja))
        ->assertRedirect(route('login'));
});

test('the image endpoint forbids non admins', function () {
    $meja = Meja::factory()->create();
    $kasir = User::factory()->create([
        'id_role' => Role::firstOrCreate(['role_name' => 'Kasir'])->id_role,
    ]);

    $this->actingAs($kasir)
        ->get(route('admin.tables.qrcode.image', $meja->id_meja))
        ->assertForbidden();
});

test('the image endpoint 404s when the file is gone', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja);

    Storage::disk('public')->delete($qr->image_path);

    $this->get(route('admin.tables.qrcode.image', $meja->id_meja))->assertNotFound();
});

test('the image endpoint 404s when no qr was generated', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();

    $this->get(route('admin.tables.qrcode.image', $meja->id_meja))->assertNotFound();
});

test('download fails gracefully when nothing was generated', function () {
    $meja = Meja::factory()->create();

    $this->actingAs(adminUser())
        ->get(route('admin.tables.qrcode.download', $meja->id_meja))
        ->assertRedirect()
        ->assertSessionHasErrors('error');
});

test('a signed scan url resolves to the table', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja);

    $url = URL::temporarySignedRoute(
        'customer.qrcode.scan',
        $qr->expired_at,
        ['token' => $qr->token]
    );

    $this->get($url)
        ->assertOk()
        ->assertSee($meja->nama_meja);
});

test('an unsigned scan url is rejected', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja);

    $this->get(route('customer.qrcode.scan', ['token' => $qr->token]))
        ->assertForbidden();
});

test('an encrypted payload url resolves to the table', function () {
    config()->set('qrcode.encode_as_signed_url', false);
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $service = app(QrCodeService::class);
    $qr = $service->generateForTable($meja->id_meja);

    $payload = Crypt::encryptString(json_encode([
        'table_id' => $meja->id_meja,
        'token' => $qr->token,
        'version' => 1,
        'issued_at' => now()->toIso8601String(),
        'expired_at' => $qr->expired_at->toIso8601String(),
    ]));

    $this->get(route('customer.qrcode.scan-payload', ['payload' => $payload]))
        ->assertOk()
        ->assertSee($meja->nama_meja);
});

test('a tampered payload url is rejected', function () {
    config()->set('qrcode.encode_as_signed_url', false);

    $this->get(route('customer.qrcode.scan-payload', ['payload' => 'tampered-value']))
        ->assertNotFound();

    $this->get(route('customer.qrcode.scan-payload'))
        ->assertNotFound();
});

test('a tampered signature on the token url is rejected', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja);

    $url = URL::temporarySignedRoute(
        'customer.qrcode.scan',
        $qr->expired_at,
        ['token' => $qr->token]
    );

    // Tukar signature dengan signature milik token lain
    $otherUrl = URL::temporarySignedRoute(
        'customer.qrcode.scan',
        $qr->expired_at,
        ['token' => (string) Str::uuid()]
    );
    parse_str(parse_url($otherUrl, PHP_URL_QUERY), $otherQuery);

    parse_str(parse_url($url, PHP_URL_QUERY), $query);
    $query['signature'] = $otherQuery['signature'];

    $this->get('/qrcode/scan/'.$qr->token.'?'.http_build_query($query))
        ->assertForbidden();
});

test('a swapped table id in the signed url cannot be forged', function () {
    $this->actingAs(adminUser());
    $mejaA = Meja::factory()->create();
    $mejaB = Meja::factory()->create();
    $qrA = app(QrCodeService::class)->generateForTable($mejaA->id_meja);
    $qrB = app(QrCodeService::class)->generateForTable($mejaB->id_meja);

    // Signature milik QR-B tidak boleh berlaku untuk QR-A
    $urlB = URL::temporarySignedRoute('customer.qrcode.scan', $qrB->expired_at, ['token' => $qrB->token]);
    parse_str(parse_url($urlB, PHP_URL_QUERY), $qB);

    $this->get('/qrcode/scan/'.$qrA->token.'?'.http_build_query($qB))
        ->assertForbidden();
});

test('an expired signed url is rejected by the signature check', function () {
    config()->set('qrcode.encode_as_signed_url', true);
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $qr = app(QrCodeService::class)->generateForTable($meja->id_meja, 24);

    $url = URL::temporarySignedRoute('customer.qrcode.scan', $qr->expired_at, ['token' => $qr->token]);

    $this->travel(25)->hours();

    $this->get($url)->assertForbidden();
});

test('a revoked token can no longer be scanned', function () {
    $this->actingAs(adminUser());
    $meja = Meja::factory()->create();
    $service = app(QrCodeService::class);
    $qr = $service->generateForTable($meja->id_meja);

    $url = URL::temporarySignedRoute(
        'customer.qrcode.scan',
        $qr->expired_at,
        ['token' => $qr->token]
    );

    $service->revoke($qr->token);

    $this->get($url)->assertNotFound();
});
