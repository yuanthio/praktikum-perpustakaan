<?php

/**
 * Cetak form editor siswa: NIS, nama, tanggal lahir, jenis kelamin, OK, Close, Save.
 *
 * @param array $form Nilai isian: nis, nama, hari, bulan, tahun, kelamin, nisLama.
 * @param string $mode add atau edit.
 * @param string $pesan Pesan operasi terakhir.
 * @param bool $gagal True jika pesan berupa kegagalan.
 * @return void
 */
function tulisEditorSiswa($form, $mode, $pesan, $gagal)
{
    $nisLama = teksAman($form['nisLama']);
    $tujuan = 'siswa.php?action=add';

    if ($mode === 'edit') {
        $tujuan = 'siswa.php?action=edit&amp;id=' . $nisLama;
    }

    echo '<p class="pengantar"><a href="siswa.php">Kembali ke daftar</a></p>';
    tulisPesan($pesan, $gagal);
    echo '<form class="editor" method="post" action="' . $tujuan . '">';
    echo '<input type="hidden" name="nis_lama" value="' . $nisLama . '">';

    echo '<div><span class="label">NIS</span> <input type="text" name="nis" maxlength="3" value="' . teksAman($form['nis']) . '"></div>';
    echo '<div><span class="label">Nama</span> <input type="text" name="nama" maxlength="100" value="' . teksAman($form['nama']) . '"></div>';
    echo '<div><span class="label">Tgl Lahir</span> ';
    echo '<select name="hari">';

    foreach (daftarHariLahir() as $hari) {
        echo '<option value="' . $hari . '"' . atributTerpilih((int) $form['hari'] === $hari) . '>' . sprintf('%02d', $hari) . '</option>';
    }

    echo '</select> <select name="bulan">';

    foreach (daftarBulanLengkap() as $nomor => $namaBulan) {
        echo '<option value="' . $nomor . '"' . atributTerpilih((int) $form['bulan'] === (int) $nomor) . '>' . teksAman($namaBulan) . '</option>';
    }

    echo '</select> <select name="tahun">';

    foreach (daftarTahunLahir() as $tahun) {
        echo '<option value="' . $tahun . '"' . atributTerpilih((int) $form['tahun'] === $tahun) . '>' . $tahun . '</option>';
    }

    echo '</select></div>';
    echo '<div><span class="label">Jenis Kelamin</span> <select name="kelamin">';
    echo '<option value="L"' . atributTerpilih($form['kelamin'] === 'L') . '>Laki-Laki</option>';
    echo '<option value="P"' . atributTerpilih($form['kelamin'] === 'P') . '>Perempuan</option>';
    echo '</select></div>';
    echo '<div>';
    echo '<button class="tombol" type="submit" name="tombol" value="ok">OK</button>';
    echo '<a class="tombol" href="siswa.php">Close</a>';
    echo '<button class="tombol" type="submit" name="tombol" value="save">Save</button>';
    echo '</div>';
    echo '</form>';
}
