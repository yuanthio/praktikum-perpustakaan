<?php

/**
 * Satu koneksi MySQL untuk seluruh request.
 * Kredensial ini mengikuti XAMPP lokal (root tanpa kata sandi).
 */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'perpustakaan');
define('DB_NAME_PEMBIAYAAN', 'pinjaman_perusahaan');

/**
 * Buat koneksi MySQL ke satu nama database.
 *
 * @param string $namaDatabase Nama database yang dibuka.
 * @return mysqli|null Koneksi yang siap dipakai, atau null jika gagal.
 */
function buatKoneksi($namaDatabase)
{
    if (!function_exists('mysqli_connect')) {
        return null;
    }

    mysqli_report(MYSQLI_REPORT_OFF);
    $koneksi = mysqli_connect(DB_HOST, DB_USER, DB_PASS, $namaDatabase);

    if ($koneksi) {
        mysqli_set_charset($koneksi, 'utf8mb4');
    }

    return $koneksi;
}

/**
 * Buka koneksi perpustakaan sekali per request.
 * Pemanggilan berikutnya mengembalikan koneksi yang sama.
 *
 * @return mysqli|null Koneksi database perpustakaan, atau null jika gagal.
 */
function bukaKoneksi()
{
    static $koneksi = null;
    static $sudahDicoba = false;

    if ($sudahDicoba) {
        return $koneksi;
    }

    $sudahDicoba = true;
    $koneksi = buatKoneksi(DB_NAME);

    return $koneksi;
}

/**
 * Buka koneksi pinjaman perusahaan sekali per request.
 * Tidak memakai koneksi perpustakaan.
 *
 * @return mysqli|null Koneksi database pembiayaan, atau null jika gagal.
 */
function bukaKoneksiPembiayaan()
{
    static $koneksi = null;
    static $sudahDicoba = false;

    if ($sudahDicoba) {
        return $koneksi;
    }

    $sudahDicoba = true;
    $koneksi = buatKoneksi(DB_NAME_PEMBIAYAAN);

    return $koneksi;
}

/**
 * Nama database yang sedang dipakai koneksi ini.
 *
 * @param mysqli $koneksi Koneksi dari bukaKoneksi().
 * @return string Nama database, atau string kosong jika tidak terbaca.
 */
function ambilNamaDatabase($koneksi)
{
    $hasil = mysqli_query($koneksi, 'SELECT DATABASE() AS nama_database');

    if (!$hasil) {
        return '';
    }

    $baris = mysqli_fetch_assoc($hasil);
    mysqli_free_result($hasil);

    if (!isset($baris['nama_database'])) {
        return '';
    }

    return $baris['nama_database'];
}
