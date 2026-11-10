<?php

if (!defined('STATUS_TERPINJAM')) {
    define('STATUS_TERPINJAM', 0);
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
