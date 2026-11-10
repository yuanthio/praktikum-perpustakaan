<?php

/**
 * Cetak laporan buku yang masih tersedia untuk dipinjam.
 *
 * @param array|null $daftarBuku Baris dari ambilBukuTersedia(), atau null jika query gagal.
 * @return void
 */
function tulisLaporanBukuTersedia($daftarBuku)
{
    echo '<p class="pengantar"><a href="../index.php">Menu</a></p>';
    echo '<p>Buku di bawah ini tidak sedang terpinjam, jadi masih bisa dipinjam.</p>';

    if ($daftarBuku === null) {
        tulisPesan('Laporan buku tersedia gagal dimuat.', true);
        return;
    }

    if (count($daftarBuku) === 0) {
        echo '<p class="status">Tidak ada buku yang tersedia untuk dipinjam.</p>';
        return;
    }

    echo '<table>';
    echo '<thead><tr><th>ID</th><th>JUDUL</th><th>ISBN</th><th>PENGARANG</th></tr></thead>';
    echo '<tbody>';

    foreach ($daftarBuku as $buku) {
        echo '<tr>';
        echo '<td>' . teksAman($buku['ID']) . '</td>';
        echo '<td>' . teksAman($buku['JUDUL']) . '</td>';
        echo '<td>' . teksAman($buku['ISBN']) . '</td>';
        echo '<td>' . teksAman($buku['PENGARANG']) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
}
