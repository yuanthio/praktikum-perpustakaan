# Rancangan Pinjaman dan Pembiayaan Perusahaan

Soal 1 meminta struktur data dan relasinya. Ini bukan halaman transaksi. Skema ada di `sql/pinjaman_perusahaan.sql` (database `pinjaman_perusahaan`). Function laporannya ada di `app/pembiayaan/pinjaman.php`. Database perpustakaan tidak dicampur ke sini.

## Relasi

```text
TBL_KARYAWAN
    |
    | satu karyawan, banyak pinjaman
    v
TBL_PINJAMAN
    |                      |                    |
    | banyak transfer      | banyak pemakaian   | banyak cicilan
    v                      v                    v
TBL_TRANSFER_DANA     TBL_PEMAKAIAN_DANA      TBL_CICILAN
    |
    | banyak penarikan dari setoran yang sudah masuk
    v
TBL_PENARIKAN_DANA
```

| Tabel | Isi | Relasi |
| --- | --- | --- |
| `TBL_KARYAWAN` | Identitas peminjam | Induk |
| `TBL_PINJAMAN` | Pokok pinjaman satu karyawan | Banyak pinjaman per karyawan |
| `TBL_TRANSFER_DANA` | Setoran bertahap ke rekening | Banyak transfer per pinjaman |
| `TBL_PENARIKAN_DANA` | Dana yang ditarik setelah masuk rekening | Banyak penarikan per transfer |
| `TBL_PEMAKAIAN_DANA` | Penggunaan dana oleh peminjam | Banyak pemakaian per pinjaman |
| `TBL_CICILAN` | Angsuran pelunasan | Banyak cicilan per pinjaman |

Penarikan menunjuk ke transfer, bukan langsung ke pinjaman. Setiap penarikan jadi milik setoran yang sudah masuk rekening. Pinjaman pemilik penarikan itu didapat lewat transfernya.

## Kolom

Nominal rupiah utuh (`BIGINT`). Tanggal bertipe `DATE`. Mesin `InnoDB` supaya kunci tamu menolak baris tanpa induk. Kolom relasi punya index.

### TBL_KARYAWAN

| Kolom | Keterangan |
| --- | --- |
| `ID` | Kunci utama |
| `NOMOR_INDUK` | Nomor karyawan, unik |
| `NAMA` | Nama karyawan |

### TBL_PINJAMAN

| Kolom | Keterangan |
| --- | --- |
| `ID` | Kunci utama |
| `ID_KARYAWAN` | Kunci tamu ke karyawan |
| `NOMOR_PINJAMAN` | Nomor pinjaman, unik |
| `TGL_PINJAMAN` | Tanggal pinjaman |
| `POKOK` | Total pinjaman, misalnya 100000000 |

### TBL_TRANSFER_DANA

Dana diberikan lewat transfer rekening dan boleh beberapa tahap.

| Kolom | Keterangan |
| --- | --- |
| `ID` | Kunci utama |
| `ID_PINJAMAN` | Kunci tamu ke pinjaman |
| `TAHAP` | Urutan setoran, unik per pinjaman |
| `TGL_TRANSFER` | Tanggal transfer |
| `NOMINAL` | Nilai tahap itu |
| `REKENING_TUJUAN` | Rekening penerima |
| `NAMA_BANK` | Bank rekening tujuan |

### TBL_PENARIKAN_DANA

| Kolom | Keterangan |
| --- | --- |
| `ID` | Kunci utama |
| `ID_TRANSFER` | Kunci tamu ke transfer yang sudah disetor |
| `TGL_PENARIKAN` | Tanggal penarikan |
| `NOMINAL` | Nilai yang ditarik |

### TBL_PEMAKAIAN_DANA

| Kolom | Keterangan |
| --- | --- |
| `ID` | Kunci utama |
| `ID_PINJAMAN` | Kunci tamu ke pinjaman |
| `TGL_PEMAKAIAN` | Tanggal pemakaian |
| `NOMINAL` | Nilai yang dipakai |
| `KEPERLUAN` | Penggunaan dana |

### TBL_CICILAN

| Kolom | Keterangan |
| --- | --- |
| `ID` | Kunci utama |
| `ID_PINJAMAN` | Kunci tamu ke pinjaman |
| `ANGSURAN_KE` | Urutan cicilan, unik per pinjaman |
| `TGL_CICILAN` | Tanggal bayar |
| `NOMINAL` | Nilai angsuran |

## Laporan

Setiap laporan satu `SELECT` di `app/pembiayaan/pinjaman.php`. Nomor pinjaman dikirim lewat prepared statement. Kolom yang tidak tampil tidak diambil.

| Function | Isi laporan |
| --- | --- |
| `ambilLaporanTransfer()` | Tahap, tanggal, nominal, rekening, bank |
| `ambilLaporanPenarikan()` | Tahap setoran, nominal setor, tanggal tarik, nominal tarik |
| `ambilLaporanPemakaian()` | Tanggal, nominal, keperluan |
| `ambilLaporanCicilan()` | Angsuran, tanggal, nominal, plus pokok |
| `jumlahNominal()` | Jumlah satu kolom dari baris yang sudah diambil |
| `hitungSisaPokok()` | Pokok dikurangi jumlah cicilan |

## Contoh PJ001

Karyawan `K001` (KARYAWAN CONTOH) meminjam `PJ001` pada 10 Jan 2026, pokok Rp100.000.000.

| Jejak | Data |
| --- | --- |
| Transfer tahap 1 | 10 Jan 2026, Rp50.000.000, rekening 1234567890, BCA |
| Transfer tahap 2 | 10 Feb 2026, Rp50.000.000, rekening yang sama |
| Penarikan | 15 Jan 2026, Rp20.000.000, dari transfer tahap 1 |
| Pemakaian | 20 Jan 2026, Rp15.000.000, pembelian bahan baku |
| Cicilan | Angsuran 1 dan 2, masing-masing Rp10.000.000 |

Jumlah dua transfer sama dengan pokok. Penarikan tahap 1 tidak melebihi setoran tahap itu. Sisa pokok setelah dua cicilan = Rp80.000.000.
