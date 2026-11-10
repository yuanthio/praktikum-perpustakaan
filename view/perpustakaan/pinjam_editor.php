<?php

/**
 * Cetak satu dropdown hari, dengan pilihan kosong bila diminta.
 *
 * @param string $nama Nama field.
 * @param string $idElemen ID elemen untuk skrip tanggal batas.
 * @param string|int $terpilih Nilai yang aktif.
 * @param bool $bolehKosong True jika boleh tidak diisi.
 * @return void
 */
function tulisPilihanHari($nama, $idElemen, $terpilih, $bolehKosong)
{
    echo '<select name="' . teksAman($nama) . '" id="' . teksAman($idElemen) . '">';

    if ($bolehKosong) {
        echo '<option value=""' . atributTerpilih($terpilih === '' || $terpilih === null) . '></option>';
    }

    foreach (daftarHariLahir() as $hari) {
        echo '<option value="' . $hari . '"' . atributTerpilih((string) $terpilih !== '' && (int) $terpilih === $hari) . '>' . sprintf('%02d', $hari) . '</option>';
    }

    echo '</select>';
}

/**
 * Cetak satu dropdown bulan.
 *
 * @param string $nama Nama field.
 * @param string $idElemen ID elemen.
 * @param string|int $terpilih Nomor bulan yang aktif.
 * @param bool $bolehKosong True jika boleh tidak diisi.
 * @return void
 */
function tulisPilihanBulan($nama, $idElemen, $terpilih, $bolehKosong)
{
    echo '<select name="' . teksAman($nama) . '" id="' . teksAman($idElemen) . '">';

    if ($bolehKosong) {
        echo '<option value=""' . atributTerpilih($terpilih === '' || $terpilih === null) . '></option>';
    }

    foreach (daftarBulanLengkap() as $nomor => $namaBulan) {
        echo '<option value="' . $nomor . '"' . atributTerpilih((string) $terpilih !== '' && (int) $terpilih === (int) $nomor) . '>' . teksAman($namaBulan) . '</option>';
    }

    echo '</select>';
}

/**
 * Cetak satu dropdown tahun pinjam.
 *
 * @param string $nama Nama field.
 * @param string $idElemen ID elemen.
 * @param string|int $terpilih Tahun yang aktif.
 * @param bool $bolehKosong True jika boleh tidak diisi.
 * @return void
 */
function tulisPilihanTahunPinjam($nama, $idElemen, $terpilih, $bolehKosong)
{
    echo '<select name="' . teksAman($nama) . '" id="' . teksAman($idElemen) . '">';

    if ($bolehKosong) {
        echo '<option value=""' . atributTerpilih($terpilih === '' || $terpilih === null) . '></option>';
    }

    foreach (daftarTahunPinjam() as $tahun) {
        echo '<option value="' . $tahun . '"' . atributTerpilih((string) $terpilih !== '' && (int) $terpilih === $tahun) . '>' . $tahun . '</option>';
    }

    echo '</select>';
}

/**
 * Cetak form editor peminjaman.
 *
 * @param array $form Nilai isian editor.
 * @param array $daftarSiswa Opsi NIS.
 * @param array $daftarBuku Opsi buku.
 * @param string $mode add atau edit.
 * @param string $pesan Pesan operasi terakhir.
 * @param bool $gagal True jika pesan berupa kegagalan.
 * @return void
 */
