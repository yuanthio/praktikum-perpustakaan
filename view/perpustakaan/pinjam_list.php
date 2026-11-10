<?php

/**
 * Cetak tabel peminjaman beserta siswa, buku, dan tanggal batas.
 *
 * @param array $daftarPinjam Baris dari ambilSemuaPinjam().
 * @param string $pesan Pesan operasi terakhir.
 * @param bool $gagal True jika pesan berupa kegagalan.
 * @return void
 */
function tulisDaftarPinjam($daftarPinjam, $pesan, $gagal)
{
    tulisPesan($pesan, $gagal);
    echo '<table>';
    echo '<thead><tr><th>ID</th><th>PINJAM</th><th>BATAS</th><th>KEMBALI</th><th>STATUS</th><th>SISWA</th><th>BUKU</th><th></th><th></th></tr></thead>';
    echo '<tbody>';

    foreach ($daftarPinjam as $pinjam) {
        $id = teksAman($pinjam['ID']);
        echo '<tr>';
        echo '<td>' . $id . '</td>';
        echo '<td>' . teksAman(formatTanggalTampil($pinjam['TGL_PINJAM'])) . '</td>';
        echo '<td>' . teksAman(formatTanggalTampil($pinjam['TGL_BATAS'])) . '</td>';
        echo '<td>' . teksAman(formatTanggalTampil($pinjam['TGL_KEMBALI'])) . '</td>';
        echo '<td>' . teksAman(labelStatusPinjam($pinjam['STATUS'])) . '</td>';
        echo '<td>' . teksAman($pinjam['NAMA']) . '</td>';
        echo '<td>' . teksAman($pinjam['JUDUL']) . '</td>';
        echo '<td><a href="pinjam.php?action=edit&amp;id=' . $id . '"><strong>EDIT</strong></a></td>';
        echo '<td>';
        echo '<form class="berderet" method="post" action="pinjam.php">';
        echo '<input type="hidden" name="id" value="' . $id . '">';
        echo '<button class="tombol" type="submit" name="tombol" value="delete"><strong>DELETE</strong></button>';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '<p><a class="tombol" href="pinjam.php?action=add">ADD NEW</a></p>';
}
