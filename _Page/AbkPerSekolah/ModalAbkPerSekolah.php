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
                                <option value="province">Provinsi</option>
                                <option value="regency">Kab/Kota</option>
                                <option value="school_name">Sekolah</option>
                                <option value="position_name">Jabatan</option>
                                <option value="abk">ABK</option>
                                <option value="asn">ASN</option>
                                <option value="PPPK2024">PPPK 2024</option>
                                <option value="NonASN_sblmOkt2022">Non ASN < Oktober 2022</option>
                                <option value="NonASN_stlhOkt2022">Non ASN > Oktober 2022</option>
                                <option value="JmlGuru">Jumlah Guru</option>
                                <option value="KurangGuru">Kurang Guru</option>
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
                                <option value="province">Provinsi</option>
                                <option value="regency">Kab/Kota</option>
                                <option value="school_name">Sekolah</option>
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
<div class="modal fade" id="ModalTambah" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesTambahJabatan" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title text-dak"><i class="bi bi-plus"></i> Tambah Jabatan Per Wilayah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="province">
                                <small>Provinsi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="province" id="province" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="regency">
                                <small>Kab/Kota <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="regency" id="regency" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="department">
                                <small>Jabatan/Posisi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="department" id="department" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="workload">
                                <small>ABK</small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="workload" id="workload" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="officials_public">
                                <small>ASN Sekolah Negeri</small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="officials_public" id="officials_public" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="officials_private">
                                <small>ASN Sekolah Swasta</small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="officials_private" id="officials_private" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="manpower_gap">
                                <small>Kekurangan ASN</small>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="manpower_gap" id="manpower_gap" class="form-control">
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
    <div class="modal-dialog modal-md">
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
                                <a href="_Page/JabatanPerWilayah/Template-1.xlsx" class="btn btn-md btn-info" role="button" aria-label="Unduh Template Excel">
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
                                        <th><b>ABK</b></th>
                                        <th><b>ASN-Negeri</b></th>
                                        <th><b>ASN-Swasta</b></th>
                                        <th><b>Kurang ASN</b></th>
                                        <th><b>Keterangan</b></th>
                                    </tr>
                                </thead>
                                <tbody id="NotifikasiImportJabatan">
                                    <tr>
                                        <td colspan="9" class="text-center">
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