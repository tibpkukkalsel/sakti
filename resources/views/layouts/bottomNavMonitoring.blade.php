<style>
    /* Footer Menu Aktif Berwarna Navy Khas Akun Monitoring */
    .appBottomMenu .item.active i.icon,
    .appBottomMenu .item.active ion-icon,
    .appBottomMenu .item.active strong {
        color: #0c234b !important;
        font-weight: 700 !important;
    }

    /* Tombol Tengah (Home) dengan Gradasi Navy */
    .appBottomMenu .item .action-button {
        background: linear-gradient(135deg, #1E6FD9 0%, #0c234b 100%) !important;
        box-shadow: 0 4px 12px rgba(12, 35, 75, 0.4) !important;
    }
    .appBottomMenu .item.active .action-button {
        background: linear-gradient(135deg, #15438C 0%, #08162d 100%) !important;
        box-shadow: 0 4px 14px rgba(8, 22, 45, 0.55) !important;
    }
    .appBottomMenu .item .action-button ion-icon,
    .appBottomMenu .item.active .action-button ion-icon,
    .appBottomMenu .item .action-button i.icon,
    .appBottomMenu .item.active .action-button i.icon {
        color: #ffffff !important;
        font-size: 28px !important;
        display: inline-block !important;
        margin: 0 !important;
        line-height: 1 !important;
    }
</style>

<!-- App Bottom Menu -->
<div class="appBottomMenu">
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

    <a href="/monitoring/dashboard" class="item {{ request()->is('monitoring/dashboard*') ? 'active' : '' }}">
        <div class="col">
            <div class="action-button large">
                <ion-icon name="{{ request()->is('monitoring/dashboard*') ? 'home' : 'home-outline' }}"></ion-icon>
            </div>
        </div>
    </a>

    <a href="/monitoring/penerimaan" class="item {{ request()->is('monitoring/penerimaan*') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="cash-outline"></ion-icon>
            <strong>Penerimaan</strong>
        </div>
    </a>
    <a href="/monitoring/profile" class="item {{ request()->is('monitoring/profile*') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="people-outline" role="img" class="md hydrated" aria-label="people outline"></ion-icon>
            <strong>Profile</strong>
        </div>
    </a>
</div>
<!-- * App Bottom Menu -->
