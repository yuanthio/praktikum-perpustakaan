<?php

/**
 * Akar URL aplikasi, supaya CSS dan navbar sama dari beranda maupun modul.
 *
 * @return string Jalur seperti /Yuanthio_Virly, tanpa garis miring di akhir.
 */
function ambilAkarAplikasi()
{
    $skrip = '';

    if (isset($_SERVER['SCRIPT_NAME'])) {
        $skrip = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
    }

    $penanda = '/perpustakaan/';
    $posisi = strpos($skrip, $penanda);

    if ($posisi !== false) {
        return substr($skrip, 0, $posisi);
    }

    $folder = str_replace('\\', '/', dirname($skrip));

    if ($folder === '/' || $folder === '.' || $folder === '') {
        return '';
    }

    return rtrim($folder, '/');
}

/**
 * Kelas lebar lembar isi. Nilainya dipakai stylesheet, bukan style inline.
 *
 * @param string $lebar Lebar yang diminta halaman, misalnya 56rem.
 * @return string Nama kelas CSS.
 */
function kelasLebarHalaman($lebar)
{
    if ($lebar === '72rem') {
        return 'lembar-lebar';
    }

    if ($lebar === '56rem') {
        return 'lembar-sedang';
    }

    if ($lebar === '48rem') {
        return 'lembar-rapat';
    }

    return 'lembar-sempit';
}

/**
 * Cetak navbar modul perpustakaan.
 *
 * @return void
 */
function tulisNavigasi()
{
    $akar = ambilAkarAplikasi();
    $berkasAktif = '';

    if (isset($_SERVER['SCRIPT_NAME'])) {
        $berkasAktif = basename(str_replace('\\', '/', $_SERVER['SCRIPT_NAME']));
    }

    $menu = array(
        array('berkas' => 'index.php', 'jalur' => '/index.php', 'label' => 'Beranda'),
        array('berkas' => 'siswa.php', 'jalur' => '/perpustakaan/siswa.php', 'label' => 'Siswa'),
        array('berkas' => 'buku.php', 'jalur' => '/perpustakaan/buku.php', 'label' => 'Buku'),
        array('berkas' => 'pinjam.php', 'jalur' => '/perpustakaan/pinjam.php', 'label' => 'Peminjaman'),
        array('berkas' => 'laporan_tersedia.php', 'jalur' => '/perpustakaan/laporan_tersedia.php', 'label' => 'Buku Tersedia'),
        array('berkas' => 'laporan_belum_kembali.php', 'jalur' => '/perpustakaan/laporan_belum_kembali.php', 'label' => 'Belum Kembali'),
        array('berkas' => 'laporan_rekap.php', 'jalur' => '/perpustakaan/laporan_rekap.php', 'label' => 'Rekap'),
    );

    echo '<a class="merek" href="' . teksAman($akar . '/index.php') . '">Perpustakaan</a>';
    echo '<nav class="navigasi">';

    foreach ($menu as $item) {
        $kelas = '';

        if ($item['berkas'] === $berkasAktif) {
            $kelas = ' class="aktif"';
        }

        echo '<a href="' . teksAman($akar . $item['jalur']) . '"' . $kelas . '>' . teksAman($item['label']) . '</a>';
    }

    echo '</nav>';
}

/**
 * Cetak awal halaman HTML.
 *
 * @param string $judul Judul di tab browser dan di kepala halaman.
 * @param string $lebar Lebar lembar isi.
 * @return void
 */
function mulaiHalaman($judul, $lebar = '40rem')
{
    $judulAman = htmlspecialchars($judul, ENT_QUOTES, 'UTF-8');
    $kelasLembar = kelasLebarHalaman($lebar);
    $css = teksAman(ambilAkarAplikasi() . '/css/tampilan.css');

    echo '<!DOCTYPE html>';
    echo '<html lang="id">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . $judulAman . '</title>';
    echo '<link rel="stylesheet" href="' . $css . '">';
    echo '</head>';
    echo '<body>';
    echo '<header class="papan"><div class="papan-dalam">';
    tulisNavigasi();
    echo '</div></header>';
    echo '<main class="' . $kelasLembar . '">';
    echo '<h1>' . $judulAman . '</h1>';
}

/**
 * Amankan teks sebelum ditulis ke HTML.
 *
 * @param string $teks Teks yang akan ditampilkan.
 * @return string Teks yang aman untuk HTML.
 */
function teksAman($teks)
{
    return htmlspecialchars($teks, ENT_QUOTES, 'UTF-8');
}

/**
 * Cetak pesan sukses atau gagal.
 *
 * @param string $pesan Teks pesan. Kosong berarti tidak ditampilkan.
 * @param bool $gagal True jika pesan berupa kegagalan.
 * @return void
 */
function tulisPesan($pesan, $gagal)
{
    if ($pesan === '') {
        return;
    }

    $kelas = 'pesan';

    if ($gagal) {
        $kelas .= ' pesan-gagal';
    }

    echo '<p class="' . $kelas . '">' . teksAman($pesan) . '</p>';
}

/**
 * Tandai option dropdown yang sedang dipilih.
 *
 * @param bool $aktif True jika option ini yang aktif.
 * @return string Atribut selected, atau string kosong.
 */
function atributTerpilih($aktif)
{
    if ($aktif) {
        return ' selected';
    }

    return '';
}

/**
 * Cetak penutup halaman HTML.
 *
 * @return void
 */
function selesaiHalaman()
{
    echo '</main>';
    echo '</body>';
    echo '</html>';
}
