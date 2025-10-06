<div class="pagetitle">
    <h1>
        <a href="">
            <i class="bi bi-grid"></i> Dashboard
        </a>
    </h1>
</div>
<section class="section dashboard">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                Selamat Datang!
                <p>
                    RTGAdd adalah aplikasi berbasis web yang dirancang untuk memadukan RTG dan pengembangan dasbor untuk memudahkan pemerintah pusat dan daerah 
                    dalam melakukan perencanaan dan pengawasan kebutuhan guru di satuan pendidikan berbasis data. RTGAdd akan diujicobakan penggunaannya di lima kabupaten/kota, 
                    yaitu: Kabupaten Karo - Sumatera Utara, Kota Dumai - Riau, Kabupaten Muaro Jambi - Jambi, Kota Semarang - Jawa Tengah, dan Kabupaten Kutai Kartanegara - Kalimantan Timur
                </p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center">
                    <h1 class="card-title">SEBARAN KEBUTUHAN GURU</h1>
                </div>
                <div class="card-body">
                    <div id="indonesia-map">
                        <!-- Menampilkan Peta Disini -->
                    </div>
                </div>
                <div class="card-footer">
                    <small class="text text-grayis">Update : <?php echo date('d F Y'); ?></small>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
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
                                    <th><b>Provinsi</b></th>
                                    <th><b>Analisis Beban Kerja (ABK)</b></th>
                                    <th><b>ASN</b></th>
                                    <th><b>Kebutuhan Guru</b></th>
                                    <th><b>Opt</b></th>
                                </tr>
                            </thead>
                            <tbody id="TabelKebutuhanGuru">
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <small>Loading...</small>
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
                                Count : 100 Record
                            </small>
                        </div>
                        <div class="col-6 text-end">
                            <button type="button" class="btn btn-sm btn-outline-info btn-floating" id="prev_button">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button type="button" disabled class="btn btn-sm btn-outline-info btn-rounded" id="page_info">
                                Page 1 / 100
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info btn-floating" id="next_button">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
