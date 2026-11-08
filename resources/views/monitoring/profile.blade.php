@extends('layouts.monitoring')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/monitoring/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Profile</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
@endsection

@section('content')
    <div class="col" style="padding-top: 75px; padding-bottom: 75px;">
        @if (Session::get('success'))
            <div class="alert alert-success mb-3">
                {{ Session::get('success') }}
            </div>
        @endif
        @if (Session::get('error'))
            <div class="alert alert-danger mb-3">
                {{ Session::get('error') }}
            </div>
        @endif

        @error('foto')
            <div class="alert alert-warning mb-3">
                <p class="mb-0">{{ $message }}</p>
            </div>
        @enderror

        <div class="text-center mb-3">
            @if (!empty($user->foto))
                @php
                    $path = Storage::url('uploads/users/' . $user->foto);
                @endphp
                <img src="{{ url($path) }}" alt="Avatar" class="imaged rounded-circle shadow-sm"
                    style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #e2e8f0;"
                    onerror="this.src='{{ asset('assets/img/avatar_monitoring.png') }}'">
            @else
                <img src="{{ asset('assets/img/avatar_monitoring.png') }}?v={{ time() }}" alt="Avatar" class="imaged rounded-circle shadow-sm"
                    style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #e2e8f0; background: #fff;">
            @endif
            <div class="mt-2 font-weight-bold" style="font-size: 15px; color: #1e293b;">
                {{ $user->name }}
            </div>
            <div class="text-muted" style="font-size: 12px;">{{ $user->jabatan ?? 'UPPD SAMSAT Batulicin' }}</div>
        </div>

        <form action="/monitoring/profile/update" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 2px;">Nama Lengkap</label>
                    <input type="text" class="form-control" value="{{ $user->name }}" disabled autocomplete="off" style="background: #f8fafc; color: #334155;">
                </div>
            </div>

            @if (!empty($user->jabatan))
                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 2px;">Jabatan</label>
                        <input type="text" class="form-control" value="{{ $user->jabatan }}" disabled autocomplete="off" style="background: #f8fafc; color: #334155;">
                    </div>
                </div>
            @endif

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 2px;">Email</label>
                    <input type="text" class="form-control" value="{{ $user->email }}" disabled autocomplete="off" style="background: #f8fafc; color: #334155;">
                </div>
            </div>

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 2px;">No. HP</label>
                    <input type="text" class="form-control" value="{{ $user->no_hp ?? '-' }}" disabled autocomplete="off" style="background: #f8fafc; color: #334155;">
                </div>
            </div>

            <div class="custom-file-upload mt-3" id="fileUploadMonitoring">
                <input type="file" name="foto" id="fileuploadInput" accept=".png, .jpg, .jpeg">
                <label for="fileuploadInput">
                    <span>
                        <strong>
                            <ion-icon name="cloud-upload-outline" role="img" class="md hydrated" aria-label="cloud upload outline"></ion-icon>
                            <i>Pilih Foto Profil</i>
                        </strong>
                    </span>
                </label>
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary btn-block btn-lg" style="background-color: #2A8BF2; border-color: #2A8BF2;">
                    <ion-icon name="camera-outline" style="vertical-align: -2px; margin-right: 4px;"></ion-icon>
                    Update Foto Profil
                </button>
            </div>
        </form>
    </div>
@endsection
