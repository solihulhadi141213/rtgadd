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


    <di class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <b class="card-title">Kebutuhan Guru menurut Jenjang di Level Provinsi</b>
                </div>
                <div class="card-body" id="kebutuhan_guru_by_jenjang">
                    <!-- Menampilkan grafik pie Apexchart -->
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <b class="card-title">Sebaran Kebutuhan Guru</b>
                </div>
                <div class="card-body" id="ShowMapProvinsiAndAkbupaten">
                    <!-- Menampilkan peta interaktif menggunakan leaflet-->
                    Loading...
                </div>
            </div>
        </div>
    </di>
</section>
