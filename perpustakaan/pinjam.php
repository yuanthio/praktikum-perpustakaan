<?php

require_once dirname(__FILE__) . '/../config/koneksi.php';
require_once dirname(__FILE__) . '/../config/format_tanggal.php';
require_once dirname(__FILE__) . '/../includes/layout.php';
require_once dirname(__FILE__) . '/../app/perpustakaan/pinjam.php';
require_once dirname(__FILE__) . '/../view/perpustakaan/pinjam_list.php';
require_once dirname(__FILE__) . '/../view/perpustakaan/pinjam_editor.php';

/**
 * Baca aksi halaman dari query string. Nilai lain dianggap list.
 *
 * @return string list, add, atau edit.
 */
function bacaAksiPinjam()
{
    if (!isset($_GET['action'])) {
        return 'list';
    }

    if ($_GET['action'] === 'add' || $_GET['action'] === 'edit') {
        return $_GET['action'];
    }

    return 'list';
}

/**
 * Baca satu nilai POST. Kosong jika tidak ada.
 *
 * @param string $kunci Nama field.
 * @return string Nilai yang sudah di-trim.
 */
function bacaPostPinjam($kunci)
{
    if (!isset($_POST[$kunci])) {
        return '';
    }

    return trim($_POST[$kunci]);
}

/**
 * ID pinjam dari query atau form. Hanya pola P diikuti 3 angka.
 *
 * @param string $id Nilai mentah.
 * @return string ID yang sah, atau string kosong.
 */
function idPinjamAman($id)
{
    $id = strtoupper($id);

    if (preg_match('/^P[0-9]{3}$/', $id)) {
        return $id;
    }

    return '';
}

/**
 * Kalimat pesan dari kode operasi yang kita buat sendiri.
 *
 * @param string $kode Kode pesan.
 * @param string $id ID pinjam yang boleh ditampilkan.
 * @return array pesan dan gagal.
 */
function bacaPesanPinjam($kode, $id)
{
    $hasil = array('pesan' => '', 'gagal' => false);

    if ($kode === 'tambah') {
        $hasil['pesan'] = 'Data peminjaman ' . $id . ' berhasil disimpan.';
    } elseif ($kode === 'ubah') {
        $hasil['pesan'] = 'Data peminjaman ' . $id . ' berhasil diubah.';
    } elseif ($kode === 'hapus') {
        $hasil['pesan'] = 'Data peminjaman ' . $id . ' berhasil dihapus.';
    } elseif ($kode === 'tidak-ada') {
        $hasil['pesan'] = 'Peminjaman tidak ditemukan.';
        $hasil['gagal'] = true;
    }

    return $hasil;
}

/**
 * Form peminjaman kosong. Tanggal pinjam awal mengikuti contoh soal: 01 Januari 2010.
 *
 * @return array Nilai form editor.
 */
function formPinjamKosong()
{
    return array(
        'id' => '',
        'hariPinjam' => 1,
        'bulanPinjam' => 1,
        'tahunPinjam' => 2010,
        'hariKembali' => '',
        'bulanKembali' => '',
        'tahunKembali' => '',
        'status' => '',
        'nis' => '',
        'idBuku' => '',
        'idLama' => '',
    );
}

/**
 * Pecah DATE MySQL menjadi hari, bulan, dan tahun. Kosong jika tanggal kosong.
 *
 * @param string|null $tanggal Tanggal Y-m-d.
 * @return array hari, bulan, tahun. String kosong bila tidak ada tanggal.
 */
function pecahTanggalForm($tanggal)
{
    if ($tanggal === null || $tanggal === '') {
        return array('hari' => '', 'bulan' => '', 'tahun' => '');
    }

    $bagian = explode('-', substr($tanggal, 0, 10));

    return array(
        'hari' => (int) $bagian[2],
        'bulan' => (int) $bagian[1],
        'tahun' => (int) $bagian[0],
    );
}

/**
 * Form editor dari satu baris peminjaman.
 *
 * @param array $pinjam Baris TBL_PINJAM.
 * @return array Nilai form editor.
 */
