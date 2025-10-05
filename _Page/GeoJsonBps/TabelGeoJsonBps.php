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
                <td colspan="7" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
        ';
    }else{
        //Keyword_by
        if(!empty($_POST['keyword_by'])){
            $keyword_by=$_POST['keyword_by'];
        }else{
            $keyword_by="";
        }
        //keyword
        if(!empty($_POST['keyword'])){
            $keyword=$_POST['keyword'];
        }else{
            $keyword="";
        }
        //batas
        if(!empty($_POST['batas'])){
            $batas=$_POST['batas'];
        }else{
            $batas="10";
        }
        //ShortBy
        if(!empty($_POST['ShortBy'])){
            $ShortBy=$_POST['ShortBy'];
        }else{
            $ShortBy="ASC";
        }
        //OrderBy
        if(!empty($_POST['OrderBy'])){
            $OrderBy=$_POST['OrderBy'];
        }else{
            $OrderBy="province_code";
        }
        //Atur Page
        if(!empty($_POST['page'])){
            $page=$_POST['page'];
            $posisi = ( $page - 1 ) * $batas;
        }else{
            $page="1";
            $posisi = 0;
        }
        if(empty($keyword_by)){
            if(empty($keyword)){
                $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_geo_region FROM geo_region WHERE level_region='Province'"));
            }else{
                $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_geo_region FROM geo_region WHERE (level_region='Province') AND (province_code like '%$keyword%' OR province_name like '%$keyword%' OR district_code like '%$keyword%' OR district_name like '%$keyword%')"));
            }
        }else{
            if(empty($keyword)){
                $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_geo_region FROM geo_region WHERE level_region='Province'"));
            }else{
                $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_geo_region FROM geo_region  WHERE (level_region='Province') AND ($keyword_by like '%$keyword%')"));
            }
        }
        
        //Mengatur Halaman
        $JmlHalaman = ceil($jml_data/$batas); 
        if(empty($jml_data)){
            echo '
                <tr>
                    <td colspan="7" class="text-center">
                        <small class="text-danger">Tidak Ada Data Yang Ditampilkan!</small>
                    </td>
                </tr>
            ';
        }else{
            $no = 1+$posisi;
            //KONDISI PENGATURAN MASING FILTER
            if(empty($keyword_by)){
                if(empty($keyword)){
                    $query = mysqli_query($Conn, "SELECT*FROM geo_region WHERE level_region='Province' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
                }else{
                    $query = mysqli_query($Conn, "SELECT*FROM geo_region WHERE (level_region='Province') AND (level_region like '%$keyword%' OR province_code like '%$keyword%' OR province_name like '%$keyword%' OR district_code like '%$keyword%' OR district_name like '%$keyword%') ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
                }
            }else{
                if(empty($keyword)){
                    $query = mysqli_query($Conn, "SELECT*FROM geo_region WHERE level_region='Province' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
                }else{
                    $query = mysqli_query($Conn, "SELECT*FROM geo_region  WHERE (level_region='Province') AND ($keyword_by like '%$keyword%') ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
                }
            }
            while ($data = mysqli_fetch_array($query)) {
                $id_geo_region          = $data['id_geo_region'];
                $level_region           = $data['level_region'];
                $province_code          = $data['province_code'];
                $province_name          = $data['province_name'];
                $coordinates            = $data['coordinates'];
                if(empty($data['district_name'])){
                    $district_name          = "-";
                    $district_code          = "-";
                }else{
                    $district_name          = $data['district_name'];
                    $district_code          = $data['district_code'];
                }
                //Routing category
                if($level_region=="District"){
                    $level_region_label='<span class="badge badge-success">Kab/Kota</span>';
                }else{
                    $level_region_label='<span class="badge badge-primary">Provinsi</span>';
                }

                if(!empty($coordinates)){
                    $coordinates_label = '
                        <a href="javascript:void(0);" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#ModalShowMap" data-id="'.$id_geo_region .'">
                            <i class="bi bi-map"></i> Show Map
                        </a>
                    ';
                }else{
                    $coordinates_label = '
                        <a href="javascript:void(0);" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#ModalGetCoordinates" data-id="'.$id_geo_region .'">
                            <i class="bi bi-download"></i> Get Coordinate
                        </a>
                    ';
                }

                //Hitung jumlah kabupaten
                $JumlahKabupaten = mysqli_num_rows(mysqli_query($Conn, "SELECT id_geo_region FROM geo_region WHERE (level_region='District') AND province_code='$province_code'"));

                //Jumlah Kabupaten Punya Coordinates
                $JumlahKabupatenCoordinates = mysqli_num_rows(mysqli_query($Conn, "SELECT id_geo_region FROM geo_region WHERE level_region='District' AND province_code='$province_code' AND coordinates!=''"));
                
                //Buat labelJumlahKab
                if(empty($JumlahKabupaten)){
                    $labelJumlahKab='
                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#ModalGetKabKot" data-id="'.$id_geo_region .'">
                            <i class="bi bi-download"></i>
                        </a>
                    ';
                }else{
                    if(empty($JumlahKabupatenCoordinates)){
                        $labelJumlahKab='
                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_geo_region .'">
                                '.$JumlahKabupaten.' / '.$JumlahKabupatenCoordinates.'
                            </a>
                        ';
                    }else{
                        $labelJumlahKab='
                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_geo_region .'">
                                '.$JumlahKabupaten.' / '.$JumlahKabupatenCoordinates.'
                            </a>
                        ';
                    }
                    
                }
                echo '
                    <tr>
                        <td><small>'.$no.'</small></td>
                        <td>
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_geo_region .'">
                                '.$level_region_label.'
                            </a>
                        </td>
                        <td><small>'.$province_code.'</small></td>
                        <td><small>'.$province_name.'</small></td>
                        <td><small>'.$labelJumlahKab.'</small></td>
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
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_geo_region .'">
                                        <i class="bi bi-info-circle"></i> Detail
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalGetKabKot" data-id="'.$id_geo_region .'">
                                        <i class="bi bi-airplane"></i> Get Kab/Kot
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalGetCoordinates" data-id="'.$id_geo_region .'">
                                        <i class="bi bi-download"></i> Get Coordinate
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalShowMap" data-id="'.$id_geo_region .'">
                                        <i class="bi bi-map"></i> Show Map
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalEdit" data-id="'.$id_geo_region .'">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalHapus" data-id="'.$id_geo_region .'">
                                        <i class="bi bi-x"></i> Hapus
                                    </a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                ';
                $no++;
            }
        }
    }
?>
<script>
    //Creat Javascript Variabel
    var data_count=<?php echo $jml_data; ?>;
    var page_count=<?php echo $JmlHalaman; ?>;
    var curent_page=<?php echo $page; ?>;
    
    //Put Into Pagging Element
    $('#data_count').html('Data : '+data_count+' Record');
    $('#page_info').html('Page '+curent_page+' / '+page_count+'');
    
    //Set Pagging Button
    if(curent_page==1){
        $('#prev_button').prop('disabled', true);
    }else{
        $('#prev_button').prop('disabled', false);
    }
    if(page_count<=curent_page){
        $('#next_button').prop('disabled', true);
    }else{
        $('#next_button').prop('disabled', false);
    }
</script>