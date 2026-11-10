<?php

/**
 * Cetak tabel siswa, tautan EDIT, tombol DELETE, dan ADD NEW.
 *
 * @param array $daftarSiswa Baris dari ambilSemuaSiswa().
 * @param string $pesan Pesan operasi terakhir.
 * @param bool $gagal True jika pesan berupa kegagalan.
 * @return void
 */
function tulisDaftarSiswa($daftarSiswa, $pesan, $gagal)
{
    tulisPesan($pesan, $gagal);
    echo '<table>';
    echo '<thead><tr><th>NIS</th><th>NAMA</th><th>TGL LAHIR</th><th>KELAMIN</th><th></th><th></th></tr></thead>';
    echo '<tbody>';

    foreach ($daftarSiswa as $siswa) {
        $nis = teksAman($siswa['NIS']);
        echo '<tr>';
        echo '<td>' . $nis . '</td>';
        echo '<td>' . teksAman($siswa['NAMA']) . '</td>';
        echo '<td>' . teksAman(formatTanggalTampil($siswa['TGL_LAHIR'])) . '</td>';
        echo '<td>' . teksAman(labelKelamin($siswa['KELAMIN'])) . '</td>';
        echo '<td><a href="siswa.php?action=edit&amp;id=' . $nis . '"><strong>EDIT</strong></a></td>';
        echo '<td>';
        echo '<form class="berderet" method="post" action="siswa.php">';
        echo '<input type="hidden" name="id" value="' . $nis . '">';
        echo '<button class="tombol" type="submit" name="tombol" value="delete"><strong>DELETE</strong></button>';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '<p><a class="tombol" href="siswa.php?action=add">ADD NEW</a></p>';
}
