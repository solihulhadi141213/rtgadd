<?php
    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Inisiasi Variabel id_position_region
    $jumlah_data="";

    //Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        echo '
            <div class="alert alert-danger">
                <small>
                    Sesi akses sudah berakhir. Silahkan <b>login</b> ulang!
                </small>
            </div>
        ';
        exit;
    }
    
    //Hitung Jumlah Data
    $jumlah_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_position_region FROM position_region "));

    if(empty($jumlah_data)){
         echo '
            <div class="alert alert-danger">
                <small>
                    Tidak ada data jabatan per wilayah yang bisa di export!
                </small>
            </div>
        ';
        exit;
    }

    echo '
        <div class="alert alert-warning">
            <small>
                <b>Keterangan : </b> <br>
                Terdapat <b>'.$jumlah_data.' record</b> data informasi jabatan per wilayah. Semakin besar data maka sistem membutuhkan waktu lebih lama untuk melakukan proses export.
            </small>
        </div>
    ';
?>

<script>
    // Ambil nilai dari PHP sebagai string aman
    var jumlah_data = <?php echo json_encode($jumlah_data); ?>;

    if(jumlah_data === "" || jumlah_data === null){
        // Disable tombol
        $('#ButtonExportJabatan').prop('disabled', true);
    }else{
        // Enable tombol
        $('#ButtonExportJabatan').prop('disabled', false);
    }
</script>