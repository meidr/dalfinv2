# Fitur Mentor Sempro (Pembimbing Seminar Proposal)

Menambahkan tahap baru **"Penentuan Mentor"** di antara ACC Judul dan Seminar Proposal. Mentor ini adalah dosen pembimbing khusus untuk seminar proposal, terpisah dari Dosen Pembimbing Skripsi (dospem) yang sudah ada. Tidak ada SK — hanya data dan cetak PDF biasa.

## User Review Required

> [!IMPORTANT]
> **Perubahan Alur Status Skripsi:**
> ```
> SEBELUM: pengajuan → disetujui → proposal → sempro → penentuan_dospem → dospem → bimbingan → ...
> SESUDAH: pengajuan → disetujui → penentuan_mentor → mentor → proposal → sempro → penentuan_dospem → dospem → bimbingan → ...
> ```
> Dua status baru ditambahkan: `penentuan_mentor` dan `mentor` (mentor telah ditetapkan).

> [!WARNING]
> **Data Existing:** Skripsi yang sudah berstatus `proposal` atau lebih lanjut tidak terpengaruh. Skripsi yang berstatus `disetujui` akan tetap di status tersebut dan admin perlu menetapkan mentor sebelum melanjutkan ke proposal.

## Open Questions

> [!IMPORTANT]
> 1. **Jumlah mentor:** Apakah mentor sempro bisa 1 atau 2 orang (seperti pembimbing skripsi), atau hanya 1?
>    - **Asumsi saat ini:** Mendukung hingga 2 mentor (mentor_1 dan mentor_2), sama seperti pembimbing skripsi. benar
> 2. **Kuota mentor:** Apakah kuota mentor terpisah dari kuota bimbingan dosen, atau pakai kuota yang sama? beda kuota dengan pembimbing skripsi
>    - **Asumsi saat ini:** Tidak ada kuota khusus mentor (hanya data assignment saja).
> 3. **Isi PDF mentor:** PDF sederhana berisi informasi mentor, mahasiswa, dan judul skripsi. Tidak perlu nomor surat/SK.
>    - **Asumsi:** Format surat biasa (non-SK) dengan kop surat, data mentor, data mahasiswa, judul, tanggal penetapan. benar

## Proposed Changes

### Database Layer

