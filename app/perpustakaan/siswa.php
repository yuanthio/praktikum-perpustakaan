<?php

/**
 * Ambil seluruh siswa untuk tampilan list. Satu query, kolom yang tampil saja.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @return array|null Daftar siswa, atau null jika query gagal.
 */
function ambilSemuaSiswa($koneksi)
{
    $sql = 'SELECT NIS, NAMA, TGL_LAHIR, KELAMIN FROM TBL_SISWA ORDER BY NIS';
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
 * Ambil satu siswa menurut NIS.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @param string $nis NIS siswa.
 * @return array|null Baris siswa, null jika tidak ada, false jika query gagal.
 */
function ambilSiswa($koneksi, $nis)
{
    $sql = 'SELECT NIS, NAMA, TGL_LAHIR, KELAMIN FROM TBL_SISWA WHERE NIS = ?';
    $perintah = mysqli_prepare($koneksi, $sql);

    if (!$perintah) {
        return false;
    }

    mysqli_stmt_bind_param($perintah, 's', $nis);

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
 * Hitung peminjaman satu siswa. Dipakai sebelum NIS diubah atau siswa dihapus.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @param string $nis NIS siswa.
 * @return int|null Jumlah pinjaman, atau null jika query gagal.
 */
function jumlahPinjamSiswa($koneksi, $nis)
{
    $sql = 'SELECT COUNT(ID) AS jumlah FROM TBL_PINJAM WHERE NIS = ?';
    $perintah = mysqli_prepare($koneksi, $sql);

    if (!$perintah) {
        return null;
    }

    mysqli_stmt_bind_param($perintah, 's', $nis);

    if (!mysqli_stmt_execute($perintah)) {
        mysqli_stmt_close($perintah);
        return null;
    }

    $hasil = mysqli_stmt_get_result($perintah);

    if (!$hasil) {
        mysqli_stmt_close($perintah);
        return null;
    }

    $baris = mysqli_fetch_assoc($hasil);
    mysqli_free_result($hasil);
    mysqli_stmt_close($perintah);

    if (!isset($baris['jumlah'])) {
        return null;
    }

    return (int) $baris['jumlah'];
}

/**
 * Simpan siswa baru.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @param string $nis NIS.
 * @param string $nama Nama siswa.
 * @param string $tglLahir Tanggal Y-m-d.
 * @param string $kelamin L atau P.
 * @return string ok, duplikat, atau gagal.
 */
function simpanSiswa($koneksi, $nis, $nama, $tglLahir, $kelamin)
{
    $sql = 'INSERT INTO TBL_SISWA (NIS, NAMA, TGL_LAHIR, KELAMIN) VALUES (?, ?, ?, ?)';
    $perintah = mysqli_prepare($koneksi, $sql);

    if (!$perintah) {
        return 'gagal';
    }

    mysqli_stmt_bind_param($perintah, 'ssss', $nis, $nama, $tglLahir, $kelamin);
    $berhasil = mysqli_stmt_execute($perintah);
    $kode = 'ok';

    if (!$berhasil) {
        if (mysqli_stmt_errno($perintah) === 1062) {
            $kode = 'duplikat';
        } else {
            $kode = 'gagal';
        }
    }

    mysqli_stmt_close($perintah);

    return $kode;
}

/**
 * Ubah data siswa. NIS boleh berganti selama tidak tertahan kunci tamu.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @param string $nisLama NIS yang sedang diubah.
 * @param string $nis NIS baru.
 * @param string $nama Nama siswa.
 * @param string $tglLahir Tanggal Y-m-d.
 * @param string $kelamin L atau P.
 * @return string ok, duplikat, atau gagal.
 */
function ubahSiswa($koneksi, $nisLama, $nis, $nama, $tglLahir, $kelamin)
{
    $sql = 'UPDATE TBL_SISWA SET NIS = ?, NAMA = ?, TGL_LAHIR = ?, KELAMIN = ? WHERE NIS = ?';
    $perintah = mysqli_prepare($koneksi, $sql);

    if (!$perintah) {
        return 'gagal';
    }

    mysqli_stmt_bind_param($perintah, 'sssss', $nis, $nama, $tglLahir, $kelamin, $nisLama);
    $berhasil = mysqli_stmt_execute($perintah);
    $kode = 'ok';

    if (!$berhasil) {
        $nomorGalat = mysqli_stmt_errno($perintah);

        if ($nomorGalat === 1062) {
            $kode = 'duplikat';
        } elseif ($nomorGalat === 1452) {
            $kode = 'masih-dipinjam';
        } else {
            $kode = 'gagal';
        }
    }

    mysqli_stmt_close($perintah);

    return $kode;
}

/**
 * Hapus siswa yang tidak punya baris di TBL_PINJAM.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @param string $nis NIS siswa.
 * @return string ok, masih-dipinjam, tidak-ada, atau gagal.
 */
function hapusSiswa($koneksi, $nis)
{
    $jumlah = jumlahPinjamSiswa($koneksi, $nis);

    if ($jumlah === null) {
        return 'gagal';
    }

    if ($jumlah > 0) {
        return 'masih-dipinjam';
    }

    $sql = 'DELETE FROM TBL_SISWA WHERE NIS = ?';
    $perintah = mysqli_prepare($koneksi, $sql);

    if (!$perintah) {
        return 'gagal';
    }

    mysqli_stmt_bind_param($perintah, 's', $nis);
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
 * Label kelamin di layar. Di database tetap L atau P.
 *
 * @param string $kode L atau P.
 * @return string Laki-Laki, Perempuan, atau string kosong.
 */
function labelKelamin($kode)
{
    if ($kode === 'L') {
        return 'Laki-Laki';
    }

    if ($kode === 'P') {
        return 'Perempuan';
    }

    return '';
}

/**
 * Susun dan periksa isian form siswa.
 *
 * @param string $nis NIS dari form.
 * @param string $nama Nama dari form.
 * @param string $hari Hari dari dropdown.
 * @param string $bulan Bulan dari dropdown.
 * @param string $tahun Tahun dari dropdown.
 * @param string $kelamin L atau P.
 * @return array sah, pesan, nis, nama, tglLahir, kelamin, hari, bulan, tahun.
 */
function periksaFormSiswa($nis, $nama, $hari, $bulan, $tahun, $kelamin)
{
    $hasil = array(
        'sah' => false,
        'pesan' => '',
        'nis' => $nis,
        'nama' => $nama,
        'tglLahir' => '',
        'kelamin' => $kelamin,
        'hari' => (int) $hari,
        'bulan' => (int) $bulan,
        'tahun' => (int) $tahun,
    );
    $pesan = array();

    if (!preg_match('/^[0-9]{3}$/', $nis)) {
        $pesan[] = 'NIS harus 3 angka, contoh 008.';
    }

    if ($nama === '') {
        $pesan[] = 'Nama wajib diisi.';
    } elseif (strlen($nama) > 100) {
        $pesan[] = 'Nama terlalu panjang.';
    }

    $hariAngka = (int) $hari;
    $bulanAngka = (int) $bulan;
    $tahunAngka = (int) $tahun;

    if ($tahunAngka < 1970 || $tahunAngka > 2010 || !checkdate($bulanAngka, $hariAngka, $tahunAngka)) {
        $pesan[] = 'Tanggal lahir tidak sah.';
    }

    if ($kelamin !== 'L' && $kelamin !== 'P') {
        $pesan[] = 'Jenis kelamin belum dipilih.';
    }

    if (count($pesan) > 0) {
        $hasil['pesan'] = implode(' ', $pesan);
        return $hasil;
    }

    $hasil['sah'] = true;
    $hasil['tglLahir'] = sprintf('%04d-%02d-%02d', $tahunAngka, $bulanAngka, $hariAngka);

    return $hasil;
}
