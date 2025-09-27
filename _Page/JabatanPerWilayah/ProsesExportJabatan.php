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
    $query = mysqli_query($Conn, "SELECT * FROM position_region ORDER BY province, regency, department");

    // Buat objek spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Data Jabatan");

    // Header
    $headers = ['No', 'Provinsi', 'Kab/Kota', 'Jabatan', 'ABK', 'ASN-Negeri', 'ASN-Swasta', 'Kurang ASN'];
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
        $sheet->setCellValue('A'.$row, $no);
        $sheet->setCellValue('B'.$row, $data['province']);
        $sheet->setCellValue('C'.$row, $data['regency']);
        $sheet->setCellValue('D'.$row, $data['department']);
        $sheet->setCellValue('E'.$row, $data['workload']);
        $sheet->setCellValue('F'.$row, $data['officials_public']);
        $sheet->setCellValue('G'.$row, $data['officials_private']);
        $sheet->setCellValue('H'.$row, $data['manpower_gap']);
        $no++;
        $row++;
    }

    // Auto size kolom
    foreach (range('A', 'H') as $columnID) {
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
