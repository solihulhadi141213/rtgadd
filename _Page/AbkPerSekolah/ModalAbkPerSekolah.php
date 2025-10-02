<div class="modal fade" id="ModalFilter" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesFilter">
                <input type="hidden" name="page" id="page" value="1">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-funnel"></i> Filter Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="batas">
                                <small>Limit/Batas</small>
                            </label>
                        </div>
                        <div class="col-8">
                            <select name="batas" id="batas" class="form-control">
                                <option value="5">5</option>
                                <option selected value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="250">250</option>
                                <option value="500">500</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="OrderBy">
                                <small>Dasar Urutan</small>
                            </label>
                        </div>
                        <div class="col-8">
                            <select name="OrderBy" id="OrderBy" class="form-control">
                                <option value="">Pilih</option>
                                <option value="province_code">Kode Provinsi</option>
                                <option value="province_name">Nama Provinsi</option>
                                <option value="district_code">Kode Kab/Kota</option>
                                <option value="district_name">Nama Kab/Kota</option>
                                <option value="npsn">Kode Sekolah</option>
                                <option value="school_name">Nama Sekolah</option>
                                <option value="position_code">Kode Jabatan</option>
                                <option value="position_name">Nama Jabatan</option>
                                <option value="abk">ABK</option>
                                <option value="asn">ASN</option>
                                <option value="PPPK2024">PPPK 2024</option>
                                <option value="NonASN_sblmOkt2022">Non ASN Sebelum Oktober 2022</option>
                                <option value="NonASN_stlhOkt2022">Non ASN Setelah Oktober 2022</option>
                                <option value="JmlGuru">Jumlah Guru</option>
                                <option value="KurangGuru">Kurang Guru</option>
                                <option value="JmlASN">Jumlah ASN</option>
                                <option value="KrngASN">Kurang ASN</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="ShortBy">
                                <small>Tipe Urutan</small>
                            </label>
                        </div>
                        <div class="col-8">
                            <select name="ShortBy" id="ShortBy" class="form-control">
                                <option value="ASC">A To Z</option>
                                <option selected value="DESC">Z To A</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="KeywordBy">
                                <small>Dasar Pencarian</small>
                            </label>
                        </div>
                        <div class="col-8">
                            <select name="keyword_by" id="KeywordBy" class="form-control">
                                <option value="">Pilih</option>
                                <option value="province_code">Kode Provinsi</option>
                                <option value="province_name">Nama Provinsi</option>
                                <option value="district_code">Kode Kab/Kota</option>
                                <option value="district_name">Nama Kab/Kota</option>
                                <option value="npsn">Kode Sekolah</option>
                                <option value="school_name">Nama Sekolah</option>
                                <option value="position_code">Kode Jabatan</option>
                                <option value="position_name">Nama Jabatan</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="keyword">
                                <small>Kata Kunci</small>
                            </label>
                        </div>
                        <div class="col-8" id="FormFilter">
                            <input type="text" name="keyword" id="keyword" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-rounded">
                        <i class="bi bi-save"></i> Filter
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="ModalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesTambah" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title text-dak"><i class="bi bi-plus"></i> Tambah AKB Per Sekolah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="province_code"><small>Provinsi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i> </small></label>
                        </div>
                        <div class="col-md-8">
                            <select name="province_code" id="province_code" class="form-control" required>
                                <option value="">Pilih</option>
                                <?php
                                    //Menampilkan list provinsi
                                    $query = mysqli_query($Conn, "SELECT province_code, province_name FROM region WHERE category='Province' ORDER BY province_name ASC");
                                    while ($data = mysqli_fetch_array($query)) {
                                        $province_code      = $data['province_code'];
                                        $province_name      = $data['province_name'];
                                        echo '<option value="'.$province_code.'">'.$province_name.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="district_code"><small>Kab/Kota <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i> </small></small></label>
                        </div>
                        <div class="col-md-8">
                            <select name="district_code" id="district_code" class="form-control" required>
                                <option value="">Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="npsn"><small>Sekolah <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i> </small></small></label>
                        </div>
                        <div class="col-md-8">
                            <select name="npsn" id="npsn" class="form-control" required>
                                <option value="">Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="position_code"><small>Jabatan <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i> </small></small></label>
                        </div>
                        <div class="col-md-8">
                            <select name="position_code" id="position_code" class="form-control" required>
                                <option value="">Pilih</option>
                                <?php
                                    //Menampilkan list jabatan (position)
                                    $query = mysqli_query($Conn, "SELECT position_code, position_name FROM position ORDER BY position_name ASC");
                                    while ($data = mysqli_fetch_array($query)) {
                                        $position_code      = $data['position_code'];
                                        $position_name      = $data['position_name'];
                                        echo '<option value="'.$position_code.'">'.$position_name.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="abk"><small>ABK</small></label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="abk" id="abk" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="asn"><small>ASN</small></label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="asn" id="asn" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="PPPK2024"><small>PPPK 2024</small></label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="PPPK2024" id="PPPK2024" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="NonASN_sblmOkt2022"><small>Non ASN Sebelum Oktober 2022</small></label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="NonASN_sblmOkt2022" id="NonASN_sblmOkt2022" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="NonASN_stlhOkt2022"><small>Non ASN Setelah Oktober 2022</small></label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="NonASN_stlhOkt2022" id="NonASN_stlhOkt2022" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="JmlGuru"><small>Jumlah Guru</small></label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="JmlGuru" id="JmlGuru" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="KurangGuru"><small>Kurang Guru</small></label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="KurangGuru" id="KurangGuru" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="JmlASN"><small>Jumlah ASN</small></label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="JmlASN" id="JmlASN" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="KrngASN"><small>Kurang ASN</small></label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="KrngASN" id="KrngASN" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12" id="NotifikasiTambah">
                            <!-- Notifikasi Tambah Sekolah -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-rounded">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark">
                    <i class="bi bi-info-circle"></i> Detail Instansi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="FormDetail">
                        <!-- Form Detail Wilayah -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesEdit">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-pencil"></i> Edit Instansi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12" id="FormEdit">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" id="NotifikasiEdit">
                            <!-- Notifikasi Edit Siswa Akan Muncul Disini -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" disabled class="btn btn-success btn-rounded" id="ButtonEdit">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalHapus" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesHapus">
                <div class="modal-header">
                    <h5 class="modal-title text-dark">
                        <i class="bi bi-trash"></i> Hapus Sekolah
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="FormHapus">
                            <!-- Form Hapus Disini -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="NotifikasiHapus">
                            <!-- Notifikasi Hapus -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" disabled class="btn btn-success btn-rounded" id="ButtonHapus">
                        <i class="bi bi-check"></i> Ya, Hapus
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tidak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalImport" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark">
                    <i class="bi bi-upload"></i> Import AKB Per Sekolah
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <b>Petunjuk Penggunaan</b><br>
                            <small>
                                <ol>
                                    <li>Unduh <strong>Template</strong> yang telah disediakan agar format kolom sesuai dengan sistem.</li>
                                    <li>Jangan mengubah <em>urutan</em> atau <em>nama kolom</em> pada template. Perubahan menyebabkan kegagalan proses import.</li>
                                    <li>Isi data pada file Excel sesuai ketentuan kolom yang ada pada file tersebut.</li>
                                    <li>Simpan file dan unggah melalui tombol <strong>Pilih File Excel</strong>, lalu klik <strong>Mulai Import</strong>.</li>
                                    <li>Sistem akan melakukan validasi otomatis dan menampilkan hasil (baris valid / baris error).</li>
                                    <li>Untuk data besar (>100 baris), sistem akan memproses secara bertahap.</li>
                                </ol>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <form id="ProsesImport" action="javascript:void(0);">
                            <div class="input-group">
                                <input type="file" name="data_akb_per_sekolah" class="form-control" accept=".xlsx,.xls">
                                <a href="_Page/AbkPerSekolah/Template_ABK_Per_Sekolah.xlsx" class="btn btn-md btn-info" role="button" aria-label="Unduh Template Excel">
                                    <i class="bi bi-download"></i> Template
                                </a>
                                <button type="submit" class="btn btn-md btn-primary" id="btnImport">
                                    <i class="bi bi-upload"></i> Import
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Progress Bar -->
                <div class="row mb-3" id="progressSection" style="display: none;">
                    <div class="col-md-12">
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 id="progressBar" 
                                 style="width: 0%">
                                <span id="progressText">0%</span>
                            </div>
                        </div>
                        <div class="mt-2 text-center">
                            <small id="progressDetail">Memproses data...</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><b>No</b></th>
                                        <th><b>Provinsi</b></th>
                                        <th><b>Kab/Kota</b></th>
                                        <th><b>Sekolah</b></th>
                                        <th><b>Jabatan</b></th>
                                        <th><b>Keterangan</b></th>
                                    </tr>
                                </thead>
                                <tbody id="NotifikasiImport">
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <small class="text-danger">Belum Ada Proses Import</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" disabled class="btn btn-warning btn-rounded" id="ResetFormImport">
                    <i class="bi bi-arrow-repeat"></i> Reset Form
                </button>
                <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalExport" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="_Page/AbkPerSekolah/ProsesExport.php" method="GET" target="_blank">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-download"></i> Export Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12" id="FormExport">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" disabled class="btn btn-success btn-rounded" id="ButtonExport">
                        <i class="bi bi-download"></i> Export
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>