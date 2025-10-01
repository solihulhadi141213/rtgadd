<div class="modal fade" id="ModalFilterJabatan" tabindex="-1">
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
                                <option value="province_name">Provinsi</option>
                                <option value="district_name">Kab/Kota</option>
                                <option value="position_name">Jabatan</option>
                                <option value="abk">ABK</option>
                                <option value="asn">ASN</option>
                                <option value="asn_di_negeri">ASN Di Negeri</option>
                                <option value="asn_di_swasta">ASN Di Swasta</option>
                                <option value="NonASN_sblmOkt2022">Non ASN Sebelum Oktober 2022</option>
                                <option value="NonASN_stlhOkt2022">Non ASN Setelah Oktober 2022</option>
                                <option value="pppk2024">PPPK 2024</option>
                                <option value="jumlah_guru">Jumlah Guru</option>
                                <option value="kurang_guru">Kurang Guru</option>
                                <option value="jumlah_asn">Jumlah ASN</option>
                                <option value="kurang_asn">Kekurangan ASN</option>
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
                                <option value="province_name">Provinsi</option>
                                <option value="district_name">Kab/Kota</option>
                                <option value="position_name">Jabatan</option>
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
<div class="modal fade" id="ModalTambahJabatan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesTambahJabatan" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title text-dak"><i class="bi bi-plus"></i> Tambah Jabatan Per Wilayah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="province_code">
                                <small>Provinsi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                            </label>
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
                            <label for="district_code">
                                <small>Kab/Kota <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <select name="district_code" id="district_code" class="form-control" required>
                                <option value="">Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="id_position">
                                <small>Jabatan/Posisi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <select name="id_position" id="id_position" class="form-control" required>
                                <option value="">Pilih</option>
                                <?php
                                    //Menampilkan list provinsi
                                    $query = mysqli_query($Conn, "SELECT id_position, position_name FROM position ORDER BY position_name ASC");
                                    while ($data = mysqli_fetch_array($query)) {
                                        $id_position        = $data['id_position'];
                                        $position_name      = $data['position_name'];
                                        echo '<option value="'.$id_position.'">'.$position_name.'</option>';
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
                            <label for="asn_di_negeri">
                                <small>ASN Di Sekolah Negeri</small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="asn_di_negeri" id="asn_di_negeri" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="asn_di_swasta">
                                <small>ASN Di Sekolah Swasta</small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="asn_di_swasta" id="asn_di_swasta" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="NonASN_sblmOkt2022">
                                <small>Non ASN Sebelum Oktober 2022</small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="NonASN_sblmOkt2022" id="NonASN_sblmOkt2022" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="NonASN_stlhOkt2022">
                                <small>Non ASN Setelah Oktober 2022</small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="NonASN_stlhOkt2022" id="NonASN_stlhOkt2022" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="pppk2024">
                                <small>PPPK 2024</small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="pppk2024" id="pppk2024" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="jumlah_guru">
                                <small>Jumlah Guru</small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="jumlah_guru" id="jumlah_guru" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="kurang_guru">
                                <small>Kurang Guru</small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="kurang_guru" id="kurang_guru" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="jumlah_asn">
                                <small>Jumlah ASN</small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="jumlah_asn" id="jumlah_asn" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="kurang_asn">
                                <small>Kurang ASN</small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="kurang_asn" id="kurang_asn" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12" id="NotifikasiTambahJabatan">
                            <!-- Notifikasi Tambah Jabatan Akan Muncul Disini -->
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

<div class="modal fade" id="ModalDetailJabatan" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark">
                    <i class="bi bi-info-circle"></i> Detail Jabatan Per Wilayah
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="FormDetailJabatan">
                        <!-- Form Detail Jabatan Per Wilayah -->
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

<div class="modal fade" id="ModalEditJabatan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesEditJabatan">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-pencil"></i> Edit Jabatan Per Wilayah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12" id="FormEditJabatan">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" id="NotifikasiEditJabatan">
                            <!-- Notifikasi Edit Siswa Akan Muncul Disini -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" disabled class="btn btn-success btn-rounded" id="ButtonEditJabatan">
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

<div class="modal fade" id="ModalHapusJabatan" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesHapusJabatan">
                <div class="modal-header">
                    <h5 class="modal-title text-dark">
                        <i class="bi bi-trash"></i> Hapus Jabatan Per Wialayah
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="FormHapusJabatan">
                            <!-- Form Hapus Jabatan Disini -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="NotifikasiHapusJabatan">
                            <!-- Notifikasi Hapus Jabatan -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" disabled class="btn btn-success btn-rounded" id="ButtonHapusJabatan">
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

<div class="modal fade" id="ModalImportJabatan" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark">
                    <i class="bi bi-upload"></i> Import Jabatan
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
                                </ol>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <form id="ProsesImportJabatan" action="javascript:void(0);">
                            <div class="input-group">
                                <input type="file" name="data_jabatan" class="form-control" accept=".xlsx,.xls">
                                <a href="_Page/JabatanPerWilayah/Template-Jabatan-Per-Wilayah.xlsx" class="btn btn-md btn-info" role="button" aria-label="Unduh Template Excel">
                                    <i class="bi bi-download"></i> Template
                                </a>
                                <button type="submit" class="btn btn-md btn-primary">
                                    <i class="bi bi-upload"></i> Import
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><b>Baris</b></th>
                                        <th><b>Provinsi</b></th>
                                        <th><b>Kabupaten</b></th>
                                        <th><b>Jabatan</b></th>
                                        <th><b>Keterangan</b></th>
                                    </tr>
                                </thead>
                                <tbody id="NotifikasiImportJabatan">
                                    <tr>
                                        <td colspan="5" class="text-center">
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
                <button type="button" disabled class="btn btn-warning btn-rounded" id="ResetFormImportJabatan">
                    <i class="bi bi-arrow-repeat"></i> Reset Form
                </button>
                <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalExportJabatan" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="_Page/JabatanPerWilayah/ProsesExportJabatan.php" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-download"></i> Export Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12" id="FormExportJabatan">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" disabled class="btn btn-success btn-rounded" id="ButtonExportJabatan">
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

<div class="modal fade" id="ModalHapusJabatanMultiple" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesHapusJabatanMultiple">
                <div class="modal-header">
                    <h5 class="modal-title text-dark">
                        <i class="bi bi-building"></i> Hapus Jabatan Per Wilayah (Multiple)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th><b>No</b></th>
                                            <th><b>Provinsi</b></th>
                                            <th><b>Kab/Kota</b></th>
                                            <th><b>Jabatan</b></th>
                                        </tr>
                                    </thead>
                                    <tbody id="FormHapusJabatanMultiple">
                                        <tr>
                                            <td class="text-center" colspan="4">
                                                <small>Tidak Ada Data Yang Dipilih</small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="NotifikasiHapusJabatanMultiple">
                            <!-- Notifikasi Update Kelas -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" disabled class="btn btn-success btn-rounded" id="ButtonHapusJabatanMultiple">
                        <i class="bi bi-check"></i> Ya, Hapus
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>