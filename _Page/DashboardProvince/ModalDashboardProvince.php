<div class="modal fade" id="ModalDetailKabKot" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="index.php" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-funnel"></i> Detail Kab/Kota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12" id="FormDetailKabKot">
                            <!-- Menampilkan Detail Kabupaten/Kota -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" disabled class="btn btn-primary btn-rounded" id="ButtonSelengkapnya">
                        Selengkapnya <i class="bi bi-arrow-right-circle"></i>
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDetailByJenjang" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark"><i class="bi bi-funnel"></i> Kebutuhan Guru Berdasarkan Jenjang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12" id="ModalDetailContent">
                        <!-- Menampilkan Detail Kabupaten/Kota -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-rounded" id="BtnLihatUraianByJenjang">
                    <i class="bi bi-chevron-down"></i> Lihat Uraian
                </button>
                <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDetailKebutuhanGuruByJabatan" tabindex="-1">
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
                    <input type="hidden" name="province_code" id="put_province_code" value="">
                    <input type="hidden" name="id_position" id="put_id_positiom" value="">
                    <input type="hidden" name="school_level" id="put_school_level" value="">
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
                            <th><b><small>Kabupaten</small></b></th>
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
                            <td colspan="12" class="text-center">
                                <small>None</small>
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