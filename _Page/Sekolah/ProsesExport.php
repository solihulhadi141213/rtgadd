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
    $query = mysqli_query($Conn, "SELECT * FROM school");

    // Buat objek spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Data Sekolah");

    // Header
    $headers = ['No', 'Kode Provinsi', 'Nama Provinsi', 'Kode Kab/Kota', 'Nama Kab/Kota', 'Kode Sekolah (NPSN)', 'Nama Sekolah', 'Jenjang'];
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
        $sheet->setCellValue('B'.$row, $province_code);
        $sheet->setCellValue('C'.$row, $province_name);
        $sheet->setCellValue('D'.$row, $district_code);
        $sheet->setCellValue('E'.$row, $district_name);
        $sheet->setCellValue('F'.$row, $data['npsn']);
        $sheet->setCellValue('G'.$row, $data['school_name']);
        $sheet->setCellValue('H'.$row, $data['school_level']);
        $no++;
        $row++;
    }

    // Auto size kolom
    foreach (range('A', 'H') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Output ke browser
    $filename = "export_school_".date('Ymd_His').".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
?>
