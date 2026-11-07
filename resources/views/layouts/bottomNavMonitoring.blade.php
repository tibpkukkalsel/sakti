<!-- App Bottom Menu -->
<div class="appBottomMenu">
    <a href="/monitoring/dashboard" class="item {{ request()->is('monitoring/dashboard*') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="home-outline"></ion-icon>
            <strong>Home</strong>
        </div>
    </a>
    <a href="/monitoring/jadwal" class="item {{ request()->is('monitoring/jadwal*') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="calendar-outline"></ion-icon>
            <strong>Jadwal</strong>
        </div>
    </a>

    <a href="/monitoring/layanan" class="item {{ request()->is('monitoring/layanan*') ? 'active' : '' }}">
        <div class="col">
            <div class="action-button large">
                <ion-icon name="bus-outline"></ion-icon>
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