function tulisEditorPinjam($form, $daftarSiswa, $daftarBuku, $mode, $pesan, $gagal)
{
    $idLama = teksAman($form['idLama']);
    $tujuan = 'pinjam.php?action=add';
    $pinjam = susunTanggalBagian($form['hariPinjam'], $form['bulanPinjam'], $form['tahunPinjam']);
    $batas = '';

    if ($pinjam['sah'] && !$pinjam['kosong']) {
        $batas = formatTanggalTampil(hitungTanggalBatas($pinjam['tanggal']));
    }

    if ($mode === 'edit') {
        $tujuan = 'pinjam.php?action=edit&amp;id=' . $idLama;
    }

    echo '<p class="pengantar"><a href="pinjam.php">Kembali ke daftar</a></p>';
    tulisPesan($pesan, $gagal);
    echo '<form class="editor" method="post" action="' . $tujuan . '">';
    echo '<input type="hidden" name="id_lama" value="' . $idLama . '">';
    echo '<div><span class="label">ID</span> <input type="text" name="id" maxlength="4" value="' . teksAman($form['id']) . '"></div>';
    echo '<div><span class="label">Tgl Pinjam</span> ';
    tulisPilihanHari('hari_pinjam', 'hari_pinjam', $form['hariPinjam'], false);
    echo ' ';
    tulisPilihanBulan('bulan_pinjam', 'bulan_pinjam', $form['bulanPinjam'], false);
    echo ' ';
    tulisPilihanTahunPinjam('tahun_pinjam', 'tahun_pinjam', $form['tahunPinjam'], false);
    echo '</div>';
    echo '<div><span class="label">Tgl Batas Pinjam</span> <strong id="tgl_batas">' . teksAman($batas) . '</strong></div>';
    echo '<div><span class="label">Tgl Kembali</span> ';
    tulisPilihanHari('hari_kembali', 'hari_kembali', $form['hariKembali'], true);
    echo ' ';
    tulisPilihanBulan('bulan_kembali', 'bulan_kembali', $form['bulanKembali'], true);
    echo ' ';
    tulisPilihanTahunPinjam('tahun_kembali', 'tahun_kembali', $form['tahunKembali'], true);
    echo '</div>';
    echo '<div><span class="label">Status</span> <select name="status">';
    echo '<option value=""' . atributTerpilih($form['status'] === '') . '>Pilih</option>';
    echo '<option value="0"' . atributTerpilih((string) $form['status'] === '0') . '>Terpinjam</option>';
    echo '<option value="1"' . atributTerpilih((string) $form['status'] === '1') . '>Kembali</option>';
    echo '</select></div>';
    echo '<div><span class="label">NIS</span> <select name="nis">';
    echo '<option value=""' . atributTerpilih($form['nis'] === '') . '>Pilih</option>';

    foreach ($daftarSiswa as $siswa) {
        $nis = $siswa['NIS'];
        echo '<option value="' . teksAman($nis) . '"' . atributTerpilih($form['nis'] === $nis) . '>' . teksAman($nis . ' - ' . $siswa['NAMA']) . '</option>';
    }

    echo '</select></div>';
    echo '<div><span class="label">ID Buku</span> <select name="id_buku">';
    echo '<option value=""' . atributTerpilih($form['idBuku'] === '') . '>Pilih</option>';

    foreach ($daftarBuku as $buku) {
        $idBuku = $buku['ID'];
        echo '<option value="' . teksAman($idBuku) . '"' . atributTerpilih($form['idBuku'] === $idBuku) . '>' . teksAman($idBuku . ' - ' . $buku['JUDUL']) . '</option>';
    }

    echo '</select></div>';
    echo '<div>';
    echo '<button class="tombol" type="submit" name="tombol" value="ok">OK</button>';
    echo '<a class="tombol" href="pinjam.php">Close</a>';
    echo '<button class="tombol" type="submit" name="tombol" value="save">Save</button>';
    echo '</div>';
    echo '</form>';
    echo '<script>';
    echo 'function segarkanBatasPinjam(){';
    echo 'var nama=["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nop","Des"];';
    echo 'var hari=parseInt(document.getElementById("hari_pinjam").value,10);';
    echo 'var bulan=parseInt(document.getElementById("bulan_pinjam").value,10);';
    echo 'var tahun=parseInt(document.getElementById("tahun_pinjam").value,10);';
    echo 'var tanggal=new Date(tahun,bulan-1,hari);';
    echo 'if(tanggal.getFullYear()!==tahun||tanggal.getMonth()!==bulan-1||tanggal.getDate()!==hari){';
    echo 'document.getElementById("tgl_batas").textContent="";return;}';
    echo 'tanggal.setDate(tanggal.getDate()+7);';
    echo 'var nomor=tanggal.getDate();';
    echo 'var teks=(nomor<10?"0":"")+nomor+"-"+nama[tanggal.getMonth()]+"-"+tanggal.getFullYear();';
    echo 'document.getElementById("tgl_batas").textContent=teks;}';
    echo 'document.getElementById("hari_pinjam").onchange=segarkanBatasPinjam;';
    echo 'document.getElementById("bulan_pinjam").onchange=segarkanBatasPinjam;';
    echo 'document.getElementById("tahun_pinjam").onchange=segarkanBatasPinjam;';
    echo '</script>';
}
