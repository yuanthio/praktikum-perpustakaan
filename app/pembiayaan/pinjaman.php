<?php

/**
 * Ambil baris laporan dengan satu prepared statement.
 * Filter nomor pinjaman dikirim sebagai parameter, bukan disambung ke SQL.
 *
 * @param mysqli $koneksi Koneksi database pinjaman perusahaan.
 * @param string $sql Query SELECT yang memakai satu tanda tanya.
 * @param string $nomorPinjaman Nomor pinjaman, misalnya PJ001.
 * @return array|null Daftar baris, atau null jika query gagal.
 */
function ambilBarisPinjaman($koneksi, $sql, $nomorPinjaman)
{
    $perintah = mysqli_prepare($koneksi, $sql);

    if (!$perintah) {
        return null;
    }

    mysqli_stmt_bind_param($perintah, 's', $nomorPinjaman);

    if (!mysqli_stmt_execute($perintah)) {
        mysqli_stmt_close($perintah);
        return null;
    }

    $hasil = mysqli_stmt_get_result($perintah);

    if (!$hasil) {
        mysqli_stmt_close($perintah);
        return null;
    }

    $daftar = array();

    while ($baris = mysqli_fetch_assoc($hasil)) {
        $daftar[] = $baris;
    }

    mysqli_free_result($hasil);
    mysqli_stmt_close($perintah);

    return $daftar;
}

/**
 * Laporan pencairan bertahap: tiap tahap, rekening, dan nominal.
 *
 * @param mysqli $koneksi Koneksi database pinjaman perusahaan.
 * @param string $nomorPinjaman Nomor pinjaman.
 * @return array|null Baris transfer, atau null jika query gagal.
 */
function ambilLaporanTransfer($koneksi, $nomorPinjaman)
{
    $sql = 'SELECT p.NOMOR_PINJAMAN, p.POKOK, t.TAHAP, t.TGL_TRANSFER, t.NOMINAL, '
        . 't.REKENING_TUJUAN, t.NAMA_BANK '
        . 'FROM TBL_PINJAMAN p '
        . 'INNER JOIN TBL_TRANSFER_DANA t ON t.ID_PINJAMAN = p.ID '
        . 'WHERE p.NOMOR_PINJAMAN = ? '
        . 'ORDER BY t.TAHAP';

    return ambilBarisPinjaman($koneksi, $sql, $nomorPinjaman);
}

/**
 * Laporan penarikan dari dana yang sudah ditransfer ke rekening.
 *
 * @param mysqli $koneksi Koneksi database pinjaman perusahaan.
 * @param string $nomorPinjaman Nomor pinjaman.
 * @return array|null Baris penarikan, atau null jika query gagal.
 */
function ambilLaporanPenarikan($koneksi, $nomorPinjaman)
{
    $sql = 'SELECT p.NOMOR_PINJAMAN, t.TAHAP, t.NOMINAL AS NOMINAL_SETOR, '
        . 'n.TGL_PENARIKAN, n.NOMINAL AS NOMINAL_TARIK '
        . 'FROM TBL_PINJAMAN p '
        . 'INNER JOIN TBL_TRANSFER_DANA t ON t.ID_PINJAMAN = p.ID '
        . 'INNER JOIN TBL_PENARIKAN_DANA n ON n.ID_TRANSFER = t.ID '
        . 'WHERE p.NOMOR_PINJAMAN = ? '
        . 'ORDER BY n.TGL_PENARIKAN';

    return ambilBarisPinjaman($koneksi, $sql, $nomorPinjaman);
}

/**
 * Laporan pemakaian dana oleh peminjam.
 *
 * @param mysqli $koneksi Koneksi database pinjaman perusahaan.
 * @param string $nomorPinjaman Nomor pinjaman.
 * @return array|null Baris pemakaian, atau null jika query gagal.
 */
function ambilLaporanPemakaian($koneksi, $nomorPinjaman)
{
    $sql = 'SELECT p.NOMOR_PINJAMAN, u.TGL_PEMAKAIAN, u.NOMINAL, u.KEPERLUAN '
        . 'FROM TBL_PINJAMAN p '
        . 'INNER JOIN TBL_PEMAKAIAN_DANA u ON u.ID_PINJAMAN = p.ID '
        . 'WHERE p.NOMOR_PINJAMAN = ? '
        . 'ORDER BY u.TGL_PEMAKAIAN';

    return ambilBarisPinjaman($koneksi, $sql, $nomorPinjaman);
}

/**
 * Laporan cicilan pelunasan satu pinjaman.
 *
 * @param mysqli $koneksi Koneksi database pinjaman perusahaan.
 * @param string $nomorPinjaman Nomor pinjaman.
 * @return array|null Baris cicilan, atau null jika query gagal.
 */
function ambilLaporanCicilan($koneksi, $nomorPinjaman)
{
    $sql = 'SELECT p.NOMOR_PINJAMAN, p.POKOK, c.ANGSURAN_KE, c.TGL_CICILAN, c.NOMINAL '
        . 'FROM TBL_PINJAMAN p '
        . 'INNER JOIN TBL_CICILAN c ON c.ID_PINJAMAN = p.ID '
        . 'WHERE p.NOMOR_PINJAMAN = ? '
        . 'ORDER BY c.ANGSURAN_KE';

    return ambilBarisPinjaman($koneksi, $sql, $nomorPinjaman);
}

/**
 * Jumlahkan satu kolom nominal dari baris yang sudah diambil.
 *
 * @param array $daftar Baris laporan.
 * @param string $kunciNominal Nama kolom nominal.
 * @return int Jumlah rupiah.
 */
function jumlahNominal($daftar, $kunciNominal)
{
    $jumlah = 0;

    foreach ($daftar as $baris) {
        $jumlah += (int) $baris[$kunciNominal];
    }

    return $jumlah;
}

/**
 * Sisa pokok setelah cicilan yang sudah tercatat.
 *
 * @param int $pokok Pokok pinjaman.
 * @param array $daftarCicilan Baris dari ambilLaporanCicilan().
 * @return int Sisa rupiah. Tidak kurang dari nol.
 */
function hitungSisaPokok($pokok, $daftarCicilan)
{
    $sisa = (int) $pokok - jumlahNominal($daftarCicilan, 'NOMINAL');

    if ($sisa < 0) {
        return 0;
    }

    return $sisa;
}