function formDariPinjam($pinjam)
{
    $pinjamBagian = pecahTanggalForm($pinjam['TGL_PINJAM']);
    $kembaliBagian = pecahTanggalForm($pinjam['TGL_KEMBALI']);

    return array(
        'id' => $pinjam['ID'],
        'hariPinjam' => $pinjamBagian['hari'],
        'bulanPinjam' => $pinjamBagian['bulan'],
        'tahunPinjam' => $pinjamBagian['tahun'],
        'hariKembali' => $kembaliBagian['hari'],
        'bulanKembali' => $kembaliBagian['bulan'],
        'tahunKembali' => $kembaliBagian['tahun'],
        'status' => (string) $pinjam['STATUS'],
        'nis' => $pinjam['NIS'],
        'idBuku' => $pinjam['ID_BUKU'],
        'idLama' => $pinjam['ID'],
    );
}

/**
 * Alihkan browser tanpa menulis HTML.
 *
 * @param string $url URL tujuan di folder ini.
 * @return void
 */
function pergiKePinjam($url)
{
    header('Location: ' . $url);
    exit;
}

/**
 * Ambil opsi siswa dan buku untuk editor. Dipanggil sekali saat form ditampilkan.
 *
 * @param mysqli $koneksi Koneksi database perpustakaan.
 * @return array|null daftarSiswa dan daftarBuku, atau null jika salah satu gagal.
 */
function ambilOpsiEditorPinjam($koneksi)
{
    $daftarSiswa = ambilOpsiSiswa($koneksi);
    $daftarBuku = ambilOpsiBuku($koneksi);

    if ($daftarSiswa === null || $daftarBuku === null) {
        return null;
    }

    return array(
        'siswa' => $daftarSiswa,
        'buku' => $daftarBuku,
    );
}

$koneksi = bukaKoneksi();
$aksi = bacaAksiPinjam();
$pesan = '';
$gagal = false;
$form = formPinjamKosong();

if (!$koneksi) {
    mulaiHalaman('Data Peminjaman', '72rem');
    echo '<p class="pesan pesan-gagal">Koneksi database gagal. Pastikan MySQL berjalan dan database perpustakaan sudah dibuat.</p>';
    selesaiHalaman();
    exit;
}

if (isset($_POST['tombol']) && $_POST['tombol'] === 'delete') {
    $id = idPinjamAman(bacaPostPinjam('id'));

    if ($id === '') {
        pergiKePinjam('pinjam.php?pesan=tidak-ada');
    }

    $hasilHapus = hapusPinjam($koneksi, $id);

    if ($hasilHapus === 'ok') {
        pergiKePinjam('pinjam.php?pesan=hapus&id=' . $id);
    }

    if ($hasilHapus === 'tidak-ada') {
        pergiKePinjam('pinjam.php?pesan=tidak-ada');
    }

    $pesan = 'Data peminjaman gagal dihapus.';
    $gagal = true;
    $aksi = 'list';
}

