# Roadmap Praktikum Programmer

Acuan soal: `docs/soal_praktikum_programmer.md`.

Praktikum ini punya dua bagian. Soal 1 merancang data sistem pinjaman perusahaan. Soal 2–8 membangun aplikasi perpustakaan (siswa, buku, peminjaman, dan laporan) dengan PHP dan MySQL.

Aturan pengerjaan dari soal:

- Setiap soal memakai minimal satu `function`.
- Database dikelola lewat phpMyAdmin di `http://localhost/xdba/`.
- Target PHP 4–7, jadi kode memakai `mysqli` prosedural dan hindari fitur PHP 8.

Centang dengan mengubah `[ ]` menjadi `[x]` setelah bagian itu selesai.

## Status pengerjaan

- [x] Fase 0 — Persiapan koneksi dan menu
- [x] Soal 1 — Rancangan data pinjaman perusahaan
- [x] Soal 2 — Tabel MySQL dan data awal
- [x] Soal 3 — Editor data siswa
- [x] Soal 4 — Editor data buku
- [x] Soal 5 — Editor transaksi peminjaman
- [x] Soal 6 — Laporan buku yang masih tersedia
- [ ] Soal 7 — Laporan belum dikembalikan dan denda
- [ ] Soal 8 — Rekap pinjaman per siswa
- [ ] Cek akhir seluruh aplikasi

## Urutan kerja

```text
Soal 1  Desain data pinjaman perusahaan
   |
Soal 2  Database perpustakaan + data awal
   |
   +-- Soal 3  Editor siswa
   +-- Soal 4  Editor buku
   |
Soal 5  Editor peminjaman (butuh siswa dan buku)
   |
   +-- Soal 6  Laporan buku tersedia
   +-- Soal 7  Laporan belum kembali + denda
   +-- Soal 8  Rekap pinjaman per siswa
```

Soal 3 dan 4 bisa dikerjakan beriringan setelah database ada. Soal 5 menunggu keduanya, karena dropdown NIS dan ID buku diambil dari tabel siswa dan buku. Soal 6–8 menunggu data peminjaman.

## Struktur aplikasi

Bukan folder per nomor soal. Aplikasi perpustakaan dipisah per lapisan. Rancangan pinjaman perusahaan (soal 1) berupa dokumen, bukan modul `soal1`.

```text
Yuanthio_Virly/
  index.php                         menu
  config/
    koneksi.php                     satu koneksi database
    format_tanggal.php              DATE MySQL ke 12-Mar-1987 / 01-Nop-2010
  includes/
    layout.php                      awal dan akhir halaman
  app/perpustakaan/                 query dan hitungan, tanpa HTML
    siswa.php
    buku.php
    pinjam.php
    laporan.php
    data_awal.php                 jumlah baris data awal
  app/pembiayaan/
    pinjaman.php                  laporan transfer, penarikan, pemakaian, cicilan
  view/perpustakaan/                HTML list, editor, dan laporan
    siswa_list.php
    siswa_editor.php
    buku_list.php
    buku_editor.php
    pinjam_list.php
    pinjam_editor.php
    laporan_tersedia.php
    laporan_belum_kembali.php
    laporan_rekap.php
  perpustakaan/                     halaman yang dibuka pengguna
    siswa.php                       action=list|add|edit
    buku.php
    pinjam.php
    laporan_tersedia.php
    laporan_belum_kembali.php
    laporan_rekap.php
  sql/
    perpustakaan.sql
    pinjaman_perusahaan.sql
  docs/
    soal_praktikum_programmer.md
    roadmap_praktikum.md
    rancangan_pinjaman_perusahaan.md
```

Halaman di `perpustakaan/` hanya mengurus `action`, memanggil function di `app/`, lalu menyertakan file di `view/`.

## Fase 0 — Persiapan

- [x] Buat database MySQL, misalnya `perpustakaan`.
- [x] Siapkan `config/koneksi.php` dengan satu function koneksi.
- [x] Siapkan `index.php` sebagai menu modul perpustakaan: Siswa, Buku, Peminjaman, dan tiga laporan.
- [x] Sepakati format tanggal di layar: `12-Mar-1987` dan `01-Nop-2010` (bulan Indonesia singkat). Di database simpan `DATE` (`1987-03-12`), format hanya saat ditampilkan. Function-nya ada di `config/format_tanggal.php`.

## Fase 1 — Soal 1: rancangan pinjaman perusahaan

Hasil: dokumen rancangan, belum wajib jadi aplikasi.

Enam kelompok data dan relasinya:

| Tabel | Isi | Relasi |
| --- | --- | --- |
| Karyawan | Identitas peminjam | Induk |
| Pinjaman | Pokok pinjaman satu karyawan | Banyak pinjaman per karyawan |
| Transfer dana | Setoran bertahap ke rekening | Banyak transfer per pinjaman |
| Penarikan dana | Dana yang ditarik peminjam setelah masuk rekening | Banyak penarikan per pinjaman |
| Pemakaian dana | Penggunaan dana oleh peminjam | Banyak pemakaian per pinjaman |
| Cicilan | Angsuran pelunasan | Banyak cicilan per pinjaman |

Yang harus bisa dilaporkan:

- [x] Pencairan bertahap. Contoh: pinjam Rp100.000.000 disetor 2 kali @ Rp50.000.000.
- [x] Setiap penarikan dari dana yang sudah disetor.
- [x] Pemakaian dana oleh peminjam.
- [x] Cicilan pelunasan.
- [x] Dari satu baris pinjaman bisa ditelusuri transfer, penarikan, pemakaian, dan cicilannya.
- [x] Dokumen rancangan tersimpan di `docs/rancangan_pinjaman_perusahaan.md`.

## Fase 2 — Soal 2: tabel dan data awal

Hasil: file SQL yang bisa di-import, tiga tabel terisi persis seperti soal.

| Tabel | Primary key | Catatan |
| --- | --- | --- |
| `TBL_SISWA` | `NIS` | 7 baris. Kelamin disimpan `L` / `P`. |
| `TBL_BUKU` | `ID` | 7 baris. |
| `TBL_PINJAM` | `ID` | 7 baris. Kolom `NIK` di soal diarahkan ke `NIS` siswa. `TGL_KEMBALI` boleh kosong. `STATUS` `0` terpinjam, `1` kembali. |

Relasi:

- `TBL_PINJAM.NIS` mengacu ke `TBL_SISWA.NIS`.
- `TBL_PINJAM.ID_BUKU` mengacu ke `TBL_BUKU.ID`.

- [x] File `sql/perpustakaan.sql` berisi `CREATE` dan `INSERT` ketiga tabel.
- [x] `TBL_SISWA` terisi 7 baris sesuai soal.
- [x] `TBL_BUKU` terisi 7 baris sesuai soal.
- [x] `TBL_PINJAM` terisi 7 baris sesuai soal.
- [x] Relasi NIS dan ID buku sudah dipasang.

## Fase 3 — Soal 3: editor siswa

Dua tampilan.

**List:** NIS, nama, tanggal lahir (`12-Mar-1987`), kelamin (“Laki-Laki” / “Perempuan”), EDIT, DELETE, plus tombol ADD NEW.

**Editor:** NIS, nama, tanggal lahir (dropdown hari, bulan, tahun), jenis kelamin (dropdown), tombol OK, Close, Save.

- [x] Tampilan list sesuai kolom soal, termasuk format tanggal dan label kelamin.
- [x] Tombol ADD NEW membuka editor kosong.
- [x] Save pada data baru menyimpan baris siswa.
- [x] EDIT membuka editor terisi, Save mengubah baris itu.
- [x] DELETE menghapus siswa. Tolak hapus jika NIS masih dipakai di `TBL_PINJAM`.
- [x] OK menyimpan lalu kembali ke list. Close kembali ke list tanpa menyimpan.
- [x] Ada minimal satu `function` (ambil semua, ambil satu, simpan, ubah, hapus, format tanggal, label kelamin).

## Fase 4 — Soal 4: editor buku

Pola sama dengan siswa.

**List:** ID, judul, ISBN, pengarang, EDIT, DELETE, ADD NEW.

**Editor:** ID, judul, ISBN, pengarang, tombol OK, Close, Save.

- [x] Tampilan list sesuai kolom soal.
- [x] ADD NEW, EDIT, DELETE, OK, Close, dan Save berjalan.
- [x] Ada minimal satu `function` (ambil semua, ambil satu, simpan, ubah, hapus).

## Fase 5 — Soal 5: editor peminjaman

**List** menampilkan nama siswa dan judul buku, bukan hanya kode.

| Kolom tampilan | Sumber |
| --- | --- |
| ID | `TBL_PINJAM.ID` |
| Pinjam / Batas / Kembali | tanggal, format `01-Nop-2010`. Kembali kosong jika belum kembali. |
| Status | `0` → Terpinjam, `1` → Kembali |
| Siswa | join `TBL_SISWA.NAMA` |
| Buku | join `TBL_BUKU.JUDUL` |

**Editor:**