#### [NEW] Migration: `create_mentor_sempro_table`
- File: [create_mentor_sempro_table.php](file:///c:/laragon/www/dalfin/backend/database/migrations/2026_06_13_000001_create_mentor_sempro_table.php)
- Tabel `mentor_sempro` dengan kolom:
  - `id`, `skripsi_id` (FK), `dosen_id` (FK), `jenis` (enum: mentor_1, mentor_2)
  - `tanggal_penetapan` (date), `is_active` (boolean), `timestamps`

#### [NEW] Migration: `add_mentor_status_to_skripsi`
- File: [add_mentor_status_to_skripsi.php](file:///c:/laragon/www/dalfin/backend/database/migrations/2026_06_13_000002_add_mentor_status_to_skripsi.php)
- Menambahkan status `penentuan_mentor` dan `mentor` ke enum status skripsi

---

### Backend Models

#### [MODIFY] [Skripsi.php](file:///c:/laragon/www/dalfin/backend/app/Models/Skripsi.php)
- Tambah status constants: `STATUS_PENENTUAN_MENTOR = 'penentuan_mentor'`, `STATUS_MENTOR = 'mentor'`
- Tambah relationships: `mentorSempro()`, `mentor1()`, `mentor2()`
- Update `statusOrder` di frontend nanti

#### [NEW] [MentorSempro.php](file:///c:/laragon/www/dalfin/backend/app/Models/MentorSempro.php)
- Model baru dengan relasi ke `Skripsi` dan `Dosen`
- Fields: `skripsi_id`, `dosen_id`, `jenis`, `tanggal_penetapan`, `is_active`

#### [MODIFY] [Dosen.php](file:///c:/laragon/www/dalfin/backend/app/Models/Dosen.php)
- Tambah relationship: `mentorSempro()` — daftar mahasiswa yang dimentori

---

### Backend Controllers

#### [NEW] [MentorSemproController.php](file:///c:/laragon/www/dalfin/backend/app/Http/Controllers/Api/Admin/MentorSemproController.php)
Controller admin untuk mengelola mentor sempro:
- `index()` — List skripsi yang butuh/sudah punya mentor (filter: status `disetujui`, `penentuan_mentor`, `mentor`)
- `availableDosen()` — Daftar dosen yang bisa jadi mentor
- `store()` — Tetapkan mentor (update status ke `mentor`)
- `update()` — Update mentor
- `destroy()` — Hapus mentor (revert status ke `penentuan_mentor`)

#### [MODIFY] [SeminarController.php](file:///c:/laragon/www/dalfin/backend/app/Http/Controllers/Api/Admin/SeminarController.php)
- Update `index()` filter: tambah status `mentor` agar skripsi yang sudah punya mentor muncul di list seminar
- Update `store()`: izinkan jadwal sempro dari status `mentor` (selain `pengajuan`, `proposal`)

#### [MODIFY] [SkripsiController.php](file:///c:/laragon/www/dalfin/backend/app/Http/Controllers/Api/Admin/SkripsiController.php)
- Update `calculateProgress()`: tambah progress untuk `penentuan_mentor` (12%) dan `mentor` (14%)

#### [MODIFY] [SkripsiVerificationController.php](file:///c:/laragon/www/dalfin/backend/app/Http/Controllers/Api/Admin/SkripsiVerificationController.php)
- Update `approve()`: setelah approve judul, status berubah ke `penentuan_mentor` (bukan langsung `proposal`)

#### [MODIFY] [PdfController.php](file:///c:/laragon/www/dalfin/backend/app/Http/Controllers/Api/Admin/PdfController.php)
- Tambah method `suratMentorSempro()` — generate PDF sederhana (bukan SK) berisi data mentor

#### [NEW] [pdf/surat-mentor-sempro.blade.php](file:///c:/laragon/www/dalfin/backend/resources/views/pdf/surat-mentor-sempro.blade.php)
- Template PDF sederhana: kop surat, data mahasiswa, judul skripsi, data mentor, tanggal penetapan

---

### Backend Routes

#### [MODIFY] [api.php](file:///c:/laragon/www/dalfin/backend/routes/api.php)
Tambah routes di group `admin`:
```php
// Mentor Sempro Management
Route::get('/mentor-sempro', [MentorSemproController::class, 'index']);
Route::get('/mentor-sempro/available-dosen', [MentorSemproController::class, 'availableDosen']);
Route::post('/mentor-sempro', [MentorSemproController::class, 'store']);
Route::put('/mentor-sempro/{mentorSempro}', [MentorSemproController::class, 'update']);
Route::delete('/mentor-sempro/{mentorSempro}', [MentorSemproController::class, 'destroy']);

// PDF Mentor
Route::get('/pdf/surat-mentor/{skripsi}', [PdfController::class, 'suratMentorSempro']);
```

Tambah routes di group `dosen`:
```php
Route::get('/mentor-sempro', [DosenBimbinganController::class, 'mentorSemproList']);
```

Tambah routes di group `mahasiswa`:
```php
Route::get('/skripsi/mentor', [MahasiswaSkripsiController::class, 'getMentor']);
```

---

### Backend — Dosen & Mahasiswa Integration

#### [MODIFY] [Dosen/BimbinganController.php](file:///c:/laragon/www/dalfin/backend/app/Http/Controllers/Api/Dosen/BimbinganController.php)
- Tambah `mentorSemproList()` — endpoint untuk dosen melihat mahasiswa yang dimentori untuk sempro

#### [MODIFY] [Dosen/DashboardController.php](file:///c:/laragon/www/dalfin/backend/app/Http/Controllers/Api/Dosen/DashboardController.php)
- Tambah stat `total_mentor_sempro` di dashboard dosen

#### [MODIFY] [Mahasiswa/SkripsiController.php](file:///c:/laragon/www/dalfin/backend/app/Http/Controllers/Api/Mahasiswa/SkripsiController.php)
- Update `show()`: load `mentorSempro.dosen` relation
- Tambah `getMentor()`: endpoint khusus untuk melihat data mentor sempro
- Update `downloadOfficialPdf()`: tambah case `surat-mentor` untuk download PDF mentor

---

### Frontend — Admin

#### [NEW] [DataMentorSempro.vue](file:///c:/laragon/www/dalfin/frontend/src/views/admin/mentorsempro/DataMentorSempro.vue)
- Halaman admin untuk mengelola penugasan mentor sempro
- Tabel skripsi yang butuh mentor, dialog assign mentor, filter & search
- Tombol cetak PDF mentor
- UI mirip dengan DataPembimbing.vue yang sudah ada

#### [MODIFY] [Sidebar.vue](file:///c:/laragon/www/dalfin/frontend/src/components/admin/Sidebar.vue)
- Tambah menu "Mentor Sempro" di antara "Data Skripsi" dan "Seminar Proposal" di section "Manajemen Skripsi"

#### [MODIFY] [router/index.js](file:///c:/laragon/www/dalfin/frontend/src/router/index.js)
- Tambah route `mentor-sempro` untuk admin

---

### Frontend — Mahasiswa

#### [MODIFY] [Progress.vue](file:///c:/laragon/www/dalfin/frontend/src/views/mahasiswa/skripsi/Progress.vue)
- Tambah step "Mentor" di workflow stepper antara "Judul" dan "Proposal"
- Update `statusOrder` dan `stepDefs` untuk include `penentuan_mentor` dan `mentor`
- Update `statusMap` untuk label baru

#### [MODIFY] [Pembimbing.vue](file:///c:/laragon/www/dalfin/frontend/src/views/mahasiswa/skripsi/Pembimbing.vue)
- Tambah section "Mentor Sempro" di atas section "Pembimbing Skripsi"
- Menampilkan data mentor sempro jika ada

---

### Frontend — Dosen

#### [MODIFY] [Dosen/Dashboard.vue](file:///c:/laragon/www/dalfin/frontend/src/views/dosen/Dashboard.vue)
- Tambah card stat "Mahasiswa Mentoring Sempro" di dashboard

#### [MODIFY] [Dosen/Bimbingan.vue](file:///c:/laragon/www/dalfin/frontend/src/views/dosen/Bimbingan.vue)
- Tambah tab atau filter untuk menampilkan mahasiswa yang dimentori (mentor sempro) vs yang dibimbing (pembimbing skripsi)

---

### Frontend Services

#### [MODIFY] [services/api.js](file:///c:/laragon/www/dalfin/frontend/src/services) atau service file terkait
- Tambah API calls untuk mentor sempro endpoints

---

## Verification Plan

### Automated Tests
```bash
cd c:\laragon\www\dalfin\backend
php artisan migrate
php artisan tinker --execute="echo App\Models\Skripsi::first()->status;"
```

### Manual Verification
1. **Admin:** Bisa assign mentor sempro ke skripsi yang sudah ACC judul
2. **Admin:** Bisa cetak PDF surat mentor (bukan SK)
3. **Mahasiswa:** Bisa melihat mentor sempro di halaman detail skripsi
4. **Dosen:** Bisa melihat mahasiswa yang dimentori di dashboard/bimbingan
5. **Alur status:** Verifikasi flow: disetujui → penentuan_mentor → mentor → proposal → sempro → ...
6. **Progress bar:** Persentase progress di workflow stepper berubah sesuai status baru
