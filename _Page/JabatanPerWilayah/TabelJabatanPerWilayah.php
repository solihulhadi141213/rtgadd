<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");
    $JmlHalaman=0;
    $page=0;

    if(empty($SessionIdAccess)){
        echo '
            <tr>
                <td colspan="16" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
        ';
    }else{
        //Keyword_by
        $keyword_by = !empty($_POST['keyword_by']) ? $_POST['keyword_by'] : "";
        //keyword
        $keyword    = !empty($_POST['keyword']) ? $_POST['keyword'] : "";
        //batas
        $batas      = !empty($_POST['batas']) ? $_POST['batas'] : "10";
        //ShortBy
        $ShortBy    = !empty($_POST['ShortBy']) ? $_POST['ShortBy'] : "DESC";
        //OrderBy
        $OrderBy    = !empty($_POST['OrderBy']) ? $_POST['OrderBy'] : "pr.id_position_region";
        //Atur Page
        if(!empty($_POST['page'])){
            $page=$_POST['page'];
            $posisi = ( $page - 1 ) * $batas;
        }else{
            $page="1";
            $posisi = 0;
        }

        //Mapping kolom agar aman
        $allowed_columns = [
            "province_name" => "r.province_name",
            "district_name" => "r.district_name",
            "position_name" => "p.position_name",
            "abk" => "pr.abk",
            "asn" => "pr.asn",
            "jumlah_guru" => "pr.jumlah_guru",
            "kurang_guru" => "pr.kurang_guru",
            "jumlah_asn" => "pr.jumlah_asn",
            "kurang_asn" => "pr.kurang_asn",
            "id_position_region" => "pr.id_position_region"
        ];

        if(array_key_exists($OrderBy, $allowed_columns)){
            $OrderBy = $allowed_columns[$OrderBy];
        }else{
            $OrderBy = "pr.id_position_region";
        }
        if(array_key_exists($keyword_by, $allowed_columns)){
            $keyword_by = $allowed_columns[$keyword_by];
        }else{
            $keyword_by = "";
        }

        //Query dasar join
        $baseQuery = "
            FROM position_region pr
            LEFT JOIN region r ON pr.id_region = r.id_region
            LEFT JOIN position p ON pr.id_position = p.id_position
        ";

        //Filter
        $where = "";
        if(!empty($keyword)){
            if(empty($keyword_by)){
                $where = "WHERE r.province_name LIKE '%$keyword%' 
                          OR r.district_name LIKE '%$keyword%' 
                          OR p.position_name LIKE '%$keyword%'";
            }else{
                $where = "WHERE $keyword_by LIKE '%$keyword%'";
            }
        }

        //Hitung total
        $sqlCount = "SELECT COUNT(pr.id_position_region) as jml ".$baseQuery." ".$where;
        $resCount = mysqli_query($Conn, $sqlCount);
        $rowCount = mysqli_fetch_assoc($resCount);
        $jml_data = $rowCount['jml'];

        $JmlHalaman = ceil($jml_data/$batas); 
        if(empty($jml_data)){
            echo '
                <tr>
                    <td colspan="16" class="text-center">
                        <small class="text-danger">Tidak Ada Data Yang Ditampilkan!</small>
                    </td>
                </tr>
            ';
        }else{
            $no = 1+$posisi;
            $sqlData = "SELECT pr.*, r.province_name, r.district_name, p.position_name 
                        ".$baseQuery." 
                        ".$where." 
                        ORDER BY $OrderBy $ShortBy 
                        LIMIT $posisi, $batas";
            $query = mysqli_query($Conn, $sqlData);

            while ($data = mysqli_fetch_array($query)) {
                $id_position_region = $data['id_position_region'];
                $province_name = $data['province_name'];
                $district_name = $data['district_name'];
                $position_name = $data['position_name'];
                $abk = $data['abk'];
                $asn = $data['asn'];
                $asn_di_negeri = $data['asn_di_negeri'];
                $asn_di_swasta = $data['asn_di_swasta'];
                $NonASN_sblmOkt2022 = $data['NonASN_sblmOkt2022'];
                $NonASN_stlhOkt2022 = $data['NonASN_stlhOkt2022'];
                $pppk2024 = $data['pppk2024'];
                $jumlah_guru = $data['jumlah_guru'];
                $kurang_guru = $data['kurang_guru'];
                $jumlah_asn = $data['jumlah_asn'];
                $kurang_asn = $data['kurang_asn'];

                echo '
                    <tr>
                        <td><small>'.$no.'</small></td>
                        <td><small>'.$province_name.'</small></td>
                        <td><small>'.$district_name.'</small></td>
                        <td><small>'.$position_name.'</small></td>
                        <td><small>'.$abk.'</small></td>
                        <td><small>'.$asn.'</small></td>
                        <td><small>'.$asn_di_negeri.'</small></td>
                        <td><small>'.$asn_di_swasta.'</small></td>
                        <td><small>'.$pppk2024.'</small></td>
                        <td><small>'.$NonASN_sblmOkt2022.'</small></td>
                        <td><small>'.$NonASN_stlhOkt2022.'</small></td>
                        <td><small>'.$jumlah_guru.'</small></td>
                        <td><small>'.$kurang_guru.'</small></td>
                        <td><small>'.$jumlah_asn.'</small></td>
                        <td><small>'.$kurang_asn.'</small></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                <li class="dropdown-header text-start">
                                    <h6>Option</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalDetailJabatan" data-id="'.$id_position_region .'">
                                        <i class="bi bi-info-circle"></i> Detail
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalEditJabatan" data-id="'.$id_position_region .'">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalHapusJabatan" data-id="'.$id_position_region .'">
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
    var page_count=<?php echo $JmlHalaman; ?>;
    var curent_page=<?php echo $page; ?>;
    $('#page_info').html('Page '+curent_page+' Of '+page_count+'');
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
