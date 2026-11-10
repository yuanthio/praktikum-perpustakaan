# AI Coding Rules & Standards Guidelines

Panduan ini berisi aturan baku dalam penulisan kode (Clean Code, Software Architecture, Maintainability, Security) untuk pengerjaan proyek **Praktikum Pemrograman Web (PHP 4-7 & MySQL)**.

---

## 1. Aturan Dasar & Pembatas (Constraints)

1. **Compatibility:** Kode wajib kompatibel dengan **PHP 4 hingga PHP 7** (Hindari fitur PHP 8+ seperti *named arguments*, *match expressions*, *union types*, atau *nullsafe operators*).
2. **Function Requirement:** Setiap file/soal wajib mengimplementasikan **minimal satu (1) `function`** modular untuk pengolahan logika, kalkulasi, atau rendering UI.
3. **Database Client:** Gunakan API database yang konsisten (disarankan menggunakan `mysqli_*` atau `PDO` yang mendukung PHP 5–7; hindari fungsi `mysql_*` lama jika menjalankan PHP 7).

---

## 2. Clean Code & Readable Code

1. **Naming Conventions:**
   * **Variables & Functions:** Gunakan format `camelCase` yang deskriptif dan mencerminkan tujuannya.
     * *Baik:* `$dataSiswa`, `function hitungDendaPinjaman()`
     * *Buruk:* `$x`, `$a1`, `function f()`
   * **Constants:** Gunakan `UPPER_SNAKE_CASE` (misal: `DB_HOST`, `TARIF_DENDA_PER_HARI`).
   * **File Names:** Gunakan `snake_case` atau `kebab-case` yang konsisten (misal: `koneksi.php`, `siswa_list.php`, `siswa_editor.php`).
2. **Komentar & Dokumentasi (PHPDoc):**
   * Berikan komentar PHPDoc singkat pada setiap `function` untuk menjelaskan masukan (parameter), keluaran (return value), dan tujuan fungsi.
3. **DRY (Don't Repeat Yourself):**
   * Pisahkan logika umum (misalnya koneksi database, header HTML, footer HTML) ke dalam file terpisah (seperti `koneksi.php` atau `header.php`) lalu gunakan `require_once` atau `include_once`.

---

## 3. Architecture & Modular Strategy

Meski proyek ini berbasis PHP prosedural, arsitektur harus tetap terpisah dengan baik:

1. **Separation of Concerns (SoC):**
   * **Database Layer:** Fungsi untuk query ke MySQL (`SELECT`, `INSERT`, `UPDATE`, `DELETE`).
   * **Business Logic Layer:** Fungsi kalkulasi (misalnya perhitungan selisih hari & denda pada Soal 7).
   * **Presentation Layer:** Template HTML/UI untuk menampilkan List dan Editor.
2. **State & Sub-View Management:**
   * Untuk soal dengan 2 sub-tampilan (*List* dan *Editor*), manfaatkan parameter URL (misalnya `action=add`, `action=edit&id=...`) untuk mengontrol tampilan dengan alur yang bersih tanpa merusak struktur halaman.

---

## 4. Security & Robustness

1. **SQL Injection Prevention:**
   * Selalu gunakan *Prepared Statements* (`mysqli_prepare` / `PDO`) atau lakukan *sanitization/escaping* (`mysqli_real_escape_string`) pada semua data masukan pengguna (`$_POST`, `$_GET`).
2. **XSS (Cross-Site Scripting) Prevention:**
   * Gunakan `htmlspecialchars()` saat menampilkan data variabel ke dalam HTML.
3. **Input Validation & Fallbacks:**
   * Selalu lakukan pengecekan keberadaan data (`isset()` atau `empty()`) sebelum memproses request POST/GET.

---

## 5. Performa (tetap ngebut)

Halaman praktikum ini kecil, tapi setiap request harus tetap ringan. Jangan menambah query, loop, atau hitungan yang tidak perlu.

1. **Satu koneksi per request:** Buka database sekali lewat `require_once` `koneksi.php`. Jangan panggil `mysqli_connect` di dalam loop atau di setiap function.
2. **Jangan query di dalam loop:** List peminjaman, laporan tersedia, denda, dan rekap siswa diambil dengan **satu** `SELECT` (pakai `JOIN` atau `LEFT JOIN`). Jangan ambil siswa atau buku satu per satu untuk setiap baris pinjam.
3. **Ambil kolom yang dipakai saja:** Hindari `SELECT *` pada laporan. Kolom yang tidak tampil di layar tidak ikut di-query.
4. **Filter di SQL, bukan di PHP:** Buku tersedia, status terpinjam, dan rekap `COUNT` diselesaikan di `WHERE` / `GROUP BY`. PHP hanya memformat tanggal, label status, dan nominal denda.
5. **Index kolom yang dipakai filter dan relasi:** Primary key sudah index. Tambah index pada `TBL_PINJAM.NIS`, `TBL_PINJAM.ID_BUKU`, dan `TBL_PINJAM.STATUS` supaya laporan tidak memindai tabel penuh.
6. **Jangan hitung ulang data yang sama:** Tanggal batas, label kelamin, dan denda lewat satu function. Dalam satu halaman, query yang sama tidak dijalankan dua kali; simpan hasilnya di variabel.
7. **Siapkan statement yang berulang:** `INSERT`, `UPDATE`, dan `DELETE` memakai prepared statement yang di-prepare sekali, lalu di-bind per simpan. Jangan merangkai SQL baru di setiap baris.
8. **Tutup hasil query:** Panggil `mysqli_free_result()` setelah data selesai dibaca, dan jangan menahan result set yang sudah tidak dipakai.
9. **Halaman tanpa kerja sampingan:** Tampil list, editor, dan laporan tidak boleh menjalankan `UPDATE` tersembunyi. Hitung denda saat ditampilkan, jangan menulis denda ke database pada setiap buka halaman.
10. **HTML langsung, tanpa kerangka berat:** Jangan pasang framework, ORM, atau autoload yang tidak dipakai soal. Satu request = koneksi, query yang diperlukan, lalu HTML.

## 6. UI/UX & Layout Precision

1. **Strict Layout Match:**
   * Bentuk form editor (seperti Dropdown Tgl/Bulan/Tahun, Jenis Kelamin, dan tombol OK/Close/Save) harus dibuat persis sesuai gambar mockup pada soal.
2. **User Feedback:**
   * Tampilkan pesan sukses atau gagal (misal: "Data berhasil disimpan") secara rinci dan ramah pengguna setelah operasi database berhasil dilakukan.