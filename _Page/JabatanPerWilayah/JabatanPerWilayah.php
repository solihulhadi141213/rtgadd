<div class="pagetitle">
    <h1>
        <a href="">
            <i class="bi bi-database"></i> Jabatan Per Wilayah</a>
        </a>
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Jabatan Per Wilayah</li>
        </ol>
    </nav>
</div>
<section class="section dashboard">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <small>
                    Berikut ini adalah halaman pengelolaan database Jabatan Per Wilayah. 
                    Anda bisa mengelola dataset yang ada dengan lebih cepat menggunakan fitur export dan import.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form action="javascript:void(0);" id="ProsesMultipleJabatan">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-md btn-success btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-file-earmark-arrow-down"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                    <li class="dropdown-header text-start">
                                        <h6>Option</h6>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalImportJabatan" title="Import Data">
                                            <i class="bi bi-upload"></i> Import
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalExportJabatan" title="Export Data">
                                            <i class="bi bi-download"></i> Export
                                        </a>
                                    </li>
                                </ul>
                                <button type="button" class="btn btn-md btn-secondary btn-floating" data-bs-toggle="modal" data-bs-target="#ModalFilterJabatan" title="Filter Data">
                                    <i class="bi bi-filter"></i>
                                </button>
                                <button type="button" class="btn btn-md btn-primary btn-floating" data-bs-toggle="modal" data-bs-target="#ModalTambahJabatan" title="Tambah Data Jabatan Per Wilayah">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" name="check_all" class="form-check-input" value="check_all">
                                        </th>
                                        <th><b>No</b></th>
                                        <th><b>Provinsi</b></th>
                                        <th><b>Kabupaten</b></th>
                                        <th><b>Jabatan</b></th>
                                        <th><b>ABK</b></th>
                                        <th><b>ASN-Negeri</b></th>
                                        <th><b>ASN-Swasta</b></th>
                                        <th><b>Kurang ASN</b></th>
                                        <th><b>Opsi</b></th>
                                    </tr>
                                </thead>
                                <tbody id="TabelJabatanPerWilayah">
                                    <tr>
                                        <td class="text-center" colspan="10">
                                            <small>Tidak ada data yang ditampilkan</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-sm btn-outline-secondary"  data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i> Option
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalHapusJabatanMultiple">
                                        <i class="bi bi-trash"></i> Hapus Data
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-6">
                                <small id="page_info">
                                    Page 1 Of 100
                                </small>
                            </div>
                            <div class="col-6 text-end">
                                <button type="button" class="btn btn-sm btn-outline-info btn-floating" id="prev_button">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info btn-floating" id="next_button">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>