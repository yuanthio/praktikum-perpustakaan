<?php

require_once __DIR__ . '/config/koneksi.php';
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

mulaiHalaman('Menu Praktikum Perpustakaan');

echo '<p class="pengantar">Pilih modul yang ingin dikerjakan.</p>';

if (!$koneksi) {
    echo '<p class="status status-gagal">Koneksi database gagal. Pastikan MySQL berjalan dan database perpustakaan sudah dibuat.</p>';
}

echo '<section>';
echo '<h2>Modul</h2>';
tulisMenuUtama();
echo '</section>';

selesaiHalaman();
