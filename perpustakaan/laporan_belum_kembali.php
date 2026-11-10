<?php

require_once dirname(__FILE__) . '/../config/koneksi.php';
require_once dirname(__FILE__) . '/../config/format_tanggal.php';
require_once dirname(__FILE__) . '/../includes/layout.php';
require_once dirname(__FILE__) . '/../app/perpustakaan/laporan.php';
require_once dirname(__FILE__) . '/../view/perpustakaan/laporan_belum_kembali.php';

$koneksi = bukaKoneksi();
$tanggalHitung = date('Y-m-d');

mulaiHalaman('Laporan Belum Dikembalikan', '72rem');

if (!$koneksi) {
    echo '<p class="pengantar"><a href="../index.php">Menu</a></p>';
    tulisPesan('Koneksi database gagal. Pastikan MySQL berjalan.', true);
} else {
    $daftarPinjam = ambilPinjamBelumKembali($koneksi);

    if (is_array($daftarPinjam)) {
        $daftarPinjam = isiDendaPinjam($daftarPinjam, $tanggalHitung);
    }

    tulisLaporanBelumKembali($daftarPinjam, $tanggalHitung);
}

selesaiHalaman();
