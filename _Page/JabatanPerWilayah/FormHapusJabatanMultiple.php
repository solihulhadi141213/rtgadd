<?php
    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Inisiasi Variabel Pertama kali
    $jumlah_data=0;
    //Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        echo '
            <tr>
                <td class="text-center" colspan="4">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan login ulang.</small>
                </td>
            </tr>
        ';
    }else{
        //Tangkap id_position_region
        if(empty($_POST['id_position_region'])){
            echo '
                <tr>
                    <td class="text-center" colspan="4">
                        <small class="text-danger">Tidak ada data yang dipilih.</small>
                    </td>
                </tr>
            ';
        }else{
            if(empty(count($_POST['id_position_region']))){
                echo '
                    <tr>
                        <td class="text-center" colspan="4">
                            <small class="text-danger">Tidak ada data yang dipilih. Silahkan pilih data yang ingin anda hapus.</small>
                        </td>
                    </tr>
                ';
            }else{
                $jumlah_data=count($_POST['id_position_region']);
                $no=1;
                foreach ($_POST['id_position_region'] as $id_position_region) {
                    //Buka Data Siswa
                    $Qry = $Conn->prepare("SELECT * FROM position_region WHERE id_position_region = ?");
                    $Qry->bind_param("s", $id_position_region);
                    if (!$Qry->execute()) {
                        $error=$Conn->error;
                        echo '
                            <tr>
                                <td class="text-center" colspan="4">
                                    <small class="text-danger">Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
                                </td>
                            </tr>
                        ';
                    }else{
                        $Result = $Qry->get_result();
                        $Data = $Result->fetch_assoc();
                        $Qry->close();

                        //Buat Variabel
                        $province   = $Data['province'];
                        $regency    = $Data['regency'];
                        $department = $Data['department'];

                        //Tampilkan Pada Baris Tabel
                        echo '
                            <tr>
                                <td class="text-center">
                                    <small>'.$no.'</small>
                                    <input type="hidden" name="id_position_region[]" value="'.$id_position_region .'">
                                </td>
                                <td><small>'.$province.'</small></td>
                                <td><small>'.$regency.'</small></td>
                                <td><small>'.$department.'</small></td>
                            </tr>
                        ';
                    }
                    $no++;
                }
            }
        }
    }
?>

<script>
    // Ambil nilai dari PHP (langsung angka, bukan string)
    var jumlah_data = <?php echo (int)$jumlah_data; ?>;

    if(jumlah_data > 0){
        $('#NotifikasiHapusJabatanMultiple').html(
            '<div class="alert alert-warning"><small>Apakah anda yakin akan menghapus data tersebut?</small></div>'
        );
        // Enable tombol
        $('#ButtonHapusJabatanMultiple').prop('disabled', false);
    }else{
        $('#NotifikasiHapusJabatanMultiple').html('');
        // Disable tombol
        $('#ButtonHapusJabatanMultiple').prop('disabled', true);
    }
</script>
            