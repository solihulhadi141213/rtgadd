<?php
    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

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

    // Pustaka PhpSpreadsheet
    require '../../vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Style\Alignment;

    // Query data dari tabel
    $query = mysqli_query($Conn, "SELECT * FROM position_region ORDER BY id_position_region ASC");

    // Buat objek spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Data Jabatan");

    // Header
    $headers = ['No', 'Provinsi', 'Kab/Kota', 'Jabatan', 'ABK', 'ASN', 'ASN-Negeri', 'ASN-Swasta', 'Non ASN Sebelum Oktober 2022', 'Non ASN Setelah Oktober 2022', 'PPPK 2024', 'Jumlah Guru', 'Kurang Guru', 'Jumlah ASN', 'Kurang ASN'];
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col.'1', $header);
        // Bold & Center
        $sheet->getStyle($col.'1')->getFont()->setBold(true);
        $sheet->getStyle($col.'1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $col++;
    }

    // Data isi
    $no = 1;
    $row = 2;
    while ($data = mysqli_fetch_assoc($query)) {
        $id_region      = $data['id_region'];
        $id_position    = $data['id_position'];
        
        //Buka Nama Provinsi dan Kab/Kota
        $province_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');
        $district_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');

        //Buka Nama Jabatan
        $position_name      = GetDetailData($Conn, 'position', 'id_position', $id_position, 'position_name');

        $sheet->setCellValue('A'.$row, $no);
        $sheet->setCellValue('B'.$row, $province_name);
        $sheet->setCellValue('C'.$row, $district_name);
        $sheet->setCellValue('D'.$row, $position_name);
        $sheet->setCellValue('E'.$row, $data['abk']);
        $sheet->setCellValue('F'.$row, $data['asn']);
        $sheet->setCellValue('G'.$row, $data['asn_di_negeri']);
        $sheet->setCellValue('H'.$row, $data['asn_di_swasta']);
        $sheet->setCellValue('I'.$row, $data['NonASN_sblmOkt2022']);
        $sheet->setCellValue('J'.$row, $data['NonASN_stlhOkt2022']);
        $sheet->setCellValue('K'.$row, $data['pppk2024']);
        $sheet->setCellValue('L'.$row, $data['jumlah_guru']);
        $sheet->setCellValue('M'.$row, $data['kurang_guru']);
        $sheet->setCellValue('N'.$row, $data['jumlah_asn']);
        $sheet->setCellValue('O'.$row, $data['kurang_asn']);
        $no++;
        $row++;
    }

    // Auto size kolom
    foreach (range('A', 'O') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Output ke browser
    $filename = "Export_Position_Region_".date('Ymd_His').".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
?>
