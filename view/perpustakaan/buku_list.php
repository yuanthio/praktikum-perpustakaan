<?php

/**
 * Cetak tabel buku, tautan EDIT, tombol DELETE, dan ADD NEW.
 *
 * @param array $daftarBuku Baris dari ambilSemuaBuku().
 * @param string $pesan Pesan operasi terakhir.
 * @param bool $gagal True jika pesan berupa kegagalan.
 * @return void
 */
function tulisDaftarBuku($daftarBuku, $pesan, $gagal)
{
    echo '<p class="pengantar"><a href="../index.php">Menu</a></p>';
    tulisPesan($pesan, $gagal);
    echo '<table>';
    echo '<thead><tr><th>ID</th><th>JUDUL</th><th>ISBN</th><th>PENGARANG</th><th></th><th></th></tr></thead>';
    echo '<tbody>';

    foreach ($daftarBuku as $buku) {
        $id = teksAman($buku['ID']);
        echo '<tr>';
        echo '<td>' . $id . '</td>';
        echo '<td>' . teksAman($buku['JUDUL']) . '</td>';
        echo '<td>' . teksAman($buku['ISBN']) . '</td>';
        echo '<td>' . teksAman($buku['PENGARANG']) . '</td>';
        echo '<td><a href="buku.php?action=edit&amp;id=' . $id . '"><strong>EDIT</strong></a></td>';
        echo '<td>';
        echo '<form class="berderet" method="post" action="buku.php">';
        echo '<input type="hidden" name="id" value="' . $id . '">';
        echo '<button class="tombol" type="submit" name="tombol" value="delete"><strong>DELETE</strong></button>';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '<p><a class="tombol" href="buku.php?action=add">ADD NEW</a></p>';
}
