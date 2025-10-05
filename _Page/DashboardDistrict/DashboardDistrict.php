<?php
    if(empty($_GET['district_code'])){
        echo '
            <section class="section dashboard">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Anda Belum Memilih Kabupaten/Kota Manapun!
                        </div>
                    </div>
                </div>
            </section>
        ';
        exit;
    }
    $district_code      = $_GET['district_code'];
    $id_region          = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');
    $district_name      = GetDetailData($Conn, 'region', 'district_code', $district_code, 'district_name');

    //Buka Provinsi
    $province_name      = GetDetailData($Conn, 'region', 'district_code', $district_code, 'province_name');
    $province_code      = GetDetailData($Conn, 'region', 'district_code', $district_code, 'province_code');
    
    //Validasi Apakah Kode Kabupaten/Kota Ada
    if(empty($id_region)){
        echo '
            <section class="section dashboard">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Kode Kabupaten/Kota Yang Anda Pilih Tidak Terdaftar
                        </div>
                    </div>
                </div>
            </section>
        ';
        exit;
    }

?>
<input type="hidden" id="district_code" value="<?php echo $district_code; ?>">
<div class="pagetitle">
    <h1>
        <a href="">
            RTG - KABUPATEN/KOTA <?php echo "$district_name "; ?>
        </a>
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="index.php">
                    <i class="bi bi-chevron-left"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="index.php?Page=DashboardProvince&province_code=<?php echo $province_code; ?>"><?php echo $province_name; ?></a>
            </li>
            <li class="breadcrumb-item active"><?php echo $district_name ?></li>
        </ol>
    </nav>
</div>
<section class="section dashboard">
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <b class="card-title">Jumlah Kebutuhan Guru</b>
                    <h2><b class="text-primary">616</b></h2>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <b class="card-title">Lulusan PPG yang Belum Diangkat </b>
                    <h2><b class="text-primary">54</b></h2>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <div class="row mb-3">
                        <div class="col-12">
                            <h4 class="card-title">Kebutuhan Guru menurut Jenjang Sekolah</h4>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12" id="ShowChartPie">
                            <!-- Menampilkan Chart Pie -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <b class="card-title">Guru menurut Jabatan</b>
                        </div>
                        <div class="col-4 text-end">
                            <button type="button" class="btn btn-md btn-secondary btn-floating" data-bs-toggle="modal" data-bs-target="#ModalFilter" title="Filter Data">
                                <i class="bi bi-filter"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th><b>No</b></th>
                                    <th><b>Jabatan</b></th>
                                    <th><b>Analisa Beban Kerja (ABK)</b></th>
                                    <th><b>ASN</b></th>
                                    <th><b>PPPK 2024</b></th>
                                    <th><b>Kebutuhan Guru</b></th>
                                </tr>
                            </thead>
                            <tbody id="TabelGuruByJabatan">
                                <tr>
                                    <td colspan="6" class="text-center">
                                        Loading...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <small id="data_count">Count : 0 Record</small>
                </div>
            </div>
        </div>
    </div>
</section>
