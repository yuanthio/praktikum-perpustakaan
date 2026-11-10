<?php

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/layout.php';

/**
 * Cetak pengantar singkat di beranda.
 *
 * @return void
 */
function tulisPengantarBeranda()
{
    echo '<p>Catatan siswa, koleksi buku, dan peminjaman. Pilih bagian dari menu di atas.</p>';
}

$koneksi = bukaKoneksi();

mulaiHalaman('Perpustakaan', '48rem');
tulisPengantarBeranda();

if (!$koneksi) {
    echo '<p class="status status-gagal">Koneksi database gagal. Pastikan MySQL berjalan dan database perpustakaan sudah dibuat.</p>';
}

selesaiHalaman();
