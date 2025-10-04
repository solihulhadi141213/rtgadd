<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    //Validasi Data
    if (empty($_POST['id_geo_region'])) {
        // Jika Belum Ada id_geo_region
        echo '
            <tr>
                <td class="text-center text-danger" colspan="4">
                    <small>Tidak Ada Data Yang Dipilih</small>
                </td>
            </tr>
        ';
        exit;
    }

    // Buat Variabel
    $id_geo_region = $_POST['id_geo_region'];
    echo '<input type="hidden" name="id_geo_region" value="'.$id_geo_region.'">';

    //Buka Kode Provinsi
    $province_code     = GetDetailData($Conn, 'geo_region', 'id_geo_region', $id_geo_region, 'province_code');
    if(empty($province_code)){
        echo '
            <tr>
                <td class="text-center text-danger" colspan="4">
                    <small>Kode Provinsi Tidak Ditemukan</small>
                </td>
            </tr>
        ';
        exit;
    }
    
    //Bentuk URL
    $url_endpoint = "https://sipedas.pertanian.go.id/api/wilayah/list_wilayah?thn=2025&lvl=11&pro=$province_code&lv2=12";
    // CURL
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url_endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ));

    $response = curl_exec($curl);

    // Cek error
    if ($response === false) {
        echo '<tr><td colspan="4" class="text-center text-danger"><small>cURL Error: '.curl_error($curl).'</small></td></tr>';
        exit;
    }
    

    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if ($httpcode >= 200 && $httpcode < 300) {
        $data = json_decode($response, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            $no = 1;
            foreach ($data as $kode => $kabupaten) {

                // Validasi ke DB apakah sudah ada
                $id_geo_region = GetDetailData($Conn, 'geo_region','district_code', $kode, 'id_geo_region');
                if(empty($id_geo_region)){
                    $label_data = '<span class="badge badge-danger">NULL</span>';
                }else{
                    $label_data = '<span class="badge badge-success">Ready</span>';
                }

                echo '
                    <tr>
                        <td><small>'.$no.'</small></td>
                        <td>
                            <small>'.$kode.'</small>
                            <input type="hidden" name="district_code[]" value="'.$kode.'">
                        </td>
                        <td>
                            <small>'.$kabupaten.'</small>
                            <input type="hidden" name="district_name[]" value="'.$kabupaten.'">
                        </td>
                        <td>'.$label_data.'</td>
                    </tr>
                ';
                $no++;
            }
        } else {
            echo '<tr><td colspan="4" class="text-center text-danger"><small>Response tidak valid</small></td></tr>';
        }
    } else {
        echo '<tr><td colspan="4" class="text-center text-danger"><small>HTTP Status '.$httpcode.'<br>'.$response.'</small></td></tr>';
    }
?>