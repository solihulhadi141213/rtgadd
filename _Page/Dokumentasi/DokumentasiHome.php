<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-md btn-secondary btn-floating" data-bs-toggle="modal" data-bs-target="#ModalFilter" title="Filter Data">
                            <i class="bi bi-filter"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th><b>No</b></th>
                                <th><b>Judul/Tema</b></th>
                                <th><b>Kategori</b></th>
                                <th><b>Tanggal</b></th>
                                <th><b>Status</b></th>
                                <th><b>Opt</b></th>
                            </tr>
                        </thead>
                        <tbody id="TabelDokumentasi">
                            <tr>
                                <td class="text-center" colspan="6">
                                    <small>Tidak ada data yang ditampilkan</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-6">
                        <small id="data_count">
                            Count : 0 Record
                        </small>
                    </div>
                    <div class="col-6 text-end">
                        <button type="button" disabled class="btn btn-sm btn-outline-info btn-floating" id="prev_button">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button type="button" disabled class="btn btn-sm btn-outline-info btn-rounded" id="page_info">
                            Page 1 / 0
                        </button>
                        <button type="button" disabled class="btn btn-sm btn-outline-info btn-floating" id="next_button">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>