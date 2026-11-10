<?php

if (!defined('LAMA_HARI_PINJAM')) {
    define('LAMA_HARI_PINJAM', 7);
}

/**
 * Hitung tanggal batas peminjaman, yaitu tanggal pinjam ditambah 7 hari.
 *
 * @param string $tanggalPinjam Tanggal Y-m-d.
 * @return string Tanggal batas Y-m-d, atau string kosong jika tanggal pinjam tidak sah.
 */
function hitungTanggalBatas($tanggalPinjam)
{
    $waktu = strtotime($tanggalPinjam . ' +' . LAMA_HARI_PINJAM . ' days');

    if ($waktu === false || $waktu === -1) {
        return '';
    }

    return date('Y-m-d', $waktu);
}

/**
 * Label status di layar. Di database tetap 0 atau 1.
 *
 * @param string|int $status Kode status.
 * @return string Terpinjam, Kembali, atau string kosong.
 */
function labelStatusPinjam($status)
{
    if ((string) $status === '0') {
        return 'Terpinjam';
    }

    if ((string) $status === '1') {
        return 'Kembali';
    }

    return '';
}

/**
 * Ambil daftar peminjaman beserta nama siswa dan judul buku. Satu query.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @return array|null Daftar pinjaman, atau null jika query gagal.
 */
