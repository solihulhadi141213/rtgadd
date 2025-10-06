<div class="pagetitle">
    <h1>
        <a href="">
            Level Provinsi
        </a>
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="index.php">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Provinsi</li>
        </ol>
    </nav>
</div>

<?php
    if(empty($_GET['province_code'])){
        echo '
            <section class="section dashboard">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <b>Anda Belum Memilih Provinsi Manapun!</b>
                            <p><small>Untuk menampilkan <i>Dashboard</i> pada tingkat Provinsi, anda harus menyertakan parameter kode provinsi.</small></p>
                        </div>
                    </div>
                </div>
            </section>
        ';
        include "_Page/DashboardProvince/FormPilihProvinsi.php";
    }else{
        $province_code = $_GET['province_code'];
        $province_name = GetDetailData($Conn, 'geo_region', 'province_code', $province_code, 'province_name');

        //Jika Data Tidak Ditemukan
        if(empty($province_name)){
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
        }else{
?>

<section class="section dashboard">

    <!-- Informasi Kode Provinsi -->
    <input type="hidden" id="kode_provinsi" value="<?php echo $province_code; ?>">

    <!-- Nama Provinsi, Kebutuhan Guru Dan Lulusan PPG -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center">
                    <h2>PROVINSI <?php echo "$province_name "; ?></h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-center border-1 border-end">
                            <b class="card-title">Kebutuhan Guru</b>
                            <h2 id="show_nominal_kebutuhan_guru">Loading..</h2>
                        </div>
                        <div class="col-6 text-center border-1 border-start">
                            <b class="card-title">Lulusan PPG Yang Belum Diangkat</b>
                            <h2 id="show_lulusan_ppg_pending">Loading..</h2>
                        </div>
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
                            <b class="card-title">
                                # Kebutuhan Guru menurut kabupaten/Kota 
                                <span id="TitleKebutuhanGuruByKabKot">
                                    <!-- Menampilkan Title Jenjang Disini -->
                                </span>
                            </b>
                        </div>
                        <div class="col-4 text-end">
                            <button type="button" class="btn btn-md btn-secondary btn-floating" data-bs-toggle="modal" data-bs-target="#ModalFilterKebutuhanGuruByKabKot" title="Filter Data">
                                <i class="bi bi-filter"></i>
                            </button>
                        </div>
                    </div>
                    
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <span id="TitleKebutuhanGuruByKabKot">
                                <!-- Menampilkan Title Jenjang Disini -->
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="table table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th><b>No</b></th>
                                            <th><b>Kabupaten/Kota</b></th>
                                            <th><b>Jumlah <br>Sekolah</b></th>
                                            <th><b>Kebutuhan <br> Guru</b></th>
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
                        <div class="col-6 col-xs-7 col-sm-8 col-md-10">
                            <b class="card-title"># Guru Menurut Jabatan</b>
                        </div>
                        <div class="col-6 col-xs-5 col-sm-4 col-md-2 text-end">
                            <form action="javascript:void(0);" id="ProsesFilterKebutuhanGuruByJabatan">
                                <input type="hidden" name="page" id="page_kebutuhan_guru_by_jabatan" value="1">
                                <input type="hidden" name="province_code" value="<?php echo "$province_code"; ?>">
                                <select name="school_level" id="school_level_2" class="form-control">
                                    <option value="">Semua Jenjang</option>
                                    <?php
                                        //Jenjang Pendidikan
                                        $no_row = 1;
                                        $query = mysqli_query($Conn, "SELECT DISTINCT school_level FROM school ");
                                        while ($data = mysqli_fetch_array($query)) {
                                            $school_level = $data['school_level'];
                                            echo '
                                                <option value="'.$school_level.'">'.$school_level.'</option>
                                            ';
                                            $no_row++;
                                        }
                                    ?>
                                </select>
                            </form>
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
                            <tbody id="TabelKebutuhanGuruByJabatan">
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
                            <small id="data_count_2">
                                Count : 0 Record
                            </small>
                        </div>
                        <div class="col-6 text-end">
                            <button type="button" disabled class="btn btn-sm btn-outline-info btn-floating" id="prev_button_2">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button type="button" disabled class="btn btn-sm btn-outline-info btn-rounded" id="page_info_2">
                                Page 1 / 0
                            </button>
                            <button type="button" disabled class="btn btn-sm btn-outline-info btn-floating" id="next_button_2">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
        }
    }
?>
