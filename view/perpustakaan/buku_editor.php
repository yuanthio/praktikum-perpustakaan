<?php

/**
 * Cetak form editor buku: ID, judul, ISBN, pengarang, OK, Close, Save.
 *
 * @param array $form Nilai isian: id, judul, isbn, pengarang, idLama.
 * @param string $mode add atau edit.
 * @param string $pesan Pesan operasi terakhir.
 * @param bool $gagal True jika pesan berupa kegagalan.
 * @return void
 */
function tulisEditorBuku($form, $mode, $pesan, $gagal)
{
    $idLama = teksAman($form['idLama']);
    $tujuan = 'buku.php?action=add';

    if ($mode === 'edit') {
        $tujuan = 'buku.php?action=edit&amp;id=' . $idLama;
    }

    echo '<p class="pengantar"><a href="buku.php">Kembali ke daftar</a></p>';
    tulisPesan($pesan, $gagal);
    echo '<form class="editor" method="post" action="' . $tujuan . '">';
    echo '<input type="hidden" name="id_lama" value="' . $idLama . '">';
    echo '<div><span class="label">ID</span> <input type="text" name="id" maxlength="4" value="' . teksAman($form['id']) . '"></div>';
    echo '<div><span class="label">Judul</span> <input type="text" name="judul" maxlength="150" value="' . teksAman($form['judul']) . '"></div>';
    echo '<div><span class="label">ISBN</span> <input type="text" name="isbn" maxlength="20" value="' . teksAman($form['isbn']) . '"></div>';
    echo '<div><span class="label">Pengarang</span> <input type="text" name="pengarang" maxlength="100" value="' . teksAman($form['pengarang']) . '"></div>';
    echo '<div>';
    echo '<button class="tombol" type="submit" name="tombol" value="ok">OK</button>';
    echo '<a class="tombol" href="buku.php">Close</a>';
    echo '<button class="tombol" type="submit" name="tombol" value="save">Save</button>';
    echo '</div>';
    echo '</form>';
}
