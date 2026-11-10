<?php

require_once dirname(__FILE__) . '/../config/koneksi.php';
require_once dirname(__FILE__) . '/../includes/layout.php';
require_once dirname(__FILE__) . '/../app/perpustakaan/buku.php';
require_once dirname(__FILE__) . '/../view/perpustakaan/buku_list.php';
require_once dirname(__FILE__) . '/../view/perpustakaan/buku_editor.php';

/**
 * Baca aksi halaman dari query string. Nilai lain dianggap list.
 *
 * @return string list, add, atau edit.
 */
function bacaAksiBuku()
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
function bacaPostBuku($kunci)
{
    if (!isset($_POST[$kunci])) {
        return '';
    }

    return trim($_POST[$kunci]);
}

/**
 * ID buku dari query atau form. Hanya pola B diikuti 3 angka.
 *
 * @param string $id Nilai mentah.
 * @return string ID yang sah, atau string kosong.
 */
function idBukuAman($id)
{
    $id = strtoupper($id);

    if (preg_match('/^B[0-9]{3}$/', $id)) {
        return $id;
    }

    return '';
}

/**
 * Kalimat pesan dari kode operasi yang kita buat sendiri.
 *
 * @param string $kode Kode pesan.
 * @param string $id ID buku yang boleh ditampilkan.
 * @return array pesan dan gagal.
 */
function bacaPesanBuku($kode, $id)
{
    $hasil = array('pesan' => '', 'gagal' => false);

    if ($kode === 'tambah') {
        $hasil['pesan'] = 'Data buku ' . $id . ' berhasil disimpan.';
    } elseif ($kode === 'ubah') {
        $hasil['pesan'] = 'Data buku ' . $id . ' berhasil diubah.';
    } elseif ($kode === 'hapus') {
        $hasil['pesan'] = 'Data buku ' . $id . ' berhasil dihapus.';
    } elseif ($kode === 'gagal-hapus') {
        $hasil['pesan'] = 'Data buku ' . $id . ' tidak dapat dihapus karena masih tercatat pada peminjaman.';
        $hasil['gagal'] = true;
    } elseif ($kode === 'tidak-ada') {
        $hasil['pesan'] = 'Buku tidak ditemukan.';
        $hasil['gagal'] = true;
    }

    return $hasil;
}

/**
 * Form buku kosong.
 *
 * @return array Nilai form editor.
 */
function formBukuKosong()
{
    return array(
        'id' => '',
        'judul' => '',
        'isbn' => '',
        'pengarang' => '',
        'idLama' => '',
    );
}

/**
 * Form editor dari satu baris buku.
 *
 * @param array $buku Baris TBL_BUKU.
 * @return array Nilai form editor.
 */
function formDariBuku($buku)
{
    return array(
        'id' => $buku['ID'],
        'judul' => $buku['JUDUL'],
        'isbn' => $buku['ISBN'],
        'pengarang' => $buku['PENGARANG'],
        'idLama' => $buku['ID'],
    );
}

/**
 * Alihkan browser tanpa menulis HTML.
 *
 * @param string $url URL tujuan di folder ini.
 * @return void
 */
function pergiKeBuku($url)
{
    header('Location: ' . $url);
    exit;
}

$koneksi = bukaKoneksi();
$aksi = bacaAksiBuku();
$pesan = '';
$gagal = false;
$form = formBukuKosong();

if (!$koneksi) {
    mulaiHalaman('Data Buku', '56rem');
    echo '<p class="pesan pesan-gagal">Koneksi database gagal. Pastikan MySQL berjalan dan database perpustakaan sudah dibuat.</p>';
    selesaiHalaman();
    exit;
}

if (isset($_POST['tombol']) && $_POST['tombol'] === 'delete') {
    $id = idBukuAman(bacaPostBuku('id'));

    if ($id === '') {
        pergiKeBuku('buku.php?pesan=tidak-ada');
    }

    $hasilHapus = hapusBuku($koneksi, $id);

    if ($hasilHapus === 'ok') {
        pergiKeBuku('buku.php?pesan=hapus&id=' . $id);
    }

    if ($hasilHapus === 'masih-dipinjam') {
        pergiKeBuku('buku.php?pesan=gagal-hapus&id=' . $id);
    }

    if ($hasilHapus === 'tidak-ada') {
        pergiKeBuku('buku.php?pesan=tidak-ada');
    }

    $pesan = 'Data buku gagal dihapus.';
    $gagal = true;
    $aksi = 'list';
}

