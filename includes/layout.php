<?php

/**
 * Cetak awal halaman HTML.
 *
 * @param string $judul Judul di tab browser dan di kepala halaman.
 * @return void
 */
function mulaiHalaman($judul, $lebar = '40rem')
{
    $judulAman = htmlspecialchars($judul, ENT_QUOTES, 'UTF-8');
    $lebarAman = htmlspecialchars($lebar, ENT_QUOTES, 'UTF-8');

    echo '<!DOCTYPE html>';
    echo '<html lang="id">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . $judulAman . '</title>';
    echo '<style>';
    echo 'body{font-family:Georgia,serif;margin:2rem auto;max-width:' . $lebarAman . ';line-height:1.5;color:#1c1917;}';
    echo 'a.tombol,button.tombol{display:inline-block;margin:0 0.25rem 0 0;padding:0.15rem 0.5rem;border:1px solid #44403c;background:#fff;color:inherit;font:inherit;text-decoration:none;cursor:pointer;}';
    echo 'form.berderet{display:inline;}';
    echo 'form.editor div{margin:0.45rem 0;}';
    echo 'form.editor .label{display:inline-block;width:8.5rem;}';
    echo '.pesan{padding:0.6rem 0.8rem;border:1px solid #d6d3d1;margin:0.6rem 0;}';
    echo '.pesan-gagal{border-color:#b91c1c;}';
    echo 'h1{font-size:1.6rem;margin-bottom:0.25rem;}';
    echo 'p.pengantar{margin-top:0;}';
    echo 'section{margin-top:1.5rem;}';
    echo 'h2{font-size:1.1rem;margin-bottom:0.4rem;}';
    echo 'ul{padding-left:1.2rem;}';
    echo 'li{margin:0.35rem 0;}';
    echo '.status{padding:0.6rem 0.8rem;border:1px solid #d6d3d1;}';
    echo '.status-gagal{border-color:#b91c1c;}';
    echo 'code{font-family:Consolas,monospace;}';
    echo 'table{border-collapse:collapse;width:100%;margin:0.5rem 0;}';
    echo 'th,td{border:1px solid #d6d3d1;padding:0.35rem 0.5rem;text-align:left;vertical-align:top;}';
    echo 'th{background:#f5f5f4;}';
    echo 'h3{font-size:1rem;margin-bottom:0.3rem;}';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    echo '<header><h1>' . $judulAman . '</h1></header>';
    echo '<main>';
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
