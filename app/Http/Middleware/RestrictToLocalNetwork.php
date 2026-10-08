<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Batasi akses ke jaringan lokal (Wi-Fi kafe).
 *
 * Dipasang hanya di route pelanggan. Route admin tetap bebas diakses dari
 * mana saja supaya dashboard bisa dikelola dari luar bila diperlukan.
 *
 * Peringatan keamanan: alamat IP di sini diambil dari REMOTE_ADDR. Laravel tidak
 * memercayai X-Forwarded-For selama tidak ada proxy tepercaya yang dikonfigurasi,
 * jadi header itu tidak bisa dipalsukan klien secara langsung. Tetap saja, ini
 * hanya mencegah akses tidak sengaja lewat internet, bukan serangan yang
 * disengaja. Andalkan juga aturan firewall di router.
 */
class RestrictToLocalNetwork
{
    public function handle(Request $request, Closure $next)
    {
        if ($this->isLocalAddress($request->ip())) {
            return $next($request);
        }

        abort(403, 'Aplikasi ini hanya dapat diakses melalui Wi-Fi Berco Cafe.');
    }

    /**
     * Cek apakah sebuah alamat IPv4/IPv6 berada di dalam jaringan lokal.
     */
    protected function isLocalAddress(?string $ip): bool
    {
        if (empty($ip) || filter_var($ip, FILTER_VALIDATE_IP) === false) {
            // alamat kosong atau tidak valid tidak boleh dianggap lokal
            return false;
        }

        $address = $this->toBinary($ip);
        if ($address === null) {
            return false;
        }

        foreach ($this->allowedCidrs() as $cidr) {
            if ($this->matches($address, $cidr)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Rentang yang dianggap "lokal". Bisa ditambah lewat config('network.local_cidrs')
     * bila router kafe memakai subnet tidak standar.
     *
     * @return array<int, array{0: string, 1: string, 2: int}>
     */
    protected function allowedCidrs(): array
    {
        $defaults = [
            '127.0.0.0/8',      // loopback IPv4
            '10.0.0.0/8',       // RFC 1918
            '172.16.0.0/12',    // RFC 1918
            '192.168.0.0/16',   // RFC 1918
            '100.64.0.0/10',    // CGNAT, dipakai sebagian AP kafe
            '::1/128',          // loopback IPv6
            'fc00::/7',         // IPv6 unique-local
            'fe80::/10',        // IPv6 link-local
        ];

        $extra = array_filter((array) config('network.extra_local_cidrs', []));

        return array_map([$this, 'parseCidr'], array_merge($defaults, $extra));
    }

    /**
     * @return array{0: string, 1: string, 2: int}|null
     */
    protected function parseCidr(string $cidr): ?array
    {
        $parts = explode('/', $cidr, 2);
        $address = $this->toBinary($parts[0]);

        if ($address === null) {
            return null;
        }

        $bits = isset($parts[1]) ? (int) $parts[1] : strlen($address) * 8;

        return [$address, $parts[0], $bits];
    }

    /**
     * Bandingkan dua alamat biner dengan Track Prefix Style.
     */
    protected function matches(string $address, ?array $cidr): bool
    {
        if ($cidr === null) {
            return false;
        }

        [$network, , $bits] = $cidr;

        if (strlen($network) !== strlen($address)) {
            // beda keluarga IP (v4 vs v6), tidak mungkin sama
            return false;
        }

        if ($bits <= 0) {
            return true;
        }

        $fullBytes = intdiv($bits, 8);
        $remainder = $bits % 8;

        if ($fullBytes > 0 && strncmp($address, $network, $fullBytes) !== 0) {
            return false;
        }

        if ($remainder === 0) {
            return true;
        }

        $mask = chr((0xFF << (8 - $remainder)) & 0xFF);

        return ($address[$fullBytes] & $mask) === ($network[$fullBytes] & $mask);
    }

    /**
     * Ubah alamat IP menjadi representasi biner per byte.
     */
    protected function toBinary(string $ip): ?string
    {
        $packed = @inet_pton($ip);

        return $packed === false ? null : $packed;
    }
}
