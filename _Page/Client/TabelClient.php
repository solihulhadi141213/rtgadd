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
                <td colspan="8" class="text-center">
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
            $ShortBy="DESC";
        }
        //OrderBy
        if(!empty($_POST['OrderBy'])){
            $OrderBy=$_POST['OrderBy'];
        }else{
            $OrderBy="id_access ";
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
                $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_access  FROM access WHERE access_client=1"));
            }else{
                $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_access  FROM access WHERE (access_client=1) AND (access_name like '%$keyword%' OR access_email like '%$keyword%' OR access_contact like '%$keyword%')"));
            }
        }else{
            if(empty($keyword)){
                $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_access  FROM access WHERE access_client=1"));
            }else{
                $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_access  FROM access WHERE (access_client=1) AND ($keyword_by like '%$keyword%')"));
            }
        }
        //Mengatur Halaman
        $JmlHalaman = ceil($jml_data/$batas); 
        if(empty($jml_data)){
            echo '
                <tr>
                    <td colspan="8" class="text-center">
                        <small class="text-danger">Tidak Ada Data Yang Ditampilkan!</small>
                    </td>
                </tr>
            ';
        }else{
            $no = 1+$posisi;
            //KONDISI PENGATURAN MASING FILTER
            if(empty($keyword_by)){
                if(empty($keyword)){
                    $query = mysqli_query($Conn, "SELECT*FROM access WHERE access_client=1  ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
                }else{
                    $query = mysqli_query($Conn, "SELECT*FROM access  WHERE (access_client=1) AND (access_name like '%$keyword%' OR access_email like '%$keyword%' OR access_contact like '%$keyword%') ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
                }
            }else{
                if(empty($keyword)){
                    $query = mysqli_query($Conn, "SELECT*FROM access WHERE access_client=1 ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
                }else{
                    $query = mysqli_query($Conn, "SELECT*FROM access  WHERE (access_client=1) AND ($keyword_by like '%$keyword%') ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
                }
            }
            while ($data = mysqli_fetch_array($query)) {
                $id_access          = $data['id_access'];
                $id_access_group    = $data['id_access_group'];
                $access_name        = $data['access_name'];
                $access_email       = $data['access_email'];
                if(empty($data['access_contact'])){
                    $access_contact = "-";
                }else{
                    $kontak = $data['access_contact'];
                    // Ganti 3 digit terakhir dengan ***
                    if(strlen($kontak) > 3){
                        $access_contact = substr($kontak, 0, -3) . '***';
                    }else{
                        // Jika panjang nomor <= 3, sembunyikan semuanya
                        $access_contact = str_repeat('*', strlen($kontak));
                    }
                }
                
                //Buka access_client
                $level          = GetDetailData($Conn, 'access_client', 'id_access', $id_access, 'level');
                $id_region      = GetDetailData($Conn, 'access_client', 'id_access', $id_access, 'id_region');

                //Buka region
                if(!empty($id_region)){
                    $category       = GetDetailData($Conn, 'region', 'id_region', $id_region, 'category');
                    $province_name  = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');
                    $district_name  = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');
                    if(empty($district_name)){
                        $district_name  = "-";
                    }
                }else{
                    $category       = "-";
                    $province_name  = "-";
                    $district_name  = "-";
                }

                //Routing Level Label
                $level_label='<span class="badge bg-danger">None</span>';
                if($level=="National"){
                    $level_label='<span class="badge bg-primary">Nasional</span>';
                }
                if($level=="Province"){
                    $level_label='<span class="badge bg-info">Provinsi</span>';
                }
                if($level=="District"){
                    $level_label='<span class="badge bg-success">Kab/Kota</span>';
                }
               
                echo '
                    <tr>
                        <td><small>'.$no.'</small></td>
                        <td>
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_access .'">
                                <small>'.$access_name.'</small>
                            </a>
                        </td>
                        <td><small>'.$access_contact.'</small></td>
                        <td><small>'.$access_email.'</small></td>
                        <td><small>'.$level_label.'</small></td>
                        <td><small>'.$province_name.'</small></td>
                        <td><small>'.$district_name.'</small></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                <li class="dropdown-header text-start">
                                    <h6>Option</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_access .'">
                                        <i class="bi bi-info-circle"></i> Detail
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalEditAkses" data-id="'.$id_access .'">
                                        <i class="bi bi-pencil"></i> Ubah Identitas
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalEditLevel" data-id="'.$id_access .'">
                                        <i class="bi bi-list-check"></i> Ubah Level
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalUbahPassword" data-id="'.$id_access .'">
                                        <i class="bi bi-key"></i> Ubah Password
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalUbahFoto" data-id="'.$id_access .'">
                                        <i class="bi bi-image"></i> Ubah Foto
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalHapus" data-id="'.$id_access .'">
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
    var page_count=<?php echo $JmlHalaman; ?>;
    var curent_page=<?php echo $page; ?>;
    
    //Put Into Pagging Element
    $('#page_info').html('Page '+curent_page+' Of '+page_count+'');
    
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