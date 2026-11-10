<?php

require_once dirname(__FILE__) . '/../config/koneksi.php';
require_once dirname(__FILE__) . '/../config/format_tanggal.php';
require_once dirname(__FILE__) . '/../includes/layout.php';
require_once dirname(__FILE__) . '/../app/perpustakaan/siswa.php';
require_once dirname(__FILE__) . '/../view/perpustakaan/siswa_list.php';
require_once dirname(__FILE__) . '/../view/perpustakaan/siswa_editor.php';

/**
 * Baca aksi halaman dari query string. Nilai lain dianggap list.
 *
 * @return string list, add, atau edit.
 */
function bacaAksiSiswa()
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
function bacaPostSiswa($kunci)
{
    if (!isset($_POST[$kunci])) {
        return '';
    }

    return trim($_POST[$kunci]);
}

/**
 * NIS dari query atau form, hanya pola 3 angka yang diterima.
 *
 * @param string $nis Nilai mentah.
 * @return string NIS yang sah, atau string kosong.
 */
function nisAman($nis)
{
    if (preg_match('/^[0-9]{3}$/', $nis)) {
        return $nis;
    }

    return '';
}

/**
 * Kalimat pesan dari kode operasi yang kita buat sendiri.
 *
 * @param string $kode Kode pesan.
 * @param string $nis NIS yang boleh ditampilkan.
 * @return array pesan dan gagal.
 */
function bacaPesanSiswa($kode, $nis)
{
    $hasil = array('pesan' => '', 'gagal' => false);

    if ($kode === 'tambah') {
        $hasil['pesan'] = 'Data siswa ' . $nis . ' berhasil disimpan.';
    } elseif ($kode === 'ubah') {
        $hasil['pesan'] = 'Data siswa ' . $nis . ' berhasil diubah.';
    } elseif ($kode === 'hapus') {
        $hasil['pesan'] = 'Data siswa ' . $nis . ' berhasil dihapus.';
    } elseif ($kode === 'gagal-hapus') {
        $hasil['pesan'] = 'Data siswa ' . $nis . ' tidak dapat dihapus karena masih tercatat pada peminjaman buku.';
        $hasil['gagal'] = true;
    } elseif ($kode === 'tidak-ada') {
        $hasil['pesan'] = 'Siswa tidak ditemukan.';
        $hasil['gagal'] = true;
    }

    return $hasil;
}

/**
 * Form kosong. Tanggal awal mengikuti contoh soal: 01 Januari 2010.
 *
 * @return array Nilai form editor.
 */
function formSiswaKosong()
{
    return array(
        'nis' => '',
        'nama' => '',
        'hari' => 1,
        'bulan' => 1,
        'tahun' => 2010,
        'kelamin' => 'L',
        'nisLama' => '',
    );
}

/**
 * Form editor dari satu baris siswa.
 *
 * @param array $siswa Baris TBL_SISWA.
 * @return array Nilai form editor.
 */
function formDariSiswa($siswa)
{
    $bagian = explode('-', $siswa['TGL_LAHIR']);

    return array(
        'nis' => $siswa['NIS'],
        'nama' => $siswa['NAMA'],
        'hari' => (int) $bagian[2],
        'bulan' => (int) $bagian[1],
        'tahun' => (int) $bagian[0],
        'kelamin' => $siswa['KELAMIN'],
        'nisLama' => $siswa['NIS'],
    );
}

/**
 * Alihkan browser tanpa menulis HTML.
 *
 * @param string $url URL tujuan di folder ini.
 * @return void
 */
function pergiKeSiswa($url)
{
    header('Location: ' . $url);
    exit;
}

$koneksi = bukaKoneksi();
$aksi = bacaAksiSiswa();
$pesan = '';
$gagal = false;
$form = formSiswaKosong();

if (!$koneksi) {
    mulaiHalaman('Data Siswa', '56rem');
    echo '<p class="pesan pesan-gagal">Koneksi database gagal. Pastikan MySQL berjalan dan database perpustakaan sudah dibuat.</p>';
    selesaiHalaman();
    exit;
}

if (isset($_POST['tombol']) && $_POST['tombol'] === 'delete') {
    $nis = nisAman(bacaPostSiswa('id'));

    if ($nis === '') {
        pergiKeSiswa('siswa.php?pesan=tidak-ada');
    }

    $hasilHapus = hapusSiswa($koneksi, $nis);

    if ($hasilHapus === 'ok') {
        pergiKeSiswa('siswa.php?pesan=hapus&id=' . $nis);
    }

    if ($hasilHapus === 'masih-dipinjam') {
        pergiKeSiswa('siswa.php?pesan=gagal-hapus&id=' . $nis);
    }

    if ($hasilHapus === 'tidak-ada') {
        pergiKeSiswa('siswa.php?pesan=tidak-ada');
    }

    $pesan = 'Data siswa gagal dihapus.';
    $gagal = true;
    $aksi = 'list';
}

