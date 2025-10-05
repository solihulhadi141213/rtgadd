<?php
    if(empty($_GET['province_code'])){
        echo '
            <section class="section dashboard">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Anda Belum Memilih Provinsi Manapun!
                        </div>
                    </div>
                </div>
            </section>
        ';
        exit;
    }
    $province_code = $_GET['province_code'];

    //Query ke database
    $sql = "
        SELECT 
            r.province_code,
            r.province_code_dapodik,
            r.province_name,
            r.district_code,
            r.district_code_dapodik,
            r.district_name,
            r.code_map,
            SUM(pr.abk) AS abk,
            SUM(pr.asn) AS asn,
            SUM(pr.jumlah_guru) AS jumlah_guru,
            SUM(pr.kurang_guru) AS kurang_guru,
            SUM(pr.kurang_asn) AS kurang_asn
        FROM region r
        LEFT JOIN position_region pr ON r.id_region = pr.id_region
        WHERE r.province_code = ?
        GROUP BY r.province_code
    ";
    $stmt = $Conn->prepare($sql);
    $stmt->bind_param("s", $province_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $province_code          = htmlspecialchars($row['province_code']);
        $province_code_dapodik  = htmlspecialchars($row['province_code_dapodik']);
        $province_name          = htmlspecialchars($row['province_name']);
        $abk                    = htmlspecialchars($row['abk']);
        $asn                    = htmlspecialchars($row['asn']);
        $jumlah_guru            = htmlspecialchars($row['jumlah_guru']);
        $kurang_guru            = htmlspecialchars($row['kurang_guru']);
        $kurang_asn             = htmlspecialchars($row['kurang_asn']);
    } else {
        $province_code  = "";
        echo '
            <section class="section dashboard">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Kode Provinsi Tidak Ditemukan
                        </div>
                    </div>
                </div>
            </section>
        ';
        exit;
    }

    $stmt->close();
?>
<input type="hidden" id="kode_provinsi" value="<?php echo $province_code; ?>">
<div class="pagetitle">
    <h1>
        <a href="">
            PROVINSI <?php echo "$province_name "; ?>
        </a>
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="index.php">
                <i class="bi bi-chevron-left"></i> Dashboard
                </a>
            </li>
        </ol>
    </nav>
</div>
<section class="section dashboard">
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-6 text-center border-1 border-end">
                                    <b class="card-title">Kebutuhan Guru</b>
                                    <H2>7.323</H2>
                                </div>
                                <div class="col-6 text-center border-1 border-start">
                                    <b class="card-title">Lulusan PPG Yang Belum Diangkat</b>
                                    <H2>7.323</H2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <di class="row mb-3">
        <div class="col-md-6 d-flex mb-3">
            <div class="card w-100 h-100">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12 text-center">
                            <b class="card-title">
                                Kebutuhan Guru menurut Jenjang di Level Provinsi
                            </b>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12 text-center">
                            <div id="kebutuhan_guru_by_jenjang">
                                <!-- Menampilkan grafik pie Apexchart -->
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="col-md-6 d-flex mb-3">
            <div class="card w-100 h-100">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12 text-center">
                            <b class="card-title">
                                Sebaran Kebutuhan Guru
                            </b>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12 text-center">
                            <div id="ShowMapProvinsiAndAkbupaten">
                                <small>Loading...</small>
                                <!-- Menampilkan peta interaktif menggunakan leaflet-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </di>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <b class="card-title">Kebutuhan Guru menurut kabupaten/Kota</b>
                        </div>
                        <div class="col-4 text-end">
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
                                    <th><b>Kabupaten/Kota</b></th>
                                    <th><b>Jenjang</b></th>
                                    <th><b>Kebutuhan Guru</b></th>
                                    <th><b>%</b></th>
                                </tr>
                            </thead>
                            <tbody id="TabelKebutuhanGuruByKabKot">
                                <tr>
                                    <td colspan="5" class="text-center">
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <b class="card-title">Guru Menurut Jabatan</b>
                        </div>
                        <div class="col-4 text-end">
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
                                    <th><b>Jabatan</b></th>
                                    <th><b>Analisis Beban Kerja (ABK)</b></th>
                                    <th><b>ASN</b></th>
                                    <th><b>PPPK 2024</b></th>
                                    <th><b>Kebutuhan Guru</b></th>
                                </tr>
                            </thead>
                            <tbody id="TabelKebutuhanGuruByKabKot">
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
</section>
