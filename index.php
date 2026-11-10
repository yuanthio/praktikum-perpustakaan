<?php

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/format_tanggal.php';
require_once __DIR__ . '/includes/layout.php';

/**
 * Cetak daftar tautan modul praktikum.
 *
 * @return void
 */
function tulisMenuUtama()
{
    $menu = array(
        array('href' => 'perpustakaan/siswa.php', 'label' => 'Data Siswa'),
        array('href' => 'perpustakaan/buku.php', 'label' => 'Data Buku'),
        array('href' => 'perpustakaan/pinjam.php', 'label' => 'Transaksi Peminjaman'),
        array('href' => 'perpustakaan/laporan_tersedia.php', 'label' => 'Laporan Buku Tersedia'),
        array('href' => 'perpustakaan/laporan_belum_kembali.php', 'label' => 'Laporan Belum Dikembalikan'),
        array('href' => 'perpustakaan/laporan_rekap.php', 'label' => 'Rekap Pinjaman per Siswa'),
    );

    echo '<ul>';

    foreach ($menu as $item) {
        $href = htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8');
        $label = htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8');
        echo '<li><a href="' . $href . '">' . $label . '</a></li>';
    }

    echo '</ul>';
}

$koneksi = bukaKoneksi();
$contohLahir = formatTanggalTampil('1987-03-12');
$contohPinjam = formatTanggalTampil('2010-11-01');
$contohKosong = formatTanggalTampil('');

mulaiHalaman('Menu Praktikum Perpustakaan');

echo '<p class="pengantar">Pilih modul yang ingin dikerjakan.</p>';

echo '<section>';
echo '<h2>Koneksi database</h2>';

if ($koneksi) {
    $namaDatabase = htmlspecialchars(ambilNamaDatabase($koneksi), ENT_QUOTES, 'UTF-8');
    echo '<p class="status">Terhubung ke database <strong>' . $namaDatabase . '</strong>.</p>';
} else {
    echo '<p class="status status-gagal">Koneksi database gagal. Pastikan MySQL berjalan dan database perpustakaan sudah dibuat.</p>';
}

echo '</section>';

echo '<section>';
echo '<h2>Format tanggal di layar</h2>';
echo '<p>Di database tanggal disimpan sebagai <code>DATE</code> (<code>1987-03-12</code>). Di layar ditulis hari-bulan singkat-tahun.</p>';
echo '<ul>';
echo '<li><code>1987-03-12</code> menjadi <strong>' . htmlspecialchars($contohLahir, ENT_QUOTES, 'UTF-8') . '</strong></li>';
echo '<li><code>2010-11-01</code> menjadi <strong>' . htmlspecialchars($contohPinjam, ENT_QUOTES, 'UTF-8') . '</strong></li>';
echo '<li>Tanggal kosong tetap <strong>' . ($contohKosong === '' ? '(kosong)' : htmlspecialchars($contohKosong, ENT_QUOTES, 'UTF-8')) . '</strong></li>';
echo '</ul>';
echo '</section>';

echo '<section>';
echo '<h2>Modul</h2>';
tulisMenuUtama();
echo '</section>';

selesaiHalaman();
