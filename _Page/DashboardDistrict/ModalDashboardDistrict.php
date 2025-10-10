<div class="modal fade" id="ModalFilterKabKot" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesFilterKabKot">
                <input type="hidden" name="page" id="page_kabkot" value="1">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-search"></i> Form Pencarian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="keyword_kabkot">
                                <small>Masukan Kata Kunci</small>
                            </label>
                            <input type="text" name="keyword" id="keyword_kabkot" class="form-control" placeholder="Kata Kunci">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-rounded">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDetailKabKot" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="index.php" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-info-circle"></i> Detail Kab/Kota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12" id="FormDetailKabKot">
                            <!-- Menampilkan Detail Kab/Kota -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-rounded">
                        Selengkapnya <i class="bi bi-chevron-right"></i>
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
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-12">
                        <b class="card-title">Distribusi Kebutuhan Guru</b><br>
                        <small id="title_position_province_name">Loading..</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body table table-responsive">
                <form action="javascript:void(0);" id="FilterTabelJabatan">
                    <input type="hidden" name="page" id="page_detail_jabatan" value="1">
                    <input type="hidden" name="id_region" id="put_id_region" value="">
                    <input type="hidden" name="id_position" id="put_id_positiom" value="">
                </form>
                <!-- <div class="row mb-3 border-1 border-bottom">
                    <div class="col-12 text-center">
                        <h3>DISTRIBUSI KEBUTUHAN GURU BERDASARJAN JABATAN</h3>
                        <span class="card-title">Untuk Jabatan <b id="title_position_name">Loading..</b></span>
                        <p id="title_province_name">Loading..</p>
                    </div>
                </div> -->
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><b><small>No</small></b></th>
                            <th><b><small>Sekolah</small></b></th>
                            <th><b><small>ABK</b></small></th>
                            <th><b><small>ASN</b></small></th>
                            <th><b><small>PPK <br>2024</b></small></th>
                            <th><b><small>Non ASN <br>< 10/2022</b></small></th>
                            <th><b><small>Non ASN <br> > 10/2022</b></small></th>
                            <th><b><small>Jumlah <br> Guru</b></small></th>
                            <th><b><small>Kurang <br> Guru</b></small></th>
                            <th><b><small>Jumlah <br> ASN</b></small></th>
                            <th><b><small>Kurang <br> ASN</b></small></th>
                        </tr>
                    </thead>
                    <tbody id="TabelDetailJabatan">
                        <tr>
                            <td colspan="11" class="text-center">
                                <small>Loading...</small>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <small id="data_count_school">
                    Count : 0 Record
                </small>
                <button type="button" disabled class="btn btn-md btn-outline-info btn-floating" id="prev_button_school">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button type="button" disabled class="btn btn-md btn-outline-info btn-rounded" id="page_info_school">
                    Page 1 / 0
                </button>
                <button type="button" disabled class="btn btn-md btn-outline-info btn-floating" id="next_button_school">
                    <i class="bi bi-chevron-right"></i>
                </button>
                <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDetailSchoolLevel" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark"><i class="bi bi-info-circle"></i> Detail Jenjang Pendidikan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-12" id="FormDetailSchoolLevel">
                        <!-- Menampilkan Detail Jenjang Pendidikan -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-rounded" id="BtnLihatUraianByJenjang">
                    <i class="bi bi-chevron-down"></i> Selengkapnya 
                </button>
                <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>