<!-- App Bottom Menu Monitoring -->
<div class="appBottomMenu" style="box-shadow: 0 -2px 12px rgba(0,0,0,0.06); border-top: 1px solid #e2e8f0;">
    <a href="/monitoring/dashboard" class="item {{ request()->is('monitoring/dashboard*') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="speedometer-outline"></ion-icon>
            <strong>Home</strong>
        </div>
    </a>
    <a href="/monitoring/layanan" class="item {{ request()->is('monitoring/layanan*') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="bus-outline"></ion-icon>
            <strong>Layanan</strong>
        </div>
    </a>
    <a href="/monitoring/jadwal" class="item {{ request()->is('monitoring/jadwal*') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="calendar-outline"></ion-icon>
            <strong>Jadwal</strong>
        </div>
    </a>
    <a href="/monitoring/presensi" class="item {{ request()->is('monitoring/presensi*') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="finger-print-outline"></ion-icon>
            <strong>Presensi</strong>
        </div>
    </a>
    <a href="/monitoring/penerimaan" class="item {{ request()->is('monitoring/penerimaan*') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="cash-outline"></ion-icon>
            <strong>Penerimaan</strong>
        </div>
    </a>
</div>
<!-- * App Bottom Menu Monitoring -->