if (isset($_POST['tombol']) && ($_POST['tombol'] === 'ok' || $_POST['tombol'] === 'save')) {
    $periksa = periksaFormSiswa(
        bacaPostSiswa('nis'),
        bacaPostSiswa('nama'),
        bacaPostSiswa('hari'),
        bacaPostSiswa('bulan'),
        bacaPostSiswa('tahun'),
        bacaPostSiswa('kelamin')
    );
    $form = array(
        'nis' => $periksa['nis'],
        'nama' => $periksa['nama'],
        'hari' => $periksa['hari'],
        'bulan' => $periksa['bulan'],
        'tahun' => $periksa['tahun'],
        'kelamin' => $periksa['kelamin'],
        'nisLama' => nisAman(bacaPostSiswa('nis_lama')),
    );

    if (!$periksa['sah']) {
        $pesan = $periksa['pesan'];
        $gagal = true;
    } elseif ($aksi === 'edit') {
        if ($form['nisLama'] === '') {
            $pesan = 'Siswa yang diubah tidak dikenali.';
            $gagal = true;
        } else {
            $jumlahPinjam = jumlahPinjamSiswa($koneksi, $form['nisLama']);

            if ($jumlahPinjam === null) {
                $pesan = 'Data siswa gagal diperiksa.';
                $gagal = true;
            } elseif ($form['nis'] !== $form['nisLama'] && $jumlahPinjam > 0) {
                $pesan = 'NIS tidak dapat diubah karena siswa masih tercatat pada peminjaman buku.';
                $gagal = true;
            } else {
                $hasilUbah = ubahSiswa($koneksi, $form['nisLama'], $form['nis'], $form['nama'], $periksa['tglLahir'], $form['kelamin']);

                if ($hasilUbah === 'ok' && $_POST['tombol'] === 'ok') {
                    pergiKeSiswa('siswa.php?pesan=ubah&id=' . $form['nis']);
                }

                if ($hasilUbah === 'ok') {
                    pergiKeSiswa('siswa.php?action=edit&id=' . $form['nis'] . '&pesan=ubah');
                }

                if ($hasilUbah === 'duplikat') {
                    $pesan = 'NIS ' . $form['nis'] . ' sudah digunakan.';
                } elseif ($hasilUbah === 'masih-dipinjam') {
                    $pesan = 'NIS tidak dapat diubah karena siswa masih tercatat pada peminjaman buku.';
                } else {
                    $pesan = 'Data siswa gagal diubah.';
                }

                $gagal = true;
            }
        }
    } else {
        $hasilSimpan = simpanSiswa($koneksi, $form['nis'], $form['nama'], $periksa['tglLahir'], $form['kelamin']);

        if ($hasilSimpan === 'ok' && $_POST['tombol'] === 'ok') {
            pergiKeSiswa('siswa.php?pesan=tambah&id=' . $form['nis']);
        }

        if ($hasilSimpan === 'ok') {
            pergiKeSiswa('siswa.php?action=edit&id=' . $form['nis'] . '&pesan=tambah');
        }

        if ($hasilSimpan === 'duplikat') {
            $pesan = 'NIS ' . $form['nis'] . ' sudah digunakan.';
        } else {
            $pesan = 'Data siswa gagal disimpan.';
        }

        $gagal = true;
        $aksi = 'add';
    }
}

if ($pesan === '' && isset($_GET['pesan'])) {
    $info = bacaPesanSiswa($_GET['pesan'], nisAman(isset($_GET['id']) ? $_GET['id'] : ''));
    $pesan = $info['pesan'];
    $gagal = $info['gagal'];
}

mulaiHalaman('Data Siswa', '56rem');

if ($aksi === 'add') {
    tulisEditorSiswa($form, 'add', $pesan, $gagal);
} elseif ($aksi === 'edit') {
    if ($gagal) {
        tulisEditorSiswa($form, 'edit', $pesan, $gagal);
    } else {
        $id = '';

        if (isset($_GET['id'])) {
            $id = nisAman($_GET['id']);
        }

        $siswa = $id === '' ? null : ambilSiswa($koneksi, $id);

        if ($siswa === false) {
            $daftarSiswa = ambilSemuaSiswa($koneksi);
            tulisDaftarSiswa($daftarSiswa === null ? array() : $daftarSiswa, 'Daftar siswa gagal dibaca.', true);
        } elseif ($siswa === null) {
            $daftarSiswa = ambilSemuaSiswa($koneksi);
            tulisDaftarSiswa($daftarSiswa === null ? array() : $daftarSiswa, 'Siswa tidak ditemukan.', true);
        } else {
            tulisEditorSiswa(formDariSiswa($siswa), 'edit', $pesan, $gagal);
        }
    }
} else {
    $daftarSiswa = ambilSemuaSiswa($koneksi);

    if ($daftarSiswa === null) {
        tulisDaftarSiswa(array(), 'Daftar siswa gagal dibaca.', true);
    } else {
        tulisDaftarSiswa($daftarSiswa, $pesan, $gagal);
    }
}

selesaiHalaman();
