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
                                <option value="province_name">Provinsi</option>
                                <option value="district_name">Kab/Kota</option>
                                <option value="perguruan_tinggi_s1">Perguruan Tiinggi</option>
                                <option value="program_studi_s1">Program Studi</option>
                                <option value="bidang_studi_ppg">Bidang Studi</option>
                                <option value="lptk">LPTK</option>
                                <option value="ppg_blm_diangkat">Status ASN</option>
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
                                <option value="perguruan_tinggi_s1">Perguruan Tiinggi</option>
                                <option value="program_studi_s1">Program Studi</option>
                                <option value="bidang_studi_ppg">Bidang Studi</option>
                                <option value="lptk">LPTK</option>
                                <option value="ppg_blm_diangkat">Status ASN</option>
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
<div class="modal fade" id="ModalHapus" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesHapus">
                <div class="modal-header">
                    <h5 class="modal-title text-dark">
                        <i class="bi bi-trash"></i> Hapus PPG (Calon Guru)
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
                    <button type="submit" class="btn btn-success btn-rounded" id="ButtonHapus">
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

<!-- Modal Import CSV -->
<div class="modal fade" id="ModalImportCsv" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark">
                    <i class="bi bi-upload"></i> Import AKB Per Sekolah (CSV)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <small>
                            Download template file CSV <a href="_Page/CalonGuru/template-ppg-calon-guru-lulusan.csv">berikut ini</a>
                        </small>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <form id="ProsesImportCsv" action="javascript:void(0);" enctype="multipart/form-data">
                            <div class="input-group">
                                <input type="file" name="data_ppg_calon_guru_lulusan" id="data_ppg_calon_guru_lulusan" class="form-control" accept=".csv,text/csv" required>
                                <button type="submit" class="btn btn-md btn-primary" id="btnImportCsv">
                                    <i class="bi bi-upload"></i> Import
                                </button>
                                <button type="button" disabled class="btn btn-md btn-danger" id="BtnStoProccess">
                                    <i class="bi bi-stop"></i> Stop
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

                <!-- Laporan -->
                <!-- Laporan Detail dalam Tabel -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="text-dark mb-3">Rekapitulasi Proses Import</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="45%">Jenis Proses</th>
                                        <th width="25%">Status</th>
                                        <th width="25%">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody id="reportTableBody">
                                    <!-- Data akan diisi oleh JavaScript -->
                                </tbody>
                                <tfoot>
                                    <tr class="table-info">
                                        <td colspan="3" class="text-end"><strong>Total Data Diproses</strong></td>
                                        <td><strong id="totalProcessed">0</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Detail Error -->
                <div class="row mt-3" id="errorDetailsSection" style="display: none;">
                    <div class="col-12">
                        <h6 class="text-danger mb-3">Detail Kesalahan</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-danger">
                                    <tr>
                                        <th width="10%">No</th>
                                        <th width="30%">Jenis Error</th>
                                        <th width="60%">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody id="errorDetailsBody">
                                    <!-- Detail error akan diisi oleh JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" disabled class="btn btn-warning" id="ResetFormImportCsv">
                    <i class="bi bi-arrow-repeat"></i> Reset Form
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>
