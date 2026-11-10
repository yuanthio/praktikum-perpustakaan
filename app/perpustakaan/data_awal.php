<?php

/**
 * Hitung baris data awal ketiga tabel perpustakaan dalam satu query.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @return array|null jumlah_siswa, jumlah_buku, dan jumlah_pinjam, atau null jika gagal.
 */
function ambilJumlahDataAwal($koneksi)
{
    $sql = 'SELECT '
        . '(SELECT COUNT(NIS) FROM TBL_SISWA) AS jumlah_siswa, '
        . '(SELECT COUNT(ID) FROM TBL_BUKU) AS jumlah_buku, '
        . '(SELECT COUNT(ID) FROM TBL_PINJAM) AS jumlah_pinjam';

    $hasil = mysqli_query($koneksi, $sql);

    if (!$hasil) {
        return null;
    }

    $baris = mysqli_fetch_assoc($hasil);
    mysqli_free_result($hasil);

    if (!is_array($baris)) {
        return null;
    }

    return $baris;
}
