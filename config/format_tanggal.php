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
