<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    $JmlHalaman=0;
    $page=0;
    //Validasi Akses
    if(empty($SessionIdAccess)){
        echo '
            <tr>
                <td colspan="9" class="text-center">
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
            $OrderBy="id_calon_guru";
        }
        //Atur Page
        if(!empty($_POST['page'])){
            $page=$_POST['page'];
            $posisi = ( $page - 1 ) * $batas;
        }else{
            $page="1";
            $posisi = 0;
        }

        // QUERY DASAR DENGAN JOIN
        $base_query = "SELECT cg.*, r.province_name, r.district_name 
                      FROM calon_guru cg 
                      LEFT JOIN region r ON cg.id_region = r.id_region";
        $count_query = "SELECT COUNT(cg.id_calon_guru) as total 
                       FROM calon_guru cg 
                       LEFT JOIN region r ON cg.id_region = r.id_region";

        // FILTER PENCARIAN
        $where_conditions = [];
        
        if(!empty($keyword_by) && !empty($keyword)){
            // Pencarian berdasarkan kolom tertentu
            if(in_array($keyword_by, ['province_name', 'district_name'])) {
                // Untuk kolom dari tabel region
                $where_conditions[] = "r.$keyword_by LIKE '%$keyword%'";
            } else {
                // Untuk kolom dari tabel calon_guru
                $where_conditions[] = "cg.$keyword_by LIKE '%$keyword%'";
            }
        } else if(empty($keyword_by) && !empty($keyword)){
            // Pencarian global di semua kolom
            $where_conditions[] = "(cg.perguruan_tinggi_s1 LIKE '%$keyword%' 
                                OR cg.program_studi_s1 LIKE '%$keyword%' 
                                OR cg.bidang_studi_ppg LIKE '%$keyword%' 
                                OR cg.lptk LIKE '%$keyword%' 
                                OR cg.ppg_blm_diangkat LIKE '%$keyword%'
                                OR r.province_name LIKE '%$keyword%'
                                OR r.district_name LIKE '%$keyword%')";
        }

        // BUILD WHERE CLAUSE
        $where_clause = "";
        if(!empty($where_conditions)){
            $where_clause = " WHERE " . implode(" AND ", $where_conditions);
        }

        // HITUNG TOTAL DATA
        $count_result = mysqli_query($Conn, $count_query . $where_clause);
        $count_data = mysqli_fetch_assoc($count_result);
        $jml_data = $count_data['total'];

        // ORDER BY
        $order_clause = "";
        if(!empty($OrderBy)){
            // Mapping untuk order by yang melibatkan join
            $order_mapping = [
                'province_name' => 'r.province_name',
                'district_name' => 'r.district_name',
                'id_calon_guru' => 'cg.id_calon_guru',
                'perguruan_tinggi_s1' => 'cg.perguruan_tinggi_s1',
                'program_studi_s1' => 'cg.program_studi_s1',
                'bidang_studi_ppg' => 'cg.bidang_studi_ppg',
                'lptk' => 'cg.lptk',
                'ppg_blm_diangkat' => 'cg.ppg_blm_diangkat'
            ];
            
            $order_field = $order_mapping[$OrderBy] ?? 'cg.id_calon_guru';
            $order_clause = " ORDER BY $order_field $ShortBy";
        }

        // LIMIT
        $limit_clause = " LIMIT $posisi, $batas";

        // QUERY FINAL
        $final_query = $base_query . $where_clause . $order_clause . $limit_clause;

        //Mengatur Halaman
        $JmlHalaman = ceil($jml_data/$batas); 
        if(empty($jml_data)){
            echo '
                <tr>
                    <td colspan="9" class="text-center">
                        <small class="text-danger">Tidak Ada Data Yang Ditampilkan!</small>
                    </td>
                </tr>
            ';
        }else{
            $no = 1+$posisi;
            $query = mysqli_query($Conn, $final_query);
            
            while ($data = mysqli_fetch_array($query)) {
                $id_calon_guru          = $data['id_calon_guru'];
                $id_region              = $data['id_region'];
                $perguruan_tinggi_s1    = $data['perguruan_tinggi_s1'];
                $program_studi_s1       = $data['program_studi_s1'];
                $bidang_studi_ppg       = $data['bidang_studi_ppg'];
                $lptk                   = $data['lptk'];
                $ppg_blm_diangkat       = $data['ppg_blm_diangkat'];
                $province_name          = $data['province_name'];
                $district_name          = $data['district_name'];

                //Label Status PPG
                if($ppg_blm_diangkat=="Belum"){
                    $label_status_asn='<span class="badge badge-danger">Belum</span>';
                }else{
                    $label_status_asn='<span class="badge badge-success">Sudah</span>';
                }
                
                echo '
                    <tr>
                        <td><small>'.$no.'</small></td>
                        <td><small>'.$province_name.'</small></td>
                        <td><small>'.$district_name.'</small></td>
                        <td><small>'.$perguruan_tinggi_s1.'</small></td>
                        <td><small>'.$program_studi_s1.'</small></td>
                        <td><small>'.$bidang_studi_ppg.'</small></td>
                        <td><small>'.$lptk.'</small></td>
                        <td><small>'.$label_status_asn.'</small></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                <li class="dropdown-header text-start">
                                    <h6>Option</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_calon_guru .'">
                                        <i class="bi bi-info-circle"></i> Detail
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalEdit" data-id="'.$id_calon_guru .'">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalHapus" data-id="'.$id_calon_guru .'">
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
    var data_count=<?php echo $jml_data; ?>;
    var page_count=<?php echo $JmlHalaman; ?>;
    var curent_page=<?php echo $page; ?>;
    
    $('#data_count').html('Data : '+data_count+' Record');
    $('#page_info').html('Page '+curent_page+' / '+page_count+'');
    
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