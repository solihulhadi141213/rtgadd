<?php
    if(empty($_GET['district_code'])){
        if(!empty($access_client)){
            //Jika province_code kosong cari berdasarkan access_client
            $id_region_client   = GetDetailData($Conn, 'access_client', 'id_access', $SessionIdAccess, 'id_region');
            $district_code      = GetDetailData($Conn, 'region', 'id_region', $id_region_client, 'district_code');
            if(!empty($district_code)){
                $district_code       = GetDetailData($Conn, 'region', 'id_region', $id_region_client, 'district_code');
                $district_name       = GetDetailData($Conn, 'region', 'id_region', $id_region_client, 'district_name');
                $province_code       = GetDetailData($Conn, 'region', 'id_region', $id_region_client, 'province_code');
                $province_name       = GetDetailData($Conn, 'region', 'id_region', $id_region_client, 'province_name');
                $id_region           = $id_region_client;
            }else{
                $district_code       = "";
                $district_name       = "";
                $province_code       = "";
                $province_name       = "";
                $id_region           = "";
            }
        }else{
            $district_code       = "";
            $district_name       = "";
            $province_code       = "";
            $province_name       = "";
            $id_region           = "";
        }
       
    }else{
        $district_code      = $_GET['district_code'];
        $district_name      = GetDetailData($Conn, 'region', 'district_code', $district_code, 'district_name');
        $province_code      = GetDetailData($Conn, 'region', 'district_code', $district_code, 'province_code');
        $province_name      = GetDetailData($Conn, 'region', 'district_code', $district_code, 'province_name');
        $id_region          = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');

        //Apabila Tidak Ditemukan Pada Tabel Region Maka Buka Dari Geo Region
        if(empty($district_name)){
            $district_name      = GetDetailData($Conn, 'geo_region', 'district_code', $district_code, 'district_name');
            $province_code      = GetDetailData($Conn, 'geo_region', 'district_code', $district_code, 'province_code');
            $province_name      = GetDetailData($Conn, 'geo_region', 'district_code', $district_code, 'province_name');
        }
    }
?>
<input type="hidden" id="district_code" value="<?php echo $district_code; ?>">
<div class="pagetitle">
    <h1>
        <a href="">
            <i class="bi bi-grid-3x3-gap"></i> Level Kabupaten
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
                <a href="index.php?Page=DashboardProvince&province_code=<?php echo $province_code; ?>">
                    Level Provinsi
                </a>
            </li>
            <li class="breadcrumb-item active">Level Kabupaten</li>
        </ol>
    </nav>
</div>
<section class="section dashboard">
    <?php
        //Apabila Belum Memilih Kab/Kota
        if(empty($district_code)){
            echo '
                <div class="row mb-2">
                    <div class="col-12 text-center">
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <h1><i class="bi bi-exclamation-triangle"></i></h1>
                            Anda Belum Memilih Kab/Kota Untuk Ditampilkan.
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-8">
                                        <b class="card-title">
                                            # Pilih Wilayah Kab/Kota
                                        </b>
                                    </div>
                                    <div class="col-4 text-end">
                                        <button type="button" class="btn btn-md btn-secondary btn-floating" data-bs-toggle="modal" data-bs-target="#ModalFilterKabKot" title="Pencarian">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><b>No</b></th>
                                                        <th><b>Provinsi</b></th>
                                                        <th><b>Kab/Kota</b></th>
                                                        <th><b>Opt</b></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="TabelKabKota">
                                                    <tr>
                                                        <td colspan="4" class="text-center">
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
                                        <small id="data_count_kabkot">
                                            Count : 0 Record
                                        </small>
                                    </div>
                                    <div class="col-6 text-end">
                                        <button type="button" disabled class="btn btn-sm btn-outline-info btn-floating" id="prev_button_kabkot">
                                            <i class="bi bi-chevron-left"></i>
                                        </button>
                                        <button type="button" disabled class="btn btn-sm btn-outline-info btn-rounded" id="page_info_kabkot">
                                            Page 1 / 0
                                        </button>
                                        <button type="button" disabled class="btn btn-sm btn-outline-info btn-floating" id="next_button_kabkot">
                                            <i class="bi bi-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ';
        }else{
            //Mulai Menampilkan Dashboard Tingkat Kab/Kota
            $district_name_low = ucwords(strtolower($district_name));
            $province_name_low = ucwords(strtolower($province_name));
            echo '
                <div class="row mb-3">
                    <div class="col-md-4 d-flex">
                        <div class="card w-100 h-100">
                            <div class="card-header text-center">
                                <h3>'.$district_name_low.'</h3>
                                <small>Provinsi '.$province_name_low.'</small>
                            </div>
                            <div class="card-body">
                                <div class="rom mb-3 border-bottom border-1">
                                    <div class="col-12 mb-3">
                                        <div class="row mb-2">
                                            <div class="col-6"><small>ABK</small></div>
                                            <div class="col-6 text-end">
                                                <small class="text-info" id="show_abk">'.$district_code.'</small>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6"><small>ASN</small></div>
                                            <div class="col-6 text-end">
                                                <small class="text-info" id="show_asn">'.$district_code.'</small>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6"><small>PPPK 2024</small></div>
                                            <div class="col-6 text-end">
                                                <small class="text-info" id="show_pppk2024">'.$district_code.'</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="rom mb-3 border-bottom border-1">
                                    <div class="col-12 mb-3  text-center">
                                        <span class="text text-grayish">Jumlah Kebutuhan Guru</span>
                                        <h2>
                                            <b class="text-primary" id="jumlah_kebutuhan_guru">Loading...</b>
                                        </h2>
                                    </div>
                                </div>
                                <div class="rom mb-3 border-bottom border-1">
                                    <div class="col-12 mb-3 text-center">
                                        <span class="text text-grayish">Lulusan PPG Calon Guru yang Belum Diangkat sebagai ASN (per 14 Agustus 2025)</span>
                                        <h2>
                                            <b class="text-primary" id="jumlah_ppg_belum_diangkat">Loading...</b>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 d-flex">
                        <div class="card  w-100 h-100">
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
                <div class="row" id="konten_kebutuhan_guru_menurut_jabatan">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-6 col-xs-7 col-sm-8 col-md-10">
                                        <b class="card-title"># Guru menurut Jabatan</b>
                                    </div>
                                    <div class="col-6 col-xs-5 col-sm-4 col-md-2 text-end">
                                        <form action="javascript:void(0);" id="ProsesFilter">
                                            <input type="hidden" name="page" id="page" value="1">
                                            <input type="hidden" name="district_code" id="district_code_filter" value="">
                                            <select name="school_level" id="school_level_by_kab_kot" class="form-control">
                                                <option value="">Semua Jenjang</option>
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
                                                <th><b><small>Analisa Beban Kerja</small><br>(ABK)</b></th>
                                                <th><b>ASN</b></th>
                                                <th><b>PPPK 2024</b></th>
                                                <th><b>Kebutuhan <br>Guru</b></th>
                                                <th><b>Opsi</b></th>
                                            </tr>
                                        </thead>
                                        <tbody id="TabelGuruByJabatan">
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    Loading...
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
            ';
        }
    ?>
</section>
