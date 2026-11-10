<?php

require_once dirname(__FILE__) . '/../config/koneksi.php';
require_once dirname(__FILE__) . '/../includes/layout.php';
require_once dirname(__FILE__) . '/../app/perpustakaan/laporan.php';
require_once dirname(__FILE__) . '/../view/perpustakaan/laporan_rekap.php';

$koneksi = bukaKoneksi();

mulaiHalaman('Rekap Pinjaman per Siswa', '48rem');

if (!$koneksi) {
    tulisPesan('Koneksi database gagal. Pastikan MySQL berjalan.', true);
} else {
    $daftarRekap = ambilRekapPinjamSiswa($koneksi);
    tulisLaporanRekapSiswa($daftarRekap);
}

selesaiHalaman();
