<?php
    //Koneksi 
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    //Inisialisasi Jumlah Data
    $jumlah_total_baris= 0;
    // OPTIMIZED QUERY - Mengurangi JOIN yang tidak diperlukan
    $query = "
        SELECT 
            gr.province_code,
            gr.province_name,
            -- Hitung kab/kota dari geo_region langsung
            (
                SELECT COUNT(*) 
                FROM geo_region gd 
                WHERE gd.province_code = gr.province_code 
                AND gd.level_region = 'District'
            ) as jumlah_kabkota,
            -- Hitung sekolah dari tabel school melalui region
            (
                SELECT COUNT(DISTINCT s.id_school)
                FROM geo_region gd
                INNER JOIN region r ON r.district_code = gd.district_code AND r.category = 'District'
                INNER JOIN school s ON s.id_region = r.id_region
                WHERE gd.province_code = gr.province_code 
                AND gd.level_region = 'District'
            ) as jumlah_sekolah,
            -- Hitung kebutuhan guru dari position_school
            COALESCE((
                SELECT SUM(ps.KurangGuru)
                FROM geo_region gd
                INNER JOIN region r ON r.district_code = gd.district_code AND r.category = 'District'
                INNER JOIN school s ON s.id_region = r.id_region
                INNER JOIN position_school ps ON ps.id_school = s.id_school
                WHERE gd.province_code = gr.province_code 
                AND gd.level_region = 'District'
            ), 0) as total_kebutuhan_guru
        FROM geo_region gr
        WHERE gr.level_region = 'Province'
        ORDER BY total_kebutuhan_guru DESC
    ";
    
    $result = mysqli_query($Conn, $query);
    
    if(!$result) {
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Error: ' . mysqli_error($Conn) . '</small>
                </td>
            </tr>
        ';
    } elseif(mysqli_num_rows($result) === 0) {
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Tidak Ada Data Provinsi Yang Ditampilkan!</small>
                </td>
            </tr>
        ';
    } else {
        $no = 1;
        $buffer = ''; // Buffer untuk menampung output
        
        while ($data_province = mysqli_fetch_assoc($result)) {
            $province_code = htmlspecialchars($data_province['province_code']);
            $province_name = htmlspecialchars($data_province['province_name']);
            $jumlah_kabkota = (int)$data_province['jumlah_kabkota'];
            $jumlah_sekolah = (int)$data_province['jumlah_sekolah'];
            $total_kebutuhan_guru = (int)$data_province['total_kebutuhan_guru'];
            
            // Format angka hanya jika perlu ditampilkan
            $jumlah_sekolah_format = $jumlah_sekolah > 1000 ? 
                number_format($jumlah_sekolah, 0, ',', '.') : $jumlah_sekolah;
            $total_kebutuhan_guru_format = $total_kebutuhan_guru > 1000 ? 
                number_format($total_kebutuhan_guru, 0, ',', '.') : $total_kebutuhan_guru;
            
            $buffer .= '
                <tr>
                    <td><small>' . $no . '</small></td>
                    <td><small>' . $province_name . '</small></td>
                    <td><small>' . $jumlah_kabkota . ' Record</small></td>
                    <td><small>' . $jumlah_sekolah_format . '</small></td>
                    <td><small>' . $total_kebutuhan_guru_format . '</small></td>
                    <td class="text-end">
                        <a href="index.php?Page=DashboardProvince&province_code=' . $province_code . '" 
                        class="btn btn-sm btn-outline-primary btn-floating"
                        title="Lihat Detail">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </td>
                </tr>
            ';
            $no++;
        }
        
        echo $buffer; // Output semua sekaligus
    }
    
    // Bebaskan memory
    if(isset($result)) {
        mysqli_free_result($result);
    }

    $jumlah_total_baris= $no;

    //Menampilkan Jquery
    $footer_title = ''.$jumlah_total_baris.' Record';
    echo '
        <script>
            $("#put_jumlah_provinsi").html(' . json_encode($footer_title) . ');
        </script>
    ';
?>