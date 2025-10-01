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
    $query = mysqli_query($Conn, "SELECT * FROM organization");

    // Buat objek spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Data Instansi");

    // Header
    $headers = ['No', 'Level', 'Kode Provinsi', 'Nama Provinsi', 'Kode Kab/Kota', 'Nama Kab/Kota', 'Kode Instansi', 'Nama Instansi'];
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

        //Buka data region
        $province_code  = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_code');
        $province_name  = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');
        $district_code  = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_code');
        $district_name  = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');

        //Tampilkan Data
        $sheet->setCellValue('A'.$row, $no);
        $sheet->setCellValue('B'.$row, $data['organization_level']);
        $sheet->setCellValue('C'.$row, $province_code);
        $sheet->setCellValue('D'.$row, $province_name);
        $sheet->setCellValue('E'.$row, $district_code);
        $sheet->setCellValue('F'.$row, $district_name);
        $sheet->setCellValue('G'.$row, $data['organization_code']);
        $sheet->setCellValue('H'.$row, $data['organization_name']);
        $no++;
        $row++;
    }

    // Auto size kolom
    foreach (range('A', 'H') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Output ke browser
    $filename = "export_organization_".date('Ymd_His').".xlsx"; //Nama File
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
?>
