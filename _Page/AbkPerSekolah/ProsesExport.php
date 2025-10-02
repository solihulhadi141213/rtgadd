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
    $query = mysqli_query($Conn, "SELECT * FROM position_school ORDER BY id_position_school ASC");

    // Buat objek spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Data Jabatan");

    // Header
    $headers = ['No', 'Kode Provinsi (BPS)', 'Kode Provinsi (DAPODIK)', 'Nama Provinsi', 'Kode Kab/Kota (BPS)', 'Kode Kab/Kota (DAPODIK)', 'Nama Kab/Kota', 'NPSN', 'Sekolah', 'Kode Jabatan', 'Jabatan', 'ABK', 'ASN', 'PPPK 2024', 'Non ASN Sebelum Oktober 2022', 'Non ASN Setelah Oktober 2022', 'Jumlah Guru', 'Kurang Guru', 'Jumlah ASN', 'Kurang ASN'];
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
        $id_school      = $data['id_school'];
        $id_position    = $data['id_position'];
        
        //Buka Nama Provinsi dan Kab/Kota
        $id_region              = GetDetailData($Conn, 'school', 'id_school', $id_school, 'id_region');
        
        $province_code          = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_code');
        $province_code_dapodik  = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_code_dapodik');
        $province_name          = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');
        $district_code          = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_code');
        $district_code_dapodik  = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_code_dapodik');
        $district_name          = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');

        //Buka Sekolah
        $npsn                   = GetDetailData($Conn, 'school', 'id_school', $id_school, 'npsn');
        $school_name            = GetDetailData($Conn, 'school', 'id_school', $id_school, 'school_name');

        //Buka Nama Jabatan
        $position_code      = GetDetailData($Conn, 'position', 'id_position', $id_position, 'position_code');
        $position_name      = GetDetailData($Conn, 'position', 'id_position', $id_position, 'position_name');

        $sheet->setCellValue('A'.$row, $no);
        $sheet->setCellValue('B'.$row, $province_code);
        $sheet->setCellValue('C'.$row, $province_code_dapodik);
        $sheet->setCellValue('D'.$row, $province_name);
        $sheet->setCellValue('E'.$row, $district_code);
        $sheet->setCellValue('F'.$row, $district_code_dapodik);
        $sheet->setCellValue('G'.$row, $district_name);
        $sheet->setCellValue('H'.$row, $npsn);
        $sheet->setCellValue('I'.$row, $school_name);
        $sheet->setCellValue('J'.$row, $position_code);
        $sheet->setCellValue('K'.$row, $position_name);
        $sheet->setCellValue('L'.$row, $data['abk']);
        $sheet->setCellValue('M'.$row, $data['asn']);
        $sheet->setCellValue('N'.$row, $data['PPPK2024']);
        $sheet->setCellValue('O'.$row, $data['NonASN_sblmOkt2022']);
        $sheet->setCellValue('P'.$row, $data['NonASN_stlhOkt2022']);
        $sheet->setCellValue('Q'.$row, $data['JmlGuru']);
        $sheet->setCellValue('R'.$row, $data['KurangGuru']);
        $sheet->setCellValue('S'.$row, $data['JmlASN']);
        $sheet->setCellValue('T'.$row, $data['KrngASN']);
        $no++;
        $row++;
    }

    // Auto size kolom
    foreach (range('A', 'T') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Output ke browser
    $filename = "Export_Position_School_".date('Ymd_His').".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
?>
