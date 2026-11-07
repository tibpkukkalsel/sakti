<style>
    .appBottomMenu .item .action-button {
        background: linear-gradient(135deg, #2A8BF2 0%, #1A73E8 100%) !important;
        box-shadow: 0 4px 12px rgba(42, 139, 242, 0.4) !important;
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
