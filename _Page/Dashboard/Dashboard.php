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
                <b>Selamat Datang!</b>
                <p>
                    <?php echo $app_description; ?>
                </p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-6">
            <div class="card info-card customers-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="ps-3">
                            <b id="count_client">20.000</b><br>
                            <small>Client/User App</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card info-card customers-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-buildings"></i>
                        </div>
                        <div class="ps-3">
                            <b id="count_school">20.000</b><br>
                            <small>Sekolah - Terdaftar</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card info-card customers-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-person-workspace"></i>
                        </div>
                        <div class="ps-3">
                            <b id="count_teacher">20.000</b><br>
                            <small>Guru (Existing)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card info-card customers-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-award"></i>
                        </div>
                        <div class="ps-3">
                            <b id="count_position">20.000</b><br>
                            <small>Jabatan (Referensi)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <dv class="card-header text-center">
                    <h3>Top 10 Kebutuhan Guru Berdasarkan Formasi</h3>
                </dv>
                <div class="card-body" id="pie_of_count_position">
                    <!-- Menampilkan Pie Disini -->
                </div>
                <div class="card-footer">
                    <small class="text text-grayis">Update : <?php echo date('d F Y'); ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Sebaran Kebutuhan Guru</h3>
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
        <div class="col-lg-12">
            
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <b class="card-title">
                                Kebutuhan Guru /  <small class="text text-muted">5 Provinsi Tertinggi</small>
                            </b>
                        </div>
                        <div class="card-body">
                            <div class="activity">
                                <div class="table table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>Provinsi</th>
                                                <th class="text-end">Kebutuhan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="top_guru_provinsi">
                                            <tr>
                                                <td colspan="2" class="text-center">
                                                    <small class="text-danger">Belum Ada Data Yang Ditampilkan</small>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <b class="card-title">
                                Kebutuhan Guru /  <small class="text text-muted">5 Kabupaten Tertinggi</small>
                            </b>
                        </div>
                        <div class="card-body">
                            <div class="activity" id="">
                                <div class="table table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>Kabupaten</th>
                                                <th>Provinsi</th>
                                                <th class="text-end">Kebutuhan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="top_guru_kabupaten">
                                            <tr>
                                                <td colspan="3" class="text-center">
                                                    <small class="text-danger">Belum Ada Data Yang Ditampilkan</small>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
