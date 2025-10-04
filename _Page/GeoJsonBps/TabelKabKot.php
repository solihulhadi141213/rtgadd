<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");
    $JmlHalaman=0;
    $page=0;
    //Validasi Akses
    if(empty($SessionIdAccess)){
        echo '
            <tr>
                <td colspan="5" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
        ';
        exit;
    }
    //id_geo_region
    if(empty($_POST['id_geo_region'])){
        echo '
            <tr>
                <td colspan="5" class="text-center">
                    <small class="text-danger">ID Wilayah Tidak Boleh Kosong!</small>
                </td>
            </tr>
        ';
        exit;
    }
    $id_geo_region=$_POST['id_geo_region'];

    //Validasi Data
    $province_code = GetDetailData($Conn, 'geo_region','id_geo_region', $id_geo_region, 'province_code');
    $province_name = GetDetailData($Conn, 'geo_region','id_geo_region', $id_geo_region, 'province_name');
    if(empty($province_code)){
        echo '
            <tr>
                <td colspan="5" class="text-center">
                    <small class="text-danger">ID Wilayah Tidak Valid!</small>
                </td>
            </tr>
        ';
        exit;
    }
    
    //Hitung Data
    $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_geo_region FROM geo_region WHERE province_code='$province_code' AND level_region='District'"));
    if(empty($jml_data)){
         echo '
            <tr>
                <td colspan="5" class="text-center">
                    <small class="text-danger">Belum Ada Data Kab/Kota Untuk Provinsi <b>'.$province_name.'</b> ini</small>
                </td>
            </tr>
        ';
        exit;
    }
    $no = 1;
    //KONDISI PENGATURAN MASING FILTER
    $query = mysqli_query($Conn, "SELECT*FROM geo_region WHERE province_code='$province_code' AND level_region='District'");
    while ($data = mysqli_fetch_array($query)) {
        $id_geo_region_list     = $data['id_geo_region'];
        $level_region           = $data['level_region'];
        $district_code          = $data['district_code'];
        $district_name          = $data['district_name'];
        $coordinates            = $data['coordinates'];
        if(!empty($coordinates)){
            $coordinates_label = '
                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#ModalShowMap" data-id="'.$id_geo_region_list .'">
                    <i class="bi bi-map"></i> Show Map
                </a>
            ';
        }else{
            $coordinates_label = '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> NULL</span>';
        }
        echo '
            <tr>
                <td><small>'.$no.'</small></td>
                <td>
                    <small>'.$district_code.'</small>
                    <input type="hidden" name="district_code[]" value="'.$district_code.'">
                </td>
                <td><small>'.$district_name.'</small></td>
                <td><small>'.$coordinates_label.'</small></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                        <li class="dropdown-header text-start">
                            <h6>Option</h6>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_geo_region_list .'">
                                <i class="bi bi-info-circle"></i> Detail
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalShowMap" data-id="'.$id_geo_region_list .'">
                                <i class="bi bi-map"></i> Show Map
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalEdit" data-id="'.$id_geo_region_list .'">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalHapus" data-id="'.$id_geo_region_list .'">
                                <i class="bi bi-x"></i> Hapus
                            </a>
                        </li>
                    </ul>
                </td>
            </tr>
        ';
        $no++;
    }
?>