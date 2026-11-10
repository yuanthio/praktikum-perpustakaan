<?php

/**
 * Bulan singkat yang dipakai di layar.
 * November ditulis Nop, sesuai contoh soal (01-Nop-2010).
 *
 * @return array Nomor bulan 1–12 ke label singkat.
 */
function daftarBulanSingkat()
{
    return array(
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'Mei',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Agu',
        9 => 'Sep',
        10 => 'Okt',
        11 => 'Nop',
        12 => 'Des',
    );
}

/**
 * Nama bulan penuh untuk dropdown tanggal lahir.
 *
 * @return array Nomor bulan 1–12 ke nama bulan.
 */
function daftarBulanLengkap()
{
    return array(
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    );
}

/**
 * Pilihan hari 1 sampai 31.
 *
 * @return array Daftar angka hari.
 */
function daftarHariLahir()
{
    $daftar = array();
    $hari = 1;

    while ($hari <= 31) {
        $daftar[] = $hari;
        $hari++;
    }

    return $daftar;
}

/**
 * Pilihan tahun lahir yang mencakup data soal dan contoh dropdown 2010.
 *
 * @return array Daftar tahun dari 1970 sampai 2010.
 */
function daftarTahunLahir()
{
    $daftar = array();
    $tahun = 1970;

    while ($tahun <= 2010) {
        $daftar[] = $tahun;
        $tahun++;
    }

    return $daftar;
}

/**
 * Pilihan tahun untuk tanggal pinjam dan kembali.
 *
 * @return array Daftar tahun dari 2000 sampai 2026.
 */
function daftarTahunPinjam()
{
    $daftar = array();
    $tahun = 2000;

    while ($tahun <= 2026) {
        $daftar[] = $tahun;
        $tahun++;
    }

    return $daftar;
}

/**
 * Ubah DATE MySQL menjadi tampilan soal.
 * Contoh: 1987-03-12 menjadi 12-Mar-1987. Nilai kosong tetap kosong.
 *
 * @param string|null $tanggalMysql Tanggal Y-m-d dari database.
 * @return string Tanggal tampilan, atau string kosong.
 */
function formatTanggalTampil($tanggalMysql)
{
    if ($tanggalMysql === null || $tanggalMysql === '' || $tanggalMysql === '0000-00-00') {
        return '';
    }

    $tanggalMysql = substr($tanggalMysql, 0, 10);
    $bagian = explode('-', $tanggalMysql);

    if (count($bagian) !== 3) {
        return '';
    }

    $bulan = (int) $bagian[1];
    $daftarBulan = daftarBulanSingkat();

    if (!isset($daftarBulan[$bulan])) {
        return '';
    }

    return $bagian[2] . '-' . $daftarBulan[$bulan] . '-' . $bagian[0];
}
