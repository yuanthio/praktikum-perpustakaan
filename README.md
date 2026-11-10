# Praktikum Perpustakaan

Aplikasi perpustakaan kecil untuk mencatat siswa, buku, dan peminjaman, plus tiga laporan. Rancangan pinjaman perusahaan disimpan terpisah dan tidak dicampur ke layar perpustakaan.

Buka aplikasi di `http://localhost/Yuanthio_Virly/` setelah Apache dan MySQL berjalan.

## Tech stack

- PHP prosedural, ditulis agar tetap jalan di PHP 5 sampai PHP 7. Tidak memakai framework atau ORM.
- MySQL atau MariaDB lewat `mysqli`, satu koneksi per request, query memakai prepared statement saat ada input pengguna.
- HTML dan CSS biasa, tanpa JavaScript framework. Tanggal batas di form peminjaman ikut berubah di browser, lalu dihitung ulang di server saat disimpan.
- Database `utf8mb4`.

## Kebutuhan

- [XAMPP](https://www.apachefriends.org/) atau Apache + PHP + MySQL/MariaDB.
- PHP dengan ekstensi `mysqli`.
- Akun MySQL lokal. Bawaan XAMPP: user `root`, kata sandi kosong.

Kredensial ada di `config/koneksi.php`. Itu untuk komputer lokal. Jangan dipakai di server yang bisa diakses orang lain.

## Instalasi

1. Nyalakan Apache dan MySQL dari XAMPP Control Panel.
2. Clone repositori ke dalam folder `htdocs`:

```bash
cd c:\xampp\htdocs
git clone https://github.com/yuanthio/praktikum-perpustakaan.git Yuanthio_Virly
```

Nama folder menjadi bagian URL. Kalau folder-nya bukan `Yuanthio_Virly`, sesuaikan alamat di browser.

3. Impor kedua file SQL. Dari folder proyek:

```bash
Get-Content -Raw sql\perpustakaan.sql | & "C:\xampp\mysql\bin\mysql.exe" -u root
Get-Content -Raw sql\pinjaman_perusahaan.sql | & "C:\xampp\mysql\bin\mysql.exe" -u root
```

Di mesin Linux atau macOS:

```bash
mysql -u root < sql/perpustakaan.sql
mysql -u root < sql/pinjaman_perusahaan.sql
```

Bisa juga lewat phpMyAdmin. Pada soal ini alatnya ada di `http://localhost/xdba/`. XAMPP standar biasanya memakai `http://localhost/phpmyadmin/`.

4. Buka `http://localhost/Yuanthio_Virly/`.

Kalau halaman menampilkan pesan koneksi gagal, pastikan MySQL hidup dan kedua database sudah terimpor. Kalau user atau kata sandi MySQL berbeda, ubah `DB_HOST`, `DB_USER`, dan `DB_PASS` di `config/koneksi.php`.

Impor ulang `sql/perpustakaan.sql` menghapus tabel perpustakaan lalu mengisinya lagi dengan data soal. Data yang ditambah lewat aplikasi akan hilang.

## Data awal

Database `perpustakaan` berisi 7 siswa, 7 buku, dan 7 pinjaman.

| Tabel | Isi |
| --- | --- |
| `TBL_SISWA` | NIS, nama, tanggal lahir, kelamin (`L` atau `P`) |
| `TBL_BUKU` | ID buku, judul, ISBN, pengarang |
| `TBL_PINJAM` | tanggal pinjam, batas, kembali, status, NIS, ID buku |

Database `pinjaman_perusahaan` dipakai rancangan soal pinjaman karyawan. Tidak ada menu untuk database itu.

## Cara pakai

Menu utama punya enam tautan.

### Data Siswa

Daftar menampilkan NIS, nama, tanggal lahir, dan kelamin. Kelamin di database `L`/`P`, di layar **Laki-Laki** / **Perempuan**.

- **ADD NEW** membuka form kosong.
- **EDIT** membuka data yang dipilih.
- **DELETE** menghapus baris. Siswa yang masih tercatat pada peminjaman tidak bisa dihapus.
- **OK** menyimpan lalu kembali ke daftar.
- **Save** menyimpan dan tetap di form.
- **Close** kembali ke daftar tanpa menyimpan.

NIS harus 3 digit, misalnya `008`.

### Data Buku

Kolomnya ID, judul, ISBN, dan pengarang. ID berbentuk `B` diikuti 3 digit, misalnya `B008`. Buku yang masih tercatat pada peminjaman tidak bisa dihapus atau diganti ID-nya.

### Transaksi Peminjaman

Daftar menampilkan nama siswa dan judul buku, bukan hanya kodenya.

- Tanggal pinjam dipilih dari hari, bulan, dan tahun.
- Tanggal batas dihitung otomatis: tanggal pinjam ditambah 7 hari. Contoh: 10 November 2010 menjadi `17-Nop-2010`.
- Tanggal kembali boleh kosong selama status **Terpinjam**.
- Status **Kembali** wajib punya tanggal kembali yang lengkap.
- Siswa dan buku dipilih dari dropdown.

ID pinjam berbentuk `P` diikuti 3 digit, misalnya `P008`. Status di database: `0` terpinjam, `1` kembali.

### Laporan Buku Tersedia

Menampilkan buku yang tidak sedang berstatus terpinjam. Dengan data awal, yang muncul **B003** dan **B005**.

### Laporan Belum Dikembalikan

Menampilkan pinjaman yang masih terpinjam: judul buku, peminjam, ID pinjam, tanggal batas, dan denda.

Denda Rp5.000 per hari setelah tanggal batas. Pada tanggal batas atau sebelumnya, denda Rp0. Angka dihitung saat halaman dibuka dan tidak ditulis ke database. Data soal berasal dari tahun 2010, jadi denda pada tanggal hari ini sudah besar.

Pinjaman yang sudah kembali tidak masuk laporan ini.

### Rekap Pinjaman per Siswa

Menghitung semua pinjaman tiap siswa, baik yang masih keluar maupun yang sudah kembali. Siswa yang belum pernah meminjam tetap muncul dengan jumlah 0.

Dengan data awal:

| NIS | Nama | Jumlah |
| --- | --- | --- |
| 001 | ARIF ABDULRAHMAN | 2 |
| 002 | ASRI WELAS | 0 |
| 003 | RASMI RAHMAWATI | 1 |
| 004 | UDIN R. KAMAL | 1 |
| 005 | ARDIYANSAH SAPUTRA | 0 |
| 006 | SEPTIRINA HERMAWAN | 2 |
| 007 | HERMAN BARLIN | 1 |

## Format tanggal

Di database tanggal disimpan sebagai `DATE`, misalnya `2010-11-01`. Di layar ditulis hari-bulan singkat-tahun. November disingkat **Nop**, sesuai soal: `01-Nop-2010`. Tanggal lahir memakai pola yang sama, misalnya `12-Mar-1987`.

## Struktur folder

```text
index.php                  menu
config/                    koneksi dan format tanggal
includes/layout.php        kerangka halaman
app/perpustakaan/          query dan hitungan, tanpa HTML
app/pembiayaan/            query laporan pinjaman perusahaan
view/perpustakaan/         HTML daftar, form, dan laporan
perpustakaan/              halaman yang dibuka pengguna
sql/                       skema dan data awal
```

Halaman di `perpustakaan/` membaca aksi, memanggil function di `app/`, lalu menyertakan file di `view/`.
