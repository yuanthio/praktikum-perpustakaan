# SOAL PRAKTIKUM PROGRAMMER

**SIFAT:** Praktikum  
**NOTE:**  
* Gunakan minimal satu (1) `function` untuk masing-masing soal.
* Shortcut Document Root (`htdocs`) ada di desktop.
* Buat folder baru dengan Nama Anda di dalam folder Document Root.
* Tools MySQL (phpMyAdmin) ada di `http://localhost/xdba/`.
* Soal dikerjakan memakai PHP 4–7.

---

## Soal 1
Buatlah rancangan struktur data dan relasinya untuk kebutuhan informasi berikut:

a. Perusahaan memerlukan sistem pinjaman dan pembiayaan yang terkomputerisasi, data yang ada dapat dikelompokan menjadi:
   - Data Karyawan
   - Data Pinjaman
   - Data Transfer Dana Pinjaman
   - Data Penarikan Dana (oleh peminjam)
   - Data Pemakaian Dana (oleh peminjam)
   - Data Cicilan Pinjaman
b. Dana pinjaman diberikan melalui transfer rekening.
c. Perusahaan dapat menyetorkan dana kepada peminjam secara bertahap (Misal: pinjam Rp100.000.000 diberikan dalam 2 tahap @ Rp50.000.000), dan ini harus dapat dilaporkan oleh sistem.
d. Setiap penarikan dana pinjaman yang sudah disetorkan ke rekening peminjam, harus dapat dilaporkan oleh sistem.
e. Pemakaian dana pinjaman oleh si peminjam harus pula dapat dilaporkan oleh sistem.
f. Data cicilan peminjam untuk melunasi pinjaman, juga harus dapat dilaporkan oleh sistem.

---

## Soal 2
Buatlah tabel dengan data seperti di bawah ini menggunakan database MySQL.

### **TBL_SISWA**

| NIS | NAMA | TGL_LAHIR | KELAMIN |
| :--- | :--- | :--- | :--- |
| 001 | ARIF ABDULRAHMAN | 12/03/1987 | L |
| 002 | ASRI WELAS | 03/10/1979 | P |
| 003 | RASMI RAHMAWATI | 05/07/1985 | P |
| 004 | UDIN R. KAMAL | 27/05/1984 | L |
| 005 | ARDIYANSAH SAPUTRA | 30/01/1981 | L |
| 006 | SEPTIRINA HERMAWAN | 17/09/1983 | P |
| 007 | HERMAN BARLIN | 09/04/1980 | L |

### **TBL_BUKU**

| ID | JUDUL | ISBN | PENGARANG |
| :--- | :--- | :--- | :--- |
| B001 | IPHONE FOR DUMMIES | 1111111 | WILLEY |
| B002 | PEMROGRAMAN BAHASA JAWA | 2222222 | ROY MARTEN |
| B003 | PHP FOR DUMMIES | 3333333 | WILLEY |
| B004 | PENGOLAHAN DATABASE DENGAN ORACLE | 4444444 | HENDRA |
| B005 | INTERFACING WITH C | 5555555 | SCADA |
| B006 | CLOUD COMPUTING FUNDAMENTAL | 6666666 | WILLEY |
| B007 | PANDUAN FORENSIK FOTO DIGITAL | 7777777 | ROY MARTEN |

### **TBL_PINJAM**

| ID | TGL_PINJAM | TGL_BATAS | TGL_KEMBALI | STATUS | NIK | ID BUKU |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| P001 | 01/11/2010 | 08/11/2010 | | 0 | 006 | B007 |
| P002 | 01/11/2010 | 08/11/2010 | 05/11/2010 | 1 | 006 | B005 |
| P003 | 01/11/2010 | 08/11/2010 | | 0 | 004 | B002 |
| P004 | 06/11/2010 | 13/11/2010 | 08/11/2010 | 1 | 003 | B003 |
| P005 | 06/11/2010 | 13/11/2010 | | 0 | 007 | B006 |
| P006 | 09/11/2010 | 16/11/2010 | | 0 | 001 | B001 |
| P007 | 09/11/2010 | 16/11/2010 | | 0 | 001 | B004 |

---

## Soal 3
Buatlah program untuk editor data siswa dengan 2 sub tampilan (list, editor) seperti layout di bawah ini.

### **Tampilan List**

| NIS | NAMA | TGL LAHIR | KELAMIN | | |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 001 | ARIF ABDULRAHMAN | 12-Mar-1987 | Laki-Laki | **EDIT** | **DELETE** |
| 002 | ASRI WELAS | 03-Okt-1979 | Perempuan | **EDIT** | **DELETE** |
| 003 | RASMI RAHMAWATI | 05-Jul-1985 | Perempuan | **EDIT** | **DELETE** |
| 004 | UDIN R. KAMAL | 27-Mei-1984 | Laki-Laki | **EDIT** | **DELETE** |
| 005 | ARDIYANSAH SAPUTRA | 30-Jan-1981 | Laki-Laki | **EDIT** | **DELETE** |
| 006 | SEPTIRINA HERMAWAN | 17-Sep-1983 | Perempuan | **EDIT** | **DELETE** |
| 007 | HERMAN BARLIN | 09-Apr-1980 | Laki-Laki | **EDIT** | **DELETE** |