- ID, tanggal pinjam (dropdown hari, bulan, tahun).
- Tanggal batas **ditampilkan**, dihitung dari tanggal pinjam (data soal selalu +7 hari: 1 Nov → 8 Nov, 6 Nov → 13 Nov, 9 Nov → 16 Nov).
- Tanggal kembali (dropdown). Boleh kosong saat status masih terpinjam.
- Status: dropdown Pilih / Terpinjam (0) / Kembali (1).
- NIS: dropdown dari `TBL_SISWA`.
- ID buku: dropdown dari `TBL_BUKU`.

- [x] List menampilkan nama siswa dan judul buku, status Terpinjam/Kembali, tanggal format `01-Nop-2010`.
- [x] Tanggal kembali kosong jika belum kembali.
- [x] Editor: tanggal pinjam dropdown, tanggal batas tampil otomatis (+7 hari).
- [x] Dropdown status, NIS, dan ID buku terisi dari data.
- [x] ADD NEW, EDIT, DELETE, OK, Close, dan Save berjalan.
- [x] Ada minimal satu `function` (list join, simpan, ubah, hapus, hitung tanggal batas).

## Fase 6 — Soal 6: buku yang masih tersedia

Laporan buku yang **tidak** sedang berstatus terpinjam (`STATUS = 0`).

Dari data awal, yang sedang keluar: B007, B002, B006, B001, B004. Yang tersedia: **B003** dan **B005** (keduanya status kembali).

- [x] Laporan hanya menampilkan buku yang tidak sedang terpinjam.
- [x] Dengan data awal, yang muncul B003 dan B005.
- [x] Ada minimal satu `function` untuk query buku tersedia.

## Fase 7 — Soal 7: belum dikembalikan dan denda

Laporan buku berstatus terpinjam, beserta peminjam, ID pinjam, batas akhir, dan denda.

Ketentuan denda:

- Hanya jika melewati tanggal batas **dan** buku belum kembali.
- Rp5.000 per hari, terhitung dari tanggal batas.

Contoh: batas 8 Nov 2010, hari hitung 10 Nov 2010, belum kembali → 2 hari × 5.000 = Rp10.000. Jika belum lewat batas, denda Rp0.

- [ ] Laporan memuat peminjam, ID pinjam, batas akhir, dan denda.
- [ ] Hanya baris status terpinjam (`0`) yang masuk.
- [ ] Denda Rp5.000 per hari setelah lewat tanggal batas. Belum lewat batas = Rp0.
- [ ] Ada minimal satu `function` hitung denda.

## Fase 8 — Soal 8: rekap per siswa

Jumlah buku yang pernah dipinjam tiap siswa (semua status, terpinjam maupun sudah kembali).

Dari data awal:

| Siswa | Jumlah |
| --- | --- |
| ARIF ABDULRAHMAN (001) | 2 |
| RASMI RAHMAWATI (003) | 1 |
| UDIN R. KAMAL (004) | 1 |
| SEPTIRINA HERMAWAN (006) | 2 |
| HERMAN BARLIN (007) | 1 |
| ASRI WELAS (002) | 0 |
| ARDIYANSAH SAPUTRA (005) | 0 |

Siswa yang belum pernah pinjam tetap muncul dengan jumlah 0 (`LEFT JOIN`).

- [ ] Setiap siswa muncul, termasuk yang jumlahnya 0.
- [ ] Angka data awal cocok dengan tabel di atas.
- [ ] Ada minimal satu `function` rekap `COUNT` per NIS.

## Definisi yang dikunci supaya laporan konsisten

- Status `0` = Terpinjam, `1` = Kembali.
- Buku “tersedia” = tidak punya baris pinjam dengan status 0.
- Buku “belum dikembalikan” = status 0.
- Denda = `max(0, selisih hari dari tanggal batas ke hari ini) × 5000`, hanya untuk status 0.
- Tanggal batas = tanggal pinjam + 7 hari, mengikuti pola data soal.
- Kelamin di database `L` / `P`, di layar “Laki-Laki” / “Perempuan”.
- Bulan singkat di layar: Jan, Feb, Mar, Apr, Mei, Jun, Jul, Agu, Sep, Okt, Nop, Des.

## Urutan cek akhir

- [ ] Import SQL, hitung baris tiga tabel (7, 7, dan 7).
- [ ] Tambah, ubah, hapus satu siswa dan satu buku, lalu kembalikan data soal jika perlu.
- [ ] Tambah satu pinjaman: batas harus +7 hari, list menampilkan nama dan judul.
- [ ] Laporan tersedia hanya buku yang tidak sedang keluar.
- [ ] Laporan denda hanya menghitung pinjaman status 0 yang lewat batas.
- [ ] Rekap per siswa cocok dengan tabel di fase 8 untuk data awal.
