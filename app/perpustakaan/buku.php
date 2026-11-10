<?php

/**
 * Ambil seluruh buku untuk tampilan list. Satu query, kolom yang tampil saja.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @return array|null Daftar buku, atau null jika query gagal.
 */
function ambilSemuaBuku($koneksi)
{
    $sql = 'SELECT ID, JUDUL, ISBN, PENGARANG FROM TBL_BUKU ORDER BY ID';
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
 * Ambil satu buku menurut ID.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @param string $id ID buku, misalnya B001.
 * @return array|null|false Baris buku, null jika tidak ada, false jika query gagal.
 */
function ambilBuku($koneksi, $id)
{
    $sql = 'SELECT ID, JUDUL, ISBN, PENGARANG FROM TBL_BUKU WHERE ID = ?';
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
 * Hitung peminjaman satu buku. Dipakai sebelum ID diubah atau buku dihapus.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @param string $id ID buku.
 * @return int|null Jumlah pinjaman, atau null jika query gagal.
 */
function jumlahPinjamBuku($koneksi, $id)
{
    $sql = 'SELECT COUNT(ID) AS jumlah FROM TBL_PINJAM WHERE ID_BUKU = ?';
    $perintah = mysqli_prepare($koneksi, $sql);

    if (!$perintah) {
        return null;
    }

    mysqli_stmt_bind_param($perintah, 's', $id);

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
 * Simpan buku baru.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @param string $id ID buku.
 * @param string $judul Judul.
 * @param string $isbn ISBN.
 * @param string $pengarang Pengarang.
 * @return string ok, duplikat, atau gagal.
 */
function simpanBuku($koneksi, $id, $judul, $isbn, $pengarang)
{
    $sql = 'INSERT INTO TBL_BUKU (ID, JUDUL, ISBN, PENGARANG) VALUES (?, ?, ?, ?)';
    $perintah = mysqli_prepare($koneksi, $sql);

    if (!$perintah) {
        return 'gagal';
    }

    mysqli_stmt_bind_param($perintah, 'ssss', $id, $judul, $isbn, $pengarang);
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
 * Ubah data buku. ID boleh berganti selama tidak tertahan kunci tamu.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @param string $idLama ID yang sedang diubah.
 * @param string $id ID baru.
 * @param string $judul Judul.
 * @param string $isbn ISBN.
 * @param string $pengarang Pengarang.
 * @return string ok, duplikat, masih-dipinjam, atau gagal.
 */
function ubahBuku($koneksi, $idLama, $id, $judul, $isbn, $pengarang)
{
    $sql = 'UPDATE TBL_BUKU SET ID = ?, JUDUL = ?, ISBN = ?, PENGARANG = ? WHERE ID = ?';
    $perintah = mysqli_prepare($koneksi, $sql);

    if (!$perintah) {
        return 'gagal';
    }

    mysqli_stmt_bind_param($perintah, 'sssss', $id, $judul, $isbn, $pengarang, $idLama);
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
 * Hapus buku yang tidak punya baris di TBL_PINJAM.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @param string $id ID buku.
 * @return string ok, masih-dipinjam, tidak-ada, atau gagal.
 */
function hapusBuku($koneksi, $id)
{
    $jumlah = jumlahPinjamBuku($koneksi, $id);

    if ($jumlah === null) {
        return 'gagal';
    }

    if ($jumlah > 0) {
        return 'masih-dipinjam';
    }

    $sql = 'DELETE FROM TBL_BUKU WHERE ID = ?';
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
 * Susun dan periksa isian form buku.
 *
 * @param string $id ID dari form.
 * @param string $judul Judul dari form.
 * @param string $isbn ISBN dari form.
 * @param string $pengarang Pengarang dari form.
 * @return array sah, pesan, id, judul, isbn, pengarang.
 */
function periksaFormBuku($id, $judul, $isbn, $pengarang)
{
    $id = strtoupper($id);
    $hasil = array(
        'sah' => false,
        'pesan' => '',
        'id' => $id,
        'judul' => $judul,
        'isbn' => $isbn,
        'pengarang' => $pengarang,
    );
    $pesan = array();

    if (!preg_match('/^B[0-9]{3}$/', $id)) {
        $pesan[] = 'ID harus seperti B008.';
    }

    if ($judul === '') {
        $pesan[] = 'Judul wajib diisi.';
    } elseif (strlen($judul) > 150) {
        $pesan[] = 'Judul terlalu panjang.';
    }

    if ($isbn === '') {
        $pesan[] = 'ISBN wajib diisi.';
    } elseif (strlen($isbn) > 20) {
        $pesan[] = 'ISBN terlalu panjang.';
    }

    if ($pengarang === '') {
        $pesan[] = 'Pengarang wajib diisi.';
    } elseif (strlen($pengarang) > 100) {
        $pesan[] = 'Nama pengarang terlalu panjang.';
    }

    if (count($pesan) > 0) {
        $hasil['pesan'] = implode(' ', $pesan);
        return $hasil;
    }

    $hasil['sah'] = true;

    return $hasil;
}
