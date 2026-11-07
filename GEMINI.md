# Panduan & Kebiasaan Arahan Pengembangan SAKTI

File ini mencatat pedoman, preferensi, dan kebiasaan arahan USER untuk aplikasi SAKTI (Sistem Absensi & Rekap Penerimaan PKB UPPD SAMSAT Batulicin). Semua agen/assistant WAJIB mematuhi panduan ini.

---

## 1. Gaya Penulisan Form & Tampilan UI (Ringkas, Padat, Langsung)
- **DILARANG memberikan penjelasan panjang** atau catatan bertele-tele pada form, baik pada `label`, `placeholder`, maupun `hint`.
- **DILARANG menggunakan keterangan dalam kurung**, seperti `(Bisa untuk Login)`, `(Kosongkan bila tidak diubah)`, dsb.
- Gunakan teks singkat dan lugas:
  - Contoh benar: `No. HP`, `Email`, `Nama User`, `Password Baru`
  - Contoh salah: `No. WhatsApp / HP (Bisa untuk Login)`, `Password Baru (Kosongkan bila tidak diubah)`
- **Jangan menggunakan icon bintang (`⭐`)** atau dekorasi tambahan yang tidak diminta pada input form.

---

## 2. Standar Nama & Konvensi SAKTI
- Penulisan nama kota yang baku dan benar adalah **Batulicin** (BUKAN *batu licin* atau *Batu Licin*).
- **DILARANG menggunakan kata "Posko"**, gunakan istilah baku **"Lokasi"** (misal: *Lokasi Layanan*, bukan *Posko Layanan*).
- Pewarnaan status presensi/layanan:
  - **Hadir (H)**: Hijau
  - **Libur (L)**: Oren
  - **Maintenance**: Biru
  - **Tanpa Keterangan (TK)**: Merah
- Icon / Logo Sidebar: Gunakan icon **mini bus / mobil pelayanan**.
- Avatar/Foto Profil Default:
  - **Akun Layanan**: Jika akun layanan tidak memiliki foto profil, tampilkan ilustrasi **mobil/bis pelayanan** (`assets/img/avatar_layanan.png`).
  - **Akun Monitoring**: Tampilkan vektor user **berpakaian jas hitam** formal (`assets/img/avatar_monitoring.png`).

---

## 3. Struktur Role & Hak Akses
- Role yang aktif di sistem:
  1. **Layanan**: Akun armada/layanan keliling.
  2. **Monitoring**: Akun pimpinan/pengawas untuk melihat data layanan, jadwal lokasi, maintenance, presensi, dan rekap penerimaan (tampilan mobile khusus + grafik dashboard).
  3. **Super Admin**: Akun teknis utama (`superadmin@presensigps.com`).
- **Role `admin departemen` sudah dihapus** dan tidak boleh dimunculkan kembali.
- Pada form tambah/edit user monitoring, **TIDAK ADA pilihan role** (otomatis terkunci ke role `monitoring`).
- Akun **Super Admin dilindungi penuh** (tidak bisa diedit atau dihapus) dan **tidak ditampilkan** pada tabel kelola user.
- **Akun Monitoring dan Super Admin TIDAK memerlukan Lokasi (`kode_cabang`)**; nilai `kode_cabang` diset `null` dan dropdown/kolom lokasi tidak ditampilkan.

---

## 4. Mekanisme Login
- Akun Monitoring dapat login menggunakan **Email**, **Nomor HP**, atau **Username**.
- Normalisasi nomor HP secara otomatis (`08...`, `628...`, `+628...`, tanda pemisah strip/spasi).
- Dilengkapi **smart fallback**: jika pengguna berada di tab Layanan namun menginput kredensial akun Monitoring, sistem tetap mengarahkannya ke Dashboard Monitoring.

---

## 5. Keselarasan Tampilan Akun Monitoring & Layanan (Wajib Senada & Identik)
- Tampilan aplikasi mobile pada akun **Monitoring** WAJIB sama dan senada dengan akun **Layanan**:
  - **Warna Biru Khas**: `#2A8BF2` (`linear-gradient(135deg, #2A8BF2 0%, #1A73E8 100%)`).
  - **Background Layar**: `#e9ecef`.
  - **Struktur Header**: Menggunakan `#user-section`, `.instansi-header` (Logo Kalsel + UPPD SAMSAT Batulicin), tombol keluar `.logout` di pojok kanan atas, serta avatar bulat berbingkai putih.
  - **Floating Quick Menu**: `#menu-section` dengan `.list-menu` melayang menutupi batas header biru (`margin-top: -42px !important;`).
  - **Ringkasan Kartu Operasional**: Menggunakan format `.todaypresence` dengan card gradasi (`.gradasigreen` & `.gradasired`).
  - **Card Status Penerimaan**: Format card putih sudut melengkung 14px dengan ikon kasir hijau, rincian nominal tebal, dan tombol aksi.
  - **Rekap 3 Kolom**: Menggunakan `#rekappresensi` dengan badge angka di pojok kanan atas.
  - **Tab Navigasi**: Menggunakan `.presencetab` dengan class `.nav-tabs style1`.
  - **Footer / Bottom Navigation**: Menggunakan class native `.appBottomMenu` dengan 5 menu, termasuk tombol tengah melayang besar (`.action-button large`) yang merupakan tombol **Home** (Layanan di sisi kiri). Ikon Home di tengah dipaksa selalu berwarna putih (`#ffffff !important`) agar tidak memudar/menghilang ke background biru saat berada di halaman Dashboard.

---

## 6. Kelola User & Informasi Profil
- Pada menu dan tabel Kelola User:
  - Menu sidebar menggunakan label **User**.
  - Tabel user menampilkan kolom **Jabatan** (bukan kolom Role Monitoring karena role sudah pasti monitoring).
  - Modal tambah & edit user menyediakan input **Jabatan**.
- Pada Header Dashboard Mobile Monitoring:
  - Menampilkan Nama User dan **Jabatan** (jika belum ada jabatan, tampilkan `UPPD SAMSAT Batulicin`), **TIDAK menampilkan teks role "Role: Monitoring"**.

---

## 7. Halaman Data Layanan (Akun Monitoring)
- Judul header halaman atas adalah **Data Layanan** (bukan *Data Layanan Keliling*).
- Pada kartu data layanan:
  - **TIDAK menampilkan jabatan PIC**.
  - **TIDAK menampilkan status scan** (*Scan: ON/OFF*).
  - **TIDAK menggunakan icon pada badge "Siap Layanan"** (hanya teks polos ber-badge hijau *Siap Layanan*).
  - **TIDAK menggunakan icon user/orang sebelum teks "PIC"**.

---

## 8. Ciri Khas Tampilan Akun Monitoring
- **Header Atas**: Menggunakan **gradasi Biru ke Biru Navy** (`linear-gradient(135deg, #1E6FD9 0%, #15438C 50%, #0c234b 100%)`).
- **Nama User Monitoring**: Berwarna **Gold** (`#ffd700`) dengan text-shadow halus agar terlihat eksklusif dan prestisius.
- **Menu Footer Aktif**: Menu yang sedang aktif pada bottom navigation monitoring berwarna **Navy** (`#0c234b`), serta tombol Home tengah menggunakan gradasi Navy (`linear-gradient(135deg, #1E6FD9 0%, #0c234b 100%)`) dengan ikon tetap putih.