`[ ADD NEW ]`

### **Tampilan Editor**
* **NIS** : `[ Input Text ]`
* **Nama** : `[ Input Text ]`
* **Tgl Lahir** : `[ 01 v ]` `[ Januari v ]` `[ 2010 v ]`
* **Jenis Kelamin** : `[ Laki - laki v ]`
* Tombol: `[ OK ]` `[ Close ]` `[ Save ]`

---

## Soal 4
Buatlah program untuk editor data buku dengan 2 sub tampilan (tampilan list & editor) seperti layout di bawah ini.

### **Tampilan List**

| ID | JUDUL | ISBN | PENGARANG | | |
| :--- | :--- | :--- | :--- | :--- | :--- |
| B001 | IPHONE FOR DUMMIES | 1111111 | WILLEY | **EDIT** | **DELETE** |
| B002 | PEMROGRAMAN BAHASA JAWA | 2222222 | ROY MARTEN | **EDIT** | **DELETE** |
| B003 | PHP FOR DUMMIES | 3333333 | WILLEY | **EDIT** | **DELETE** |
| B004 | PENGOLAHAN DATABASE DENGAN ORACLE | 4444444 | HENDRA | **EDIT** | **DELETE** |
| B005 | INTERFACING WITH C | 5555555 | SCADA | **EDIT** | **DELETE** |
| B006 | CLOUD COMPUTING FUNDAMENTAL | 6666666 | WILLEY | **EDIT** | **DELETE** |
| B007 | PANDUAN FORENSIK FOTO DIGITAL | 7777777 | ROY MARTEN | **EDIT** | **DELETE** |

`[ ADD NEW ]`

### **Tampilan Editor**
* **ID** : `[ Input Text ]`
* **Judul** : `[ Input Text ]`
* **ISBN** : `[ Input Text ]`
* **Pengarang** : `[ Input Text ]`
* Tombol: `[ OK ]` `[ Close ]` `[ Save ]`

---

## Soal 5
Buatlah program untuk editor data transaksi peminjaman buku seperti contoh layout di bawah ini.

### **Tampilan List**

| ID | PINJAM | BATAS | KEMBALI | STATUS | SISWA | BUKU | | |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| P001 | 01-Nop-2010 | 08-Nop-2010 | | Terpinjam | SEPTIRINA HERMAWAN | PANDUAN FORENSIK FOTO DIGITAL | **EDIT** | **DELETE** |
| P002 | 01-Nop-2010 | 08-Nop-2010 | 05-Nop-2010 | Kembali | SEPTIRINA HERMAWAN | INTERFACING WITH C | **EDIT** | **DELETE** |
| P003 | 01-Nop-2010 | 08-Nop-2010 | | Terpinjam | UDIN R. KAMAL | PEMROGRAMAN BAHASA JAWA | **EDIT** | **DELETE** |
| P004 | 06-Nop-2010 | 13-Nop-2010 | 08-Nop-2010 | Kembali | RASMI RAHMAWATI | PHP FOR DUMMIES | **EDIT** | **DELETE** |
| P005 | 06-Nop-2010 | 13-Nop-2010 | | Terpinjam | HERMAN BARLIN | CLOUD COMPUTING FUNDAMENTAL | **EDIT** | **DELETE** |
| P006 | 09-Nop-2010 | 16-Nop-2010 | | Terpinjam | ARIF ABDULRAHMAN | IPHONE FOR DUMMIES | **EDIT** | **DELETE** |
| P007 | 09-Nop-2010 | 16-Nop-2010 | | Terpinjam | ARIF ABDULRAHMAN | PENGOLAHAN DATABASE DENGAN ORACLE | **EDIT** | **DELETE** |

`[ ADD NEW ]`

### **Tampilan Editor**
* **ID** : `[ Input Text ]`
* **Tgl Pinjam** : `[ 01 v ]` `[ Januari v ]` `[ 2010 v ]`
* **Tgl Batas Pinjam** : `[ Tampilkan Tgl Batas Pinjaman di sini ]`
* **Tgl Kembali** : `[ 03 v ]` `[ Januari v ]` `[ 2010 v ]`
* **Status** : `[ Pilih v ]` *(0 = Terpinjam, 1 = Kembali)*
* **NIS** : `[ Pilih v ]`
* **ID Buku** : `[ Pilih v ]`
* Tombol: `[ OK ]` `[ Close ]` `[ Save ]`

---

## Soal 6
Buat laporan yang menunjukkan **buku-buku yang masih tersedia untuk dipinjam**.

---

## Soal 7
Buat laporan yang menunjukkan **buku-buku yang belum dikembalikan**, peminjamnya, ID/no pinjam, batas akhir peminjaman, dan denda jika ada.

**Ketentuan Denda:**
* Denda dikenakan jika melewati tanggal batas peminjaman dan buku belum kembali.
* Denda dikenakan sebesar **Rp 5.000 per hari** terhitung dari tanggal batas peminjaman.

---

## Soal 8
Buat laporan rekap yang menunjukkan **berapa buku yang sudah dipinjam oleh masing-masing siswa**.

---

*Selamat Mengerjakan,*  
**Webcenter**