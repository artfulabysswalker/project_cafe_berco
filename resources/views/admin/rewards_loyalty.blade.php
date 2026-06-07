@extends('dashboard')

@section('page-title', 'Rewards & Loyalty')
@section('breadcrumb', 'Rewards')

@section('content')

<!-- Rewards Overview -->
<div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-bottom:14px">
    <div class="stat-card">
        <div class="stat-label"><i class="ti ti-crown"></i> Total Tier</div>
        <div class="stat-val">4</div>
        <div class="stat-diff up">✓ Member dapat manfaat eksklusif</div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><i class="ti ti-users"></i> Member Aktif</div>
        <div class="stat-val">128</div>
        <div class="stat-diff up">↑ 5 member baru minggu ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><i class="ti ti-coin"></i> Poin Beredar</div>
        <div class="stat-val">45.2k</div>
        <div class="stat-diff up">Rp 226k nilai tukar</div>
    </div>
</div>

<!-- Rewards Tier Settings -->
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-layers"></i> Tier Reward & Benefit</div>
    </div>
    <table class="tbl">
        <thead>
            <tr>
                <th>Tier</th>
                <th>Min. Spending</th>
                <th>Poin/Rp</th>
                <th>Benefit</th>
                <th>Member</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="tier-badge bronze">Bronze</span></td>
                <td>Rp 0</td>
                <td>1%</td>
                <td>
                    <div style="font-size:10px">
                        • Poin akumulasi<br/>
                        • Member discount 2%
                    </div>
                </td>
                <td style="font-weight:600">65</td>
                <td><button class="tbl-btn">Edit</button></td>
            </tr>
            <tr>
                <td><span class="tier-badge silver">Silver</span></td>
                <td>Rp 500k</td>
                <td>1.5%</td>
                <td>
                    <div style="font-size:10px">
                        • Poin akumulasi<br/>
                        • Member discount 5%<br/>
                        • Birthday voucher
                    </div>
                </td>
                <td style="font-weight:600">38</td>
                <td><button class="tbl-btn">Edit</button></td>
            </tr>
            <tr>
                <td><span class="tier-badge gold">Gold</span></td>
                <td>Rp 2M</td>
                <td>2%</td>
                <td>
                    <div style="font-size:10px">
                        • Poin akumulasi<br/>
                        • Member discount 8%<br/>
                        • Birthday voucher<br/>
                        • Priority service
                    </div>
                </td>
                <td style="font-weight:600">20</td>
                <td><button class="tbl-btn">Edit</button></td>
            </tr>
            <tr>
                <td><span class="tier-badge platinum">Platinum</span></td>
                <td>Rp 5M</td>
                <td>3%</td>
                <td>
                    <div style="font-size:10px">
                        • Poin akumulasi<br/>
                        • Member discount 10%<br/>
                        • Birthday voucher<br/>
                        • Priority + VIP lounge<br/>
                        • Exclusive menu access
                    </div>
                </td>
                <td style="font-weight:600">5</td>
                <td><button class="tbl-btn">Edit</button></td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Redemption Options -->
<div class="card" style="margin-top:12px">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-gift"></i> Opsi Penukaran Poin</div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:12px;padding:14px">
        <div class="redeem-card">
            <div style="font-size:12px;font-weight:600;color:#1a1a1a">Menu Diskon</div>
            <div style="font-size:24px;font-weight:700;color:#D4752C;margin:8px 0">100 Poin</div>
            <div style="font-size:10px;color:var(--color-text-secondary)">Diskon Rp 5.000 untuk pembelian apapun</div>
        </div>
        <div class="redeem-card">
            <div style="font-size:12px;font-weight:600;color:#1a1a1a">Cappuccino Gratis</div>
            <div style="font-size:24px;font-weight:700;color:#D4752C;margin:8px 0">200 Poin</div>
            <div style="font-size:10px;color:var(--color-text-secondary)">Tukar dengan 1 cup Cappuccino</div>
        </div>
        <div class="redeem-card">
            <div style="font-size:12px;font-weight:600;color:#1a1a1a">Voucher Spesial</div>
            <div style="font-size:24px;font-weight:700;color:#D4752C;margin:8px 0">500 Poin</div>
            <div style="font-size:10px;color:var(--color-text-secondary)">Diskon 30% 1x pembelian</div>
        </div>
        <div class="redeem-card">
            <div style="font-size:12px;font-weight:600;color:#1a1a1a">Merchandise</div>
            <div style="font-size:24px;font-weight:700;color:#D4752C;margin:8px 0">1000 Poin</div>
            <div style="font-size:10px;color:var(--color-text-secondary)">Merchandise eksklusif Berco Cafe</div>
        </div>
    </div>
