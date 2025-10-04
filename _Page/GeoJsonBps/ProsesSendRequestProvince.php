<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    //Validasi Data
    if (empty($_POST['url_endpoint'])) {
        // Jika Belum Ada url_endpoint
        echo '
            <tr>
                <td class="text-center text-danger" colspan="4">
                    <small>URL Harus Diisi Terlebih Dulu</small>
                </td>
            </tr>
        ';
        exit;
    }

    // Buat Variabel
    $url_endpoint = $_POST['url_endpoint'];

    // Mulai Koneksi dengan CURL
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url_endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 10, // kasih timeout supaya tidak nunggu terlalu lama
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_SSL_VERIFYPEER => false, // <-- bypass SSL verify
        CURLOPT_SSL_VERIFYHOST => false, // <-- bypass host check
    ));

    $response = curl_exec($curl);

    // Debug error curl
    if ($response === false) {
        $error_msg = curl_error($curl);
        $error_no  = curl_errno($curl);

        echo "<tr><td colspan='4' class='text-danger text-center'>";
        echo "<b>cURL Error #{$error_no}:</b> {$error_msg}";
        echo "</td></tr>";
    } else {
        // Cek HTTP status code
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($httpcode >= 200 && $httpcode < 300) {
            // Sukses Decode response
            $data  = json_decode($response,true);
            // Cek kalau decode berhasil
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {

                $no = 1;
                foreach ($data as $kode => $provinsi) {
                    $id_geo_region = GetDetailData($Conn, 'geo_region','province_code', $kode, 'id_geo_region');
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
                                <input type="hidden" name="province_code[]" value="'.$kode.'">
                            </td>
                            <td>
                                <small>'.$provinsi.'</small>
                                <input type="hidden" name="province_name[]" value="'.$provinsi.'">
                            </td>
                            <td>'.$label_data.'</td>
                        </tr>
                    ';
                    $no++;
                }
            } else {
                echo '
                    <tr>
                        <td class="text-center text-danger" colspan="4">
                            <small>Response Tidak Valid</small>
                        </td>
                    </tr>
                ';
            }
        } else {
            // Debug response kalau status code bukan 200
            echo "<tr><td colspan='4' class='text-warning text-center'>";
            echo "<b>HTTP Status:</b> {$httpcode}<br>";
            echo "Response:<br><pre>".htmlspecialchars($response)."</pre>";
            echo "</td></tr>";
            echo '
                <tr>
                    <td class="text-center text-danger" colspan="4">
                        <small>
                            HTTP Status: <b>'.$httpcode.'</b><br>
                            esponse:<br><pre>'.htmlspecialchars($response).'</pre>
                        </small>
                    </td>
                </tr>
            ';
        }
    }

    curl_close($curl);
?>
