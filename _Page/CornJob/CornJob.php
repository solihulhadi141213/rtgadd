<div class="pagetitle">
    <h1>
        <a href="">
            <i class="bi bi-clock"></i> Corn Job</a>
        </a>
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Corn Job</li>
        </ol>
    </nav>
</div>
<section class="section dashboard">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <small>
                    Cron adalah tool yang memungkinkan user menginput command (perintah untuk menjadwalkan tugas berulang pada waktu tertentu. Cron job adalah tugas yang dijadwalkan di cron. User bisa menentukan tugas yang mereka inginkan untuk dijalankan secara otomatis beserta waktu eksekusinya.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12">
                            <b># Daftar Fungsi Corn Job</b>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th><b>No</b></th>
                                    <th><b>Nama Fungsi</b></th>
                                    <th><b>Deskripsi</b></th>
                                    <th><b>Directory</b></th>
                                    <th><b>Frekuenasi</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><small>1</small></td>
                                    <td><small>Update map_count.json</small></td>
                                    <td><small>Update data peta pada tingkat nasional</small></td>
                                    <td><small><a href="_Page/CornJob/update_map_count.php" target="_blank">_Page/CornJob/update_map_count.php</a></small></td>
                                    <td><small>1 Jam</small></td>
                                </tr>
                                <tr>
                                    <td><small>2</small></td>
                                    <td><small>Update map_count.json</small></td>
                                    <td><small>Update peta pada tingkat provinsi</small></td>
                                    <td><small><a href="_Page/CornJob/update_map_count_province.php" target="_blank">_Page/CornJob/update_map_count_province.php</a></small></td>
                                    <td><small>1 Jam</small></td>
                                </tr>
                                 <tr>
                                    <td><small>3</small></td>
                                    <td><small>Update kebutuhan_guru_by_jenjang.json</small></td>
                                    <td><small>Update kebutuhan guru berdasarkan jengjang pada provinsi</small></td>
                                    <td><small><a href="_Page/CornJob/update_kbutuhan_guru_by_jenjang.php" target="_blank">_Page/CornJob/update_kbutuhan_guru_by_jenjang.php</a></small></td>
                                    <td><small>1 Jam</small></td>
                                </tr>
                                <tr>
                                    <td><small>4</small></td>
                                    <td><small>ABK Sekolah Provinsi</small></td>
                                    <td><small>Membuat struktur JSON jumlah parameter berdasarkan jenjang pendidikan di tingkat provinsi</small></td>
                                    <td><small><a href="_Page/CornJob/abk_school_level_province.php" target="_blank">_Page/CornJob/abk_school_level_province.php</a></small></td>
                                    <td><small>1 Jam</small></td>
                                </tr>
                                <tr>
                                    <td><small>5</small></td>
                                    <td><small>Agregator Kabupaten</small></td>
                                    <td><small>Membuat agregator kabupaten</small></td>
                                    <td><small><a href="_Page/CornJob/refresh_cache.php" target="_blank">_Page/CornJob/refresh_cache.php</a></small></td>
                                    <td><small>1 Jam</small></td>
                                </tr>
                                <tr>
                                    <td><small>6</small></td>
                                    <td><small>Agregator Provinsi</small></td>
                                    <td><small>Membuat agregator provinsi</small></td>
                                    <td><small><a href="_Page/CornJob/cron_update_summary_province.php" target="_blank">_Page/CornJob/cron_update_summary_province.php</a></small></td>
                                    <td><small>1 Jam</small></td>
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