</div>

<!-- Member Tier Distribution -->
<div class="card" style="margin-top:12px">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-chart-pie"></i> Distribusi Member Tier</div>
    </div>
    <div style="padding:14px">
        <div class="tier-dist-item">
            <span class="tier-badge bronze">Bronze</span>
            <div class="tier-bar-wrap">
                <div class="tier-bar" style="width:60%"></div>
            </div>
            <span style="font-size:11px;color:var(--color-text-secondary)">65 (50.8%)</span>
        </div>
        <div class="tier-dist-item">
            <span class="tier-badge silver">Silver</span>
            <div class="tier-bar-wrap">
                <div class="tier-bar silver" style="width:35%"></div>
            </div>
            <span style="font-size:11px;color:var(--color-text-secondary)">38 (29.7%)</span>
        </div>
        <div class="tier-dist-item">
            <span class="tier-badge gold">Gold</span>
            <div class="tier-bar-wrap">
                <div class="tier-bar gold" style="width:18%"></div>
            </div>
            <span style="font-size:11px;color:var(--color-text-secondary)">20 (15.6%)</span>
        </div>
        <div class="tier-dist-item">
            <span class="tier-badge platinum">Platinum</span>
            <div class="tier-bar-wrap">
                <div class="tier-bar platinum" style="width:5%"></div>
            </div>
            <span style="font-size:11px;color:var(--color-text-secondary)">5 (3.9%)</span>
        </div>
    </div>
</div>

<style>
    .stat-card {
        background: var(--color-background-primary);
        border: 0.5px solid var(--color-border-tertiary);
        border-radius: 9px;
        padding: 12px 14px;
    }
    .stat-label {
        font-size: 11px;
        color: var(--color-text-secondary);
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .stat-label i {
        color: #D4752C;
    }
    .stat-val {
        font-size: 22px;
        font-weight: 600;
        color: var(--color-text-primary);
    }
    .stat-diff.up {
        font-size: 10px;
        color: #27500A;
        margin-top: 4px;
    }
    .card {
        background: var(--color-background-primary);
        border: 0.5px solid var(--color-border-tertiary);
        border-radius: 9px;
        overflow: hidden;
    }
    .card-header {
        padding: 12px 14px;
        border-bottom: 0.5px solid var(--color-border-tertiary);
    }
    .card-title {
        font-size: 13px;
        font-weight: 500;
        color: var(--color-text-primary);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .card-title i {
        color: #D4752C;
    }
    .tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    .tbl th {
        padding: 8px 12px;
        text-align: left;
        color: var(--color-text-secondary);
        font-weight: 600;
        font-size: 10px;
        border-bottom: 0.5px solid var(--color-border-tertiary);
        background: var(--color-background-secondary);
    }
    .tbl td {
        padding: 10px 12px;
        border-bottom: 0.5px solid var(--color-border-tertiary);
        color: var(--color-text-primary);
    }
    .tbl tr:hover td {
        background: #FDF8F4;
    }
    .tbl-btn {
        padding: 4px 8px;
        border-radius: 4px;
        background: #D4752C;
        color: #fff;
        border: none;
        cursor: pointer;
        font-size: 10px;
        font-weight: 600;
    }
    .tier-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .tier-badge.bronze {
        background: #FFE8CC;
        color: #7D3D1F;
    }
    .tier-badge.silver {
        background: #E8E8E8;
        color: #4A4A4A;
    }
    .tier-badge.gold {
        background: #FFF9E6;
        color: #B39200;
    }
    .tier-badge.platinum {
        background: #E6F7FF;
        color: #003D99;
    }
    .redeem-card {
        background: var(--color-background-secondary);
        border: 0.5px solid var(--color-border-tertiary);
        border-radius: 7px;
        padding: 12px;
    }
    .tier-dist-item {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }
    .tier-bar-wrap {
        flex: 1;
        height: 12px;
        border-radius: 6px;
        background: var(--color-background-secondary);
        overflow: hidden;
    }
    .tier-bar {
        height: 100%;
        border-radius: 6px;
        background: #D4A574;
    }
    .tier-bar.silver {
        background: #B0B0B0;
    }
    .tier-bar.gold {
        background: #FFD700;
    }
    .tier-bar.platinum {
        background: #00A6FF;
    }
</style>

@endsection