if (isset($_POST['tombol']) && ($_POST['tombol'] === 'ok' || $_POST['tombol'] === 'save')) {
    $periksa = periksaFormPinjam(
        bacaPostPinjam('id'),
        bacaPostPinjam('hari_pinjam'),
        bacaPostPinjam('bulan_pinjam'),
        bacaPostPinjam('tahun_pinjam'),
        bacaPostPinjam('hari_kembali'),
        bacaPostPinjam('bulan_kembali'),
        bacaPostPinjam('tahun_kembali'),
        bacaPostPinjam('status'),
        bacaPostPinjam('nis'),
        bacaPostPinjam('id_buku')
    );
    $form = array(
        'id' => $periksa['id'],
        'hariPinjam' => $periksa['hariPinjam'],
        'bulanPinjam' => $periksa['bulanPinjam'],
        'tahunPinjam' => $periksa['tahunPinjam'],
        'hariKembali' => $periksa['hariKembali'],
        'bulanKembali' => $periksa['bulanKembali'],
        'tahunKembali' => $periksa['tahunKembali'],
        'status' => $periksa['status'],
        'nis' => $periksa['nis'],
        'idBuku' => $periksa['idBuku'],
        'idLama' => idPinjamAman(bacaPostPinjam('id_lama')),
    );

    if (!$periksa['sah']) {
        $pesan = $periksa['pesan'];
        $gagal = true;
    } elseif ($periksa['tglBatas'] === '') {
        $pesan = 'Tanggal batas tidak dapat dihitung.';
        $gagal = true;
    } elseif ($aksi === 'edit') {
        if ($form['idLama'] === '') {
            $pesan = 'Peminjaman yang diubah tidak dikenali.';
            $gagal = true;
        } else {
            $hasilUbah = ubahPinjam(
                $koneksi,
                $form['idLama'],
                $form['id'],
                $periksa['tglPinjam'],
                $periksa['tglBatas'],
                $periksa['tglKembali'],
                (int) $form['status'],
                $form['nis'],
                $form['idBuku']
            );

            if ($hasilUbah === 'ok' && $_POST['tombol'] === 'ok') {
                pergiKePinjam('pinjam.php?pesan=ubah&id=' . $form['id']);
            }

            if ($hasilUbah === 'ok') {
                pergiKePinjam('pinjam.php?action=edit&id=' . $form['id'] . '&pesan=ubah');
            }

            if ($hasilUbah === 'duplikat') {
                $pesan = 'ID ' . $form['id'] . ' sudah digunakan.';
            } elseif ($hasilUbah === 'relasi') {
                $pesan = 'Siswa atau buku yang dipilih tidak ada.';
            } else {
                $pesan = 'Data peminjaman gagal diubah.';
            }

            $gagal = true;
        }
    } else {
        $hasilSimpan = simpanPinjam(
            $koneksi,
            $form['id'],
            $periksa['tglPinjam'],
            $periksa['tglBatas'],
            $periksa['tglKembali'],
            (int) $form['status'],
            $form['nis'],
            $form['idBuku']
        );

        if ($hasilSimpan === 'ok' && $_POST['tombol'] === 'ok') {
            pergiKePinjam('pinjam.php?pesan=tambah&id=' . $form['id']);
        }

        if ($hasilSimpan === 'ok') {
            pergiKePinjam('pinjam.php?action=edit&id=' . $form['id'] . '&pesan=tambah');
        }

        if ($hasilSimpan === 'duplikat') {
            $pesan = 'ID ' . $form['id'] . ' sudah digunakan.';
        } elseif ($hasilSimpan === 'relasi') {
            $pesan = 'Siswa atau buku yang dipilih tidak ada.';
        } else {
            $pesan = 'Data peminjaman gagal disimpan.';
        }

        $gagal = true;
        $aksi = 'add';
    }
}

if ($pesan === '' && isset($_GET['pesan'])) {
    $idPesan = '';

    if (isset($_GET['id'])) {
        $idPesan = idPinjamAman($_GET['id']);
    }

    $info = bacaPesanPinjam($_GET['pesan'], $idPesan);
    $pesan = $info['pesan'];
    $gagal = $info['gagal'];
}

mulaiHalaman('Data Peminjaman', '72rem');

if ($aksi === 'add' || $aksi === 'edit') {
    $opsi = ambilOpsiEditorPinjam($koneksi);

    if ($opsi === null) {
        echo '<p class="pesan pesan-gagal">Daftar siswa atau buku gagal dibaca.</p>';
    } elseif ($aksi === 'add') {
        tulisEditorPinjam($form, $opsi['siswa'], $opsi['buku'], 'add', $pesan, $gagal);
    } elseif ($gagal) {
        tulisEditorPinjam($form, $opsi['siswa'], $opsi['buku'], 'edit', $pesan, $gagal);
    } else {
        $id = '';

        if (isset($_GET['id'])) {
            $id = idPinjamAman($_GET['id']);
        }

        $pinjam = $id === '' ? null : ambilPinjam($koneksi, $id);

        if ($pinjam === false || $pinjam === null) {
            $daftarPinjam = ambilSemuaPinjam($koneksi);
            $teks = $pinjam === false ? 'Daftar peminjaman gagal dibaca.' : 'Peminjaman tidak ditemukan.';
            tulisDaftarPinjam($daftarPinjam === null ? array() : $daftarPinjam, $teks, true);
        } else {
            tulisEditorPinjam(formDariPinjam($pinjam), $opsi['siswa'], $opsi['buku'], 'edit', $pesan, $gagal);
        }
    }
} else {
    $daftarPinjam = ambilSemuaPinjam($koneksi);

    if ($daftarPinjam === null) {
        tulisDaftarPinjam(array(), 'Daftar peminjaman gagal dibaca.', true);
    } else {
        tulisDaftarPinjam($daftarPinjam, $pesan, $gagal);
    }
}

selesaiHalaman();
