<?php

/**
 * Cetak rekap jumlah pinjaman tiap siswa.
 *
 * @param array|null $daftarRekap Baris dari ambilRekapPinjamSiswa(), atau null jika query gagal.
 * @return void
 */
function tulisLaporanRekapSiswa($daftarRekap)
{
    echo '<p class="pengantar"><a href="../index.php">Menu</a></p>';
    echo '<p>Jumlah ini menghitung setiap pinjaman, baik yang masih terpinjam maupun yang sudah kembali.</p>';

    if ($daftarRekap === null) {
        tulisPesan('Rekap pinjaman per siswa gagal dimuat.', true);
        return;
    }

    if (count($daftarRekap) === 0) {
        echo '<p class="status">Tidak ada data siswa.</p>';
        return;
    }

    echo '<table>';
    echo '<thead><tr><th>NIS</th><th>NAMA</th><th>JUMLAH</th></tr></thead>';
    echo '<tbody>';

    foreach ($daftarRekap as $siswa) {
        echo '<tr>';
        echo '<td>' . teksAman($siswa['NIS']) . '</td>';
        echo '<td>' . teksAman($siswa['NAMA']) . '</td>';
        echo '<td>' . teksAman($siswa['jumlah']) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
}
