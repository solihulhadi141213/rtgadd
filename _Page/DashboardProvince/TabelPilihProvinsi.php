<?php
    // Koneksi 
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    // Inisialisasi
    $jumlah_total_baris = 0;

    // Ambil data summary province (SUPER CEPAT)
    $query = "
        SELECT 
            province_code,
            province_name,
            jumlah_kabkota,
            jumlah_sekolah,
            total_kebutuhan_guru
        FROM summary_province
        ORDER BY total_kebutuhan_guru DESC
    ";

    $result = mysqli_query($Conn, $query);

    if (!$result) {
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Error: ' . mysqli_error($Conn) . '</small>
                </td>
            </tr>
        ';
        exit;
    }

    if (mysqli_num_rows($result) === 0) {
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Tidak Ada Data Summary Provinsi!</small>
                </td>
            </tr>
        ';
        exit;
    }

    $no = 1;
    $buffer = '';

    while ($row = mysqli_fetch_assoc($result)) {

        $province_code  = htmlspecialchars($row['province_code']);
        $province_name  = htmlspecialchars($row['province_name']);
        $jumlah_kabkota = (int)$row['jumlah_kabkota'];
        $jumlah_sekolah = (int)$row['jumlah_sekolah'];
        $total_kebutuhan_guru = (int)$row['total_kebutuhan_guru'];

        // Formatting angka
        $jumlah_sekolah_format = $jumlah_sekolah > 1000 ?
            number_format($jumlah_sekolah, 0, ',', '.') :
            $jumlah_sekolah;

        $total_kebutuhan_guru_format = $total_kebutuhan_guru > 1000 ?
            number_format($total_kebutuhan_guru, 0, ',', '.') :
            $total_kebutuhan_guru;

        // Jika provinsi bukan daerah sasaran
        if ($jumlah_sekolah == 0 && $total_kebutuhan_guru == 0) {
            $jumlah_sekolah_format = "Bukan Daerah Sasaran";
            $total_kebutuhan_guru_format = "Bukan Daerah Sasaran";
        }

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

    echo $buffer;

    // Footer jumlah baris
    $jumlah_total_baris = $no - 1;

    echo '
        <script>
            $("#put_jumlah_provinsi").html(' . json_encode($jumlah_total_baris . " Record") . ');
        </script>
    ';
?>