function ambilSemuaPinjam($koneksi)
{
    $sql = 'SELECT p.ID, p.TGL_PINJAM, p.TGL_BATAS, p.TGL_KEMBALI, p.STATUS, s.NAMA, b.JUDUL '
        . 'FROM TBL_PINJAM p '
        . 'INNER JOIN TBL_SISWA s ON s.NIS = p.NIS '
        . 'INNER JOIN TBL_BUKU b ON b.ID = p.ID_BUKU '
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
 * Ambil satu transaksi peminjaman.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @param string $id ID pinjam, misalnya P001.
 * @return array|null|false Baris pinjaman, null jika tidak ada, false jika query gagal.
 */
function ambilPinjam($koneksi, $id)
{
    $sql = 'SELECT ID, TGL_PINJAM, TGL_KEMBALI, STATUS, NIS, ID_BUKU FROM TBL_PINJAM WHERE ID = ?';
    $perintah = mysqli_prepare($koneksi, $sql);

    if (!$perintah) {
        return false;
    }

    mysqli_stmt_bind_param($perintah, 's', $id);

    if (!mysqli_stmt_execute($perintah)) {
        mysqli_stmt_close($perintah);
        return false;
    }

    $hasil = mysqli_stmt_get_result($perintah);

    if (!$hasil) {
        mysqli_stmt_close($perintah);
        return false;
    }

    $baris = mysqli_fetch_assoc($hasil);
    mysqli_free_result($hasil);
    mysqli_stmt_close($perintah);

    if (!is_array($baris)) {
        return null;
    }

    return $baris;
}

/**
 * Daftar siswa untuk dropdown NIS.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @return array|null Baris NIS dan NAMA, atau null jika gagal.
 */
function ambilOpsiSiswa($koneksi)
{
    $sql = 'SELECT NIS, NAMA FROM TBL_SISWA ORDER BY NIS';
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
 * Daftar buku untuk dropdown ID buku.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @return array|null Baris ID dan JUDUL, atau null jika gagal.
 */
function ambilOpsiBuku($koneksi)
{
    $sql = 'SELECT ID, JUDUL FROM TBL_BUKU ORDER BY ID';
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
 * Simpan transaksi baru. Tanggal batas sudah dihitung pemanggil.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @param string $id ID pinjam.
 * @param string $tglPinjam Tanggal Y-m-d.
 * @param string $tglBatas Tanggal Y-m-d.
 * @param string|null $tglKembali Tanggal Y-m-d, atau null jika belum kembali.
 * @param int $status 0 terpinjam, 1 kembali.
 * @param string $nis NIS siswa.
 * @param string $idBuku ID buku.
 * @return string ok, duplikat, relasi, atau gagal.
 */
function simpanPinjam($koneksi, $id, $tglPinjam, $tglBatas, $tglKembali, $status, $nis, $idBuku)
{
    if ($tglKembali === null || $tglKembali === '') {
        $sql = 'INSERT INTO TBL_PINJAM (ID, TGL_PINJAM, TGL_BATAS, TGL_KEMBALI, STATUS, NIS, ID_BUKU) '
            . 'VALUES (?, ?, ?, NULL, ?, ?, ?)';
        $perintah = mysqli_prepare($koneksi, $sql);

        if (!$perintah) {
            return 'gagal';
        }

        mysqli_stmt_bind_param($perintah, 'ssisss', $id, $tglPinjam, $tglBatas, $status, $nis, $idBuku);
    } else {
        $sql = 'INSERT INTO TBL_PINJAM (ID, TGL_PINJAM, TGL_BATAS, TGL_KEMBALI, STATUS, NIS, ID_BUKU) '
            . 'VALUES (?, ?, ?, ?, ?, ?, ?)';
        $perintah = mysqli_prepare($koneksi, $sql);

        if (!$perintah) {
            return 'gagal';
        }

        mysqli_stmt_bind_param($perintah, 'sssisss', $id, $tglPinjam, $tglBatas, $tglKembali, $status, $nis, $idBuku);
    }

    $berhasil = mysqli_stmt_execute($perintah);
    $kode = 'ok';

    if (!$berhasil) {
        $nomorGalat = mysqli_stmt_errno($perintah);

        if ($nomorGalat === 1062) {
            $kode = 'duplikat';
        } elseif ($nomorGalat === 1452) {
            $kode = 'relasi';
        } else {
            $kode = 'gagal';
        }
    }

    mysqli_stmt_close($perintah);

    return $kode;
}

/**
 * Ubah transaksi peminjaman.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @param string $idLama ID yang sedang diubah.
 * @param string $id ID baru.
 * @param string $tglPinjam Tanggal Y-m-d.
 * @param string $tglBatas Tanggal Y-m-d.
 * @param string|null $tglKembali Tanggal Y-m-d, atau null.
 * @param int $status 0 atau 1.
 * @param string $nis NIS siswa.
 * @param string $idBuku ID buku.
 * @return string ok, duplikat, relasi, atau gagal.
 */
function ubahPinjam($koneksi, $idLama, $id, $tglPinjam, $tglBatas, $tglKembali, $status, $nis, $idBuku)
{
    if ($tglKembali === null || $tglKembali === '') {
        $sql = 'UPDATE TBL_PINJAM SET ID = ?, TGL_PINJAM = ?, TGL_BATAS = ?, TGL_KEMBALI = NULL, '
            . 'STATUS = ?, NIS = ?, ID_BUKU = ? WHERE ID = ?';
        $perintah = mysqli_prepare($koneksi, $sql);

        if (!$perintah) {
            return 'gagal';
        }

        mysqli_stmt_bind_param($perintah, 'ssissss', $id, $tglPinjam, $tglBatas, $status, $nis, $idBuku, $idLama);
    } else {
        $sql = 'UPDATE TBL_PINJAM SET ID = ?, TGL_PINJAM = ?, TGL_BATAS = ?, TGL_KEMBALI = ?, '
            . 'STATUS = ?, NIS = ?, ID_BUKU = ? WHERE ID = ?';
        $perintah = mysqli_prepare($koneksi, $sql);

        if (!$perintah) {
            return 'gagal';
        }

        mysqli_stmt_bind_param($perintah, 'ssssisss', $id, $tglPinjam, $tglBatas, $tglKembali, $status, $nis, $idBuku, $idLama);
    }

    $berhasil = mysqli_stmt_execute($perintah);
    $kode = 'ok';

    if (!$berhasil) {
        $nomorGalat = mysqli_stmt_errno($perintah);

        if ($nomorGalat === 1062) {
            $kode = 'duplikat';
        } elseif ($nomorGalat === 1452) {
            $kode = 'relasi';
        } else {
            $kode = 'gagal';
        }
    }

    mysqli_stmt_close($perintah);

    return $kode;
}

/**
 * Hapus satu transaksi peminjaman.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @param string $id ID pinjam.
 * @return string ok, tidak-ada, atau gagal.
 */
function hapusPinjam($koneksi, $id)
{
    $sql = 'DELETE FROM TBL_PINJAM WHERE ID = ?';
    $perintah = mysqli_prepare($koneksi, $sql);

    if (!$perintah) {
        return 'gagal';
    }

    mysqli_stmt_bind_param($perintah, 's', $id);
    $berhasil = mysqli_stmt_execute($perintah);
    $terhapus = mysqli_stmt_affected_rows($perintah);
    mysqli_stmt_close($perintah);

    if (!$berhasil) {
        return 'gagal';
    }

    if ($terhapus < 1) {
        return 'tidak-ada';
    }

    return 'ok';
}

/**
 * Susun tanggal Y-m-d dari tiga bagian dropdown. Kosong jika ketiganya kosong.
 *
 * @param string $hari Hari.
 * @param string $bulan Bulan.
 * @param string $tahun Tahun.
 * @return array lengkap, kosong, sah, tanggal.
 */
function susunTanggalBagian($hari, $bulan, $tahun)
{
    $hasil = array(
        'kosong' => false,
        'sah' => false,
        'tanggal' => '',
    );

    if ($hari === '' && $bulan === '' && $tahun === '') {
        $hasil['kosong'] = true;
        $hasil['sah'] = true;
        return $hasil;
    }

    if ($hari === '' || $bulan === '' || $tahun === '') {
        return $hasil;
    }

    $hariAngka = (int) $hari;
    $bulanAngka = (int) $bulan;
    $tahunAngka = (int) $tahun;

    if ($tahunAngka < 2000 || $tahunAngka > 2026 || !checkdate($bulanAngka, $hariAngka, $tahunAngka)) {
        return $hasil;
    }

    $hasil['sah'] = true;
    $hasil['tanggal'] = sprintf('%04d-%02d-%02d', $tahunAngka, $bulanAngka, $hariAngka);

    return $hasil;
}

/**
 * Susun dan periksa isian form peminjaman.
 *
 * @param string $id ID pinjam.
 * @param string $hariPinjam Hari pinjam.
 * @param string $bulanPinjam Bulan pinjam.
 * @param string $tahunPinjam Tahun pinjam.
 * @param string $hariKembali Hari kembali.
 * @param string $bulanKembali Bulan kembali.
 * @param string $tahunKembali Tahun kembali.
 * @param string $status Status 0 atau 1.
 * @param string $nis NIS siswa.
 * @param string $idBuku ID buku.
 * @return array Hasil periksa beserta nilai yang sudah dinormalkan.
 */
function periksaFormPinjam($id, $hariPinjam, $bulanPinjam, $tahunPinjam, $hariKembali, $bulanKembali, $tahunKembali, $status, $nis, $idBuku)
{
    $id = strtoupper($id);
    $idBuku = strtoupper($idBuku);
    $hasil = array(
        'sah' => false,
        'pesan' => '',
        'id' => $id,
        'hariPinjam' => (int) $hariPinjam,
        'bulanPinjam' => (int) $bulanPinjam,
        'tahunPinjam' => (int) $tahunPinjam,
        'hariKembali' => $hariKembali,
        'bulanKembali' => $bulanKembali,
        'tahunKembali' => $tahunKembali,
        'status' => $status,
        'nis' => $nis,
        'idBuku' => $idBuku,
        'tglPinjam' => '',
        'tglBatas' => '',
        'tglKembali' => null,
    );
    $pesan = array();
    $pinjam = susunTanggalBagian($hariPinjam, $bulanPinjam, $tahunPinjam);
    $kembali = susunTanggalBagian($hariKembali, $bulanKembali, $tahunKembali);

    if (!preg_match('/^P[0-9]{3}$/', $id)) {
        $pesan[] = 'ID harus seperti P008.';
    }

    if (!$pinjam['sah'] || $pinjam['kosong']) {
        $pesan[] = 'Tanggal pinjam tidak sah.';
    }

    if (!$kembali['sah']) {
        $pesan[] = 'Tanggal kembali belum lengkap atau tidak sah.';
    }

    if ($status !== '0' && $status !== '1') {
        $pesan[] = 'Status belum dipilih.';
    } elseif ($status === '1' && $kembali['kosong']) {
        $pesan[] = 'Tanggal kembali wajib diisi jika status Kembali.';
    }

    if (!preg_match('/^[0-9]{3}$/', $nis)) {
        $pesan[] = 'Siswa belum dipilih.';
    }

    if (!preg_match('/^B[0-9]{3}$/', $idBuku)) {
        $pesan[] = 'Buku belum dipilih.';
    }

    if (count($pesan) > 0) {
        $hasil['pesan'] = implode(' ', $pesan);
        return $hasil;
    }

    $hasil['sah'] = true;
    $hasil['tglPinjam'] = $pinjam['tanggal'];
    $hasil['tglBatas'] = hitungTanggalBatas($pinjam['tanggal']);
    $hasil['tglKembali'] = $kembali['kosong'] ? null : $kembali['tanggal'];
    $hasil['hariPinjam'] = (int) $hariPinjam;
    $hasil['bulanPinjam'] = (int) $bulanPinjam;
    $hasil['tahunPinjam'] = (int) $tahunPinjam;

    return $hasil;
}
