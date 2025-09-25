<?php
    //Koneksi
    date_default_timezone_set('Asia/Jakarta');
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    if(empty($SessionIdAccess)){
        echo '<div class="row">';
        echo '  <div class="col-md-12 mb-3 text-center text-danger">';
        echo '      Sesi Akses Sudah Berakhir, Silahkan Login Ulang';
        echo '  </div>';
        echo '</div>';
    }else{
        if(empty($_POST['id_help'])){
            echo '<div class="row">';
            echo '  <div class="col-md-12 mb-3 text-center text-danger">';
            echo '      ID Konten Bantuan Tidak Boleh Kosong';
            echo '  </div>';
            echo '</div>';
        }else{
            $id_help=$_POST['id_help'];
            //Bersihkan Variabel
            $id_help=validateAndSanitizeInput($id_help);
            //Validasi Keberadaan Data
            $id_help=GetDetailData($Conn,'help','id_help',$id_help,'id_help');
            if(empty($id_help)){
                echo '<div class="row">';
                echo '  <div class="col-md-12 mb-3 text-center text-danger">';
                echo '      ID Bantuan Yang Anda Gunakan Tidak Valid!';
                echo '  </div>';
                echo '</div>';
            }else{
                $author=GetDetailData($Conn,'help','id_help',$id_help,'author');
                $judul=GetDetailData($Conn,'help','id_help',$id_help,'judul');
                $kategori=GetDetailData($Conn,'help','id_help',$id_help,'kategori');
                $deskripsi=GetDetailData($Conn,'help','id_help',$id_help,'deskripsi');
                $datetime_creat=GetDetailData($Conn,'help','id_help',$id_help,'datetime_creat');
                $datetime_update=GetDetailData($Conn,'help','id_help',$id_help,'datetime_update');
                $status=GetDetailData($Conn,'help','id_help',$id_help,'status');
                //Format Waktu
                $strtotime1=strtotime($datetime_creat);
                $strtotime2=strtotime($datetime_update);
                $TanggalCreatFormat=date('d/m/Y H:i:s T',$strtotime1);
                $TanggalUpdateFormat=date('d/m/Y H:i:s T',$strtotime2);
?>
    <div class="row mb-3">
        <div class="col col-md-4">Author</div>
        <div class="col col-md-8">
            <small class="credit text-grayish">
                <?php echo "$author"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col col-md-4">Judul</div>
        <div class="col col-md-8">
            <small class="credit text-grayish">
                <?php echo "$judul"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col col-md-4">Kategori</div>
        <div class="col col-md-8">
            <small class="credit text-grayish">
                <?php echo "$kategori"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col col-md-4">Tanggal Post</div>
        <div class="col col-md-8">
            <small class="credit text-grayish">
                <?php echo "$TanggalCreatFormat"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col col-md-4">Tanggal Update</div>
        <div class="col col-md-8">
            <small class="credit text-grayish">
                <?php echo "$TanggalUpdateFormat"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3 border-1 border-bottom">
        <div class="col col-md-4 mb-3">Status</div>
        <div class="col col-md-8 mb-3">
            <?php 
                if($status=="Publish"){
                    echo '<badge class="badge badge-success">'; 
                    echo '  Publish';
                    echo '</badge>'; 
                }else{
                    echo '<badge class="badge badge-warning">'; 
                    echo '  Draft';
                    echo '</badge>'; 
                }
            ?>
        </div>
    </div>
    <div class="row mb-3 mt-5">
        <div class="col col-md-12 text-center">
            Untuk merubah/edit data bantuan ini, anda akan diarahkan pada halaman form khusus.
        </div>
    </div>
    <div class="col-md-12 mb-3 text-center">
        <small class="credit text-info">
            Apakah anda ingin melanjutkan?
        </small>
    </div>
    <div class="col-md-12 mb-3 text-center">
        <a href="index.php?Page=Help&Sub=EditHelp&id=<?php echo "$id_help"; ?>" class="btn btn-primary btn-rounded">
            Lanjutkan <i class="bi bi-chevron-right"></i>
        </a>
    </div>
<?php 
            }
        }
    } 
?>