if (isset($_POST['tombol']) && ($_POST['tombol'] === 'ok' || $_POST['tombol'] === 'save')) {
    $periksa = periksaFormBuku(
        bacaPostBuku('id'),
        bacaPostBuku('judul'),
        bacaPostBuku('isbn'),
        bacaPostBuku('pengarang')
    );
    $form = array(
        'id' => $periksa['id'],
        'judul' => $periksa['judul'],
        'isbn' => $periksa['isbn'],
        'pengarang' => $periksa['pengarang'],
        'idLama' => idBukuAman(bacaPostBuku('id_lama')),
    );

    if (!$periksa['sah']) {
        $pesan = $periksa['pesan'];
        $gagal = true;
    } elseif ($aksi === 'edit') {
        if ($form['idLama'] === '') {
            $pesan = 'Buku yang diubah tidak dikenali.';
            $gagal = true;
        } else {
            $jumlahPinjam = jumlahPinjamBuku($koneksi, $form['idLama']);

            if ($jumlahPinjam === null) {
                $pesan = 'Data buku gagal diperiksa.';
                $gagal = true;
            } elseif ($form['id'] !== $form['idLama'] && $jumlahPinjam > 0) {
                $pesan = 'ID tidak dapat diubah karena buku masih tercatat pada peminjaman.';
                $gagal = true;
            } else {
                $hasilUbah = ubahBuku(
                    $koneksi,
                    $form['idLama'],
                    $form['id'],
                    $form['judul'],
                    $form['isbn'],
                    $form['pengarang']
                );

                if ($hasilUbah === 'ok' && $_POST['tombol'] === 'ok') {
                    pergiKeBuku('buku.php?pesan=ubah&id=' . $form['id']);
                }

                if ($hasilUbah === 'ok') {
                    pergiKeBuku('buku.php?action=edit&id=' . $form['id'] . '&pesan=ubah');
                }

                if ($hasilUbah === 'duplikat') {
                    $pesan = 'ID ' . $form['id'] . ' sudah digunakan.';
                } elseif ($hasilUbah === 'masih-dipinjam') {
                    $pesan = 'ID tidak dapat diubah karena buku masih tercatat pada peminjaman.';
                } else {
                    $pesan = 'Data buku gagal diubah.';
                }

                $gagal = true;
            }
        }
    } else {
        $hasilSimpan = simpanBuku($koneksi, $form['id'], $form['judul'], $form['isbn'], $form['pengarang']);

        if ($hasilSimpan === 'ok' && $_POST['tombol'] === 'ok') {
            pergiKeBuku('buku.php?pesan=tambah&id=' . $form['id']);
        }

        if ($hasilSimpan === 'ok') {
            pergiKeBuku('buku.php?action=edit&id=' . $form['id'] . '&pesan=tambah');
        }

        if ($hasilSimpan === 'duplikat') {
            $pesan = 'ID ' . $form['id'] . ' sudah digunakan.';
        } else {
            $pesan = 'Data buku gagal disimpan.';
        }

        $gagal = true;
        $aksi = 'add';
    }
}

if ($pesan === '' && isset($_GET['pesan'])) {
    $idPesan = '';

    if (isset($_GET['id'])) {
        $idPesan = idBukuAman($_GET['id']);
    }

    $info = bacaPesanBuku($_GET['pesan'], $idPesan);
    $pesan = $info['pesan'];
    $gagal = $info['gagal'];
}

mulaiHalaman('Data Buku', '56rem');

if ($aksi === 'add') {
    tulisEditorBuku($form, 'add', $pesan, $gagal);
} elseif ($aksi === 'edit') {
    if ($gagal) {
        tulisEditorBuku($form, 'edit', $pesan, $gagal);
    } else {
        $id = '';

        if (isset($_GET['id'])) {
            $id = idBukuAman($_GET['id']);
        }

        $buku = $id === '' ? null : ambilBuku($koneksi, $id);

        if ($buku === false) {
            $daftarBuku = ambilSemuaBuku($koneksi);
            tulisDaftarBuku($daftarBuku === null ? array() : $daftarBuku, 'Daftar buku gagal dibaca.', true);
        } elseif ($buku === null) {
            $daftarBuku = ambilSemuaBuku($koneksi);
            tulisDaftarBuku($daftarBuku === null ? array() : $daftarBuku, 'Buku tidak ditemukan.', true);
        } else {
            tulisEditorBuku(formDariBuku($buku), 'edit', $pesan, $gagal);
        }
    }
} else {
    $daftarBuku = ambilSemuaBuku($koneksi);

    if ($daftarBuku === null) {
        tulisDaftarBuku(array(), 'Daftar buku gagal dibaca.', true);
    } else {
        tulisDaftarBuku($daftarBuku, $pesan, $gagal);
    }
}

selesaiHalaman();
