<?php

/**
 * Ubah nominal rupiah menjadi teks layar, misalnya 10000 menjadi Rp10.000.
 *
 * @param int $nominal Nominal dalam rupiah.
 * @return string Nominal dengan pemisah ribuan.
 */
function formatRupiah($nominal)
{
    return 'Rp' . number_format((int) $nominal, 0, ',', '.');
}

/**
 * Cetak laporan buku yang belum dikembalikan beserta dendanya.
 *
 * @param array|null $daftarPinjam Baris yang sudah berisi denda, atau null jika query gagal.
 * @param string $tanggalHitung Tanggal acuan Y-m-d.
 * @return void
 */
function tulisLaporanBelumKembali($daftarPinjam, $tanggalHitung)
{
    $tanggalLayar = formatTanggalTampil($tanggalHitung);

    echo '<p class="pengantar"><a href="../index.php">Menu</a></p>';
    echo '<p>Buku berikut masih berstatus terpinjam. Denda dihitung pada <strong>'
        . teksAman($tanggalLayar)
        . '</strong>, sebesar '
        . teksAman(formatRupiah(TARIF_DENDA_PER_HARI))
        . ' per hari setelah tanggal batas. Belum lewat batas, denda Rp0.</p>';

    if ($daftarPinjam === null) {
        tulisPesan('Laporan belum dikembalikan gagal dimuat.', true);
        return;
    }

    if (count($daftarPinjam) === 0) {
        echo '<p class="status">Tidak ada buku yang belum dikembalikan.</p>';
        return;
    }

    echo '<table>';
    echo '<thead><tr><th>ID PINJAM</th><th>BUKU</th><th>PEMINJAM</th><th>BATAS</th><th>DENDA</th></tr></thead>';
    echo '<tbody>';

    foreach ($daftarPinjam as $pinjam) {
        echo '<tr>';
        echo '<td>' . teksAman($pinjam['ID']) . '</td>';
        echo '<td>' . teksAman($pinjam['JUDUL']) . '</td>';
        echo '<td>' . teksAman($pinjam['NAMA']) . '</td>';
        echo '<td>' . teksAman(formatTanggalTampil($pinjam['TGL_BATAS'])) . '</td>';
        echo '<td>' . teksAman(formatRupiah($pinjam['denda'])) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
}
