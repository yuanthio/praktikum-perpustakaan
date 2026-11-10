<?php

require_once dirname(__FILE__) . '/../config/koneksi.php';
require_once dirname(__FILE__) . '/../includes/layout.php';
require_once dirname(__FILE__) . '/../app/perpustakaan/laporan.php';
require_once dirname(__FILE__) . '/../view/perpustakaan/laporan_tersedia.php';

$koneksi = bukaKoneksi();

mulaiHalaman('Laporan Buku Tersedia', '56rem');

if (!$koneksi) {
    echo '<p class="pengantar"><a href="../index.php">Menu</a></p>';
    tulisPesan('Koneksi database gagal. Pastikan MySQL berjalan.', true);
} else {
    $daftarBuku = ambilBukuTersedia($koneksi);
    tulisLaporanBukuTersedia($daftarBuku);
}

selesaiHalaman();
