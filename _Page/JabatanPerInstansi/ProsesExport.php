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
    $query = mysqli_query($Conn, "SELECT * FROM position_organization");

    // Buat objek spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Data Instansi");

    // Header
    $headers = ['No', 'Kode Provinsi (BPS)', 'Kode Provinsi (DAPODIK)', 'Nama Provinsi', 'Kode Kab/Kota (BPS)', 'Kode Kab/Kota (DAPODIK)', 'Nama Kab/Kota', 'Kode Instansi', 'Nama Instansi', 'Kode Jabatan', 'Nama Jabatan', 'Formasi PPG'];
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
        //Buat variabel data
        $id_region=$data['id_region'];
        $id_position=$data['id_position'];
        $id_organization=$data['id_organization'];
        $category=$data['category'];
        $formasi_ppg=$data['formasi_ppg'];

        //Buka data region
        $province_code          = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_code');
        $province_code_dapodik  = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_code_dapodik');
        $province_name          = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');
        $district_code          = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_code');
        $district_code_dapodik  = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_code_dapodik');
        $district_name          = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');

        //Buka Position
        $position_code          = GetDetailData($Conn, 'position', 'id_position', $id_position, 'position_code');
        $position_name          = GetDetailData($Conn, 'position', 'id_position', $id_position, 'position_name');

        //Buka Organization
        $organization_code      = GetDetailData($Conn, 'organization', 'id_organization', $id_organization, 'organization_code');
        $organization_name      = GetDetailData($Conn, 'organization', 'id_organization', $id_organization, 'organization_name');

        //Tampilkan Data
        $sheet->setCellValue('A'.$row, $no);
        $sheet->setCellValue('B'.$row, $province_code);
        $sheet->setCellValue('C'.$row, $province_code_dapodik);
        $sheet->setCellValue('D'.$row, $province_name);
        $sheet->setCellValue('E'.$row, $district_code);
        $sheet->setCellValue('F'.$row, $district_code_dapodik);
        $sheet->setCellValue('G'.$row, $district_name);
        $sheet->setCellValue('H'.$row, $position_code);
        $sheet->setCellValue('I'.$row, $position_name);
        $sheet->setCellValue('J'.$row, $organization_code);
        $sheet->setCellValue('K'.$row, $organization_name);
        $sheet->setCellValue('L'.$row, $formasi_ppg);
        $no++;
        $row++;
    }

    // Auto size kolom
    foreach (range('A', 'L') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Output ke browser
    $filename = "export_POSITION_BY_organization_".date('Ymd_His').".xlsx"; //Nama File
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
?>
