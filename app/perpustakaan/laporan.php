<?php

if (!defined('STATUS_TERPINJAM')) {
    define('STATUS_TERPINJAM', 0);
}

if (!defined('TARIF_DENDA_PER_HARI')) {
    define('TARIF_DENDA_PER_HARI', 5000);
}

/**
 * Ambil buku yang masih bisa dipinjam.
 *
 * Buku tersedia jika tidak punya baris pinjam berstatus terpinjam.
 * Filter diselesaikan di SQL, satu query, tanpa kolom yang tidak tampil.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @return array|null Daftar buku, atau null jika query gagal.
 */
function ambilBukuTersedia($koneksi)
{
    $statusTerpinjam = (int) STATUS_TERPINJAM;
    $sql = 'SELECT b.ID, b.JUDUL, b.ISBN, b.PENGARANG '
        . 'FROM TBL_BUKU b '
        . 'WHERE NOT EXISTS ('
        . 'SELECT 1 FROM TBL_PINJAM p '
        . 'WHERE p.ID_BUKU = b.ID AND p.STATUS = ' . $statusTerpinjam
        . ') ORDER BY b.ID';

    $hasil = mysqli_query($koneksi, $sql);

    if (!$hasil) {
        return null;
    }

    $daftar = array();

    while ($baris = mysqli_fetch_assoc($hasil)) {
        $daftar[] = $baris;
    }

    mysqli_free_result($hasil);

    return $daftar;
}

/**
 * Hitung denda satu pinjaman yang belum kembali.
 *
 * Denda nol jika tanggal hitung belum melewati tanggal batas.
 * Setelah lewat, setiap hari dikali tarif harian. Contoh: batas
 * 2010-11-08 dan hitung 2010-11-10 menghasilkan 10000.
 *
 * @param string $tanggalBatas Tanggal batas Y-m-d.
 * @param string $tanggalHitung Tanggal acuan Y-m-d, biasanya hari ini.
 * @return int Nominal denda dalam rupiah.
 */
function hitungDendaPinjaman($tanggalBatas, $tanggalHitung)
{
    $batas = strtotime($tanggalBatas . ' 00:00:00');
    $hitung = strtotime($tanggalHitung . ' 00:00:00');

    if ($batas === false || $batas === -1 || $hitung === false || $hitung === -1) {
        return 0;
    }

    $selisihDetik = $hitung - $batas;

    if ($selisihDetik <= 0) {
        return 0;
    }

    $hari = (int) floor($selisihDetik / 86400);

    return $hari * TARIF_DENDA_PER_HARI;
}

/**
 * Ambil pinjaman yang belum dikembalikan, beserta peminjam dan judul buku.
 *
 * Status terpinjam disaring di SQL. Denda tidak disimpan dan tidak dihitung di sini.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @return array|null Daftar pinjaman, atau null jika query gagal.
 */
function ambilPinjamBelumKembali($koneksi)
{
    $statusTerpinjam = (int) STATUS_TERPINJAM;
    $sql = 'SELECT p.ID, p.TGL_BATAS, s.NAMA, b.JUDUL '
        . 'FROM TBL_PINJAM p '
        . 'INNER JOIN TBL_SISWA s ON s.NIS = p.NIS '
        . 'INNER JOIN TBL_BUKU b ON b.ID = p.ID_BUKU '
        . 'WHERE p.STATUS = ' . $statusTerpinjam . ' '
        . 'ORDER BY p.ID';

    $hasil = mysqli_query($koneksi, $sql);

    if (!$hasil) {
        return null;
    }

    $daftar = array();

    while ($baris = mysqli_fetch_assoc($hasil)) {
        $daftar[] = $baris;
    }

    mysqli_free_result($hasil);

    return $daftar;
}

/**
 * Isi nominal denda pada baris pinjaman yang sudah diambil.
 *
 * @param array $daftarPinjam Baris dari ambilPinjamBelumKembali().
 * @param string $tanggalHitung Tanggal acuan Y-m-d.
 * @return array Baris yang sama ditambah kunci denda.
 */
function isiDendaPinjam($daftarPinjam, $tanggalHitung)
{
    $hasil = array();

    foreach ($daftarPinjam as $baris) {
        $baris['denda'] = hitungDendaPinjaman($baris['TGL_BATAS'], $tanggalHitung);
        $hasil[] = $baris;
    }

    return $hasil;
}
