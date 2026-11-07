@extends('layouts.presensi')
@section('header')
<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="/dashboard" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Edit Profile</div>
    <div class="right"></div>
</div>
<!-- * App Header -->
@endsection

@section('content')

<form action="/presensi/{{ $karyawan->nik }}/updateprofile" method="POST" enctype="multipart/form-data" style="padding-top: 75px;">
    @csrf
    <div class="col">
        @php
        $messagesuccess = Session::get('success');
        $messageerror = Session::get('error');
        @endphp
        @if (Session::get('success'))
        <div class="alert alert-success">
            {{ $messagesuccess }}
        </div>
        @endif
        @if (Session::get('error'))
        <div class="alert alert-warning">
            {{ $messageerror }}
        </div>
        @endif

        @error('foto')
        <div class="alert alert-warning">
            <p>{{ $message }}</p>
        </div>
        @enderror

        <div class="text-center mb-3">
            @if (!empty($karyawan->foto))
                @php
                    $path = Storage::url('uploads/karyawan/' . $karyawan->foto);
                @endphp
                <img src="{{ url($path) }}" alt="Foto Layanan" class="imaged rounded-circle shadow-sm"
                    style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #e2e8f0;"
                    onerror="this.src='{{ asset('assets/img/avatar_layanan.png') }}'">
            @else
                <img src="{{ asset('assets/img/avatar_layanan.png') }}" alt="Avatar Bis Layanan" class="imaged rounded-circle shadow-sm"
                    style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #e2e8f0; background: #fff;">
            @endif
            <div class="mt-2 font-weight-bold" style="font-size: 15px; color: #1e293b;">
                {{ $karyawan->nama_layanan ?? $karyawan->nama_lengkap }}
            </div>
            <div class="text-muted" style="font-size: 12px;">Akun Layanan SAKTI</div>
        </div>
        <div class="form-group boxed">
            <div class="input-wrapper">
                <label style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 2px;">Nama Layanan</label>
                <input type="text" class="form-control" value="{{ $karyawan->nama_lengkap }}" name="nama_lengkap" placeholder="Nama Layanan" autocomplete="off">
            </div>
        </div>
        
        
        
        <div class="form-group boxed">
            <div class="input-wrapper">
                <label style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 2px;">No. WhatsApp / HP</label>
                <input type="text" class="form-control" value="{{ $karyawan->no_hp }}" name="no_hp" placeholder="No. HP" autocomplete="off">
            </div>
        </div>
        <div class="form-group boxed">
            <div class="input-wrapper">
                <input type="password" class="form-control" name="password" placeholder="Password" autocomplete="off">
            </div>
        </div>

        <div class="custom-file-upload" id="fileUpload1">
            <input type="file" name="foto" id="fileuploadInput" accept=".png, .jpg, .jpeg">
            <label for="fileuploadInput">
                <span>
                    <strong>
                        <ion-icon name="cloud-upload-outline" role="img" class="md hydrated" aria-label="cloud upload outline"></ion-icon>
                        <i>Tap to Upload</i>
                    </strong>
                </span>
            </label>
        </div>
        <div class="form-group boxed">
            <div class="input-wrapper">
                <button type="submit" class="btn btn-primary btn-block">
                    <ion-icon name="refresh-outline"></ion-icon>
                    Update
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
