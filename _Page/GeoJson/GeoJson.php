<div class="pagetitle">
    <h1>
        <a href="">
            <i class="bi bi-globe"></i> GeoJson</a>
        </a>
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active">GeoJson</li>
        </ol>
    </nav>
</div>
<section class="section dashboard">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <small>
                    Berikut ini adalah halaman pengelolaan data referensi GeoJson. 
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2># Daftar GeoJson Provinsi dan Kabupaten Indonesia</h2>
                </div>
                <div class="card-body">
                    <div class="table table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th><b>No</b></th>
                                    <th><b>Kode Provinsi</b></th>
                                    <th><b>Nama Provinsi</b></th>
                                    <th><b>Kode Kab/Kota</b></th>
                                    <th><b>Nama Kab/Kota</b></th>
                                    <th><b>Opsi</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $directory_json_prov = "GeoJson/provinsi.json";
                                    $directory_json_kab  = "GeoJson/kabupaten.json";

                                    if (!file_exists($directory_json_prov)) {
                                        die("File JSON Provinsi tidak ditemukan");
                                    }
                                    if (!file_exists($directory_json_kab)) {
                                        die("File JSON Kabupaten tidak ditemukan");
                                    }

                                    $jsonProvString = file_get_contents($directory_json_prov);
                                    $jsonKabString  = file_get_contents($directory_json_kab);

                                    $dataProv = json_decode($jsonProvString, true);
                                    $dataKab  = json_decode($jsonKabString, true);

                                    if (json_last_error() !== JSON_ERROR_NONE) {
                                        die("Error parsing JSON: " . json_last_error_msg());
                                    }

                                    $featuresProv = $dataProv['features'] ?? [];
                                    $featuresKab  = $dataKab['features'] ?? [];

                                    $noProv=1;
                                    foreach ($featuresProv as $prov) {
                                        $kode_provinsi  = $prov['properties']['KODE_PROV'] ?? '-';
                                        $nama_provinsi  = $prov['properties']['PROVINSI'] ?? '-';

                                        echo '
                                            <tr>
                                                <td><b>'.$noProv.'</b></td>
                                                <td><b>'.$kode_provinsi.'</b></td>
                                                <td colspan="3"><b>'.$nama_provinsi.'</b></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary show_child_row_data" data-prov="'.$kode_provinsi.'">
                                                        <i class="bi bi-chevron-down"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#ModalShowMapProvinsi" data-id="'.$kode_provinsi .'">
                                                        <i class="bi bi-globe"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#ModalShowMapProvinsiAndAkbupaten" data-id="'.$kode_provinsi .'">
                                                        <i class="bi bi-map"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        ';

                                        // Baris kabupaten disembunyikan dulu
                                        $noKab=1;
                                        foreach ($featuresKab as $kab) {
                                            $OBJECTID  = $kab['properties']['OBJECTID'] ?? '';
                                            $kdppum  = $kab['properties']['KDPPUM'] ?? '';
                                            $nama_kabupaten = $kab['properties']['WADMKK'] ?? '-';
                                            $nama_prov_kab  = $kab['properties']['WADMPR'] ?? '-';

                                            if ($kdppum == $kode_provinsi) {
                                                echo '
                                                    <tr class="child-row child-of-'.$kode_provinsi.'" style="display:none;">
                                                        <td></td>
                                                        <td>'.$kdppum.'</td>
                                                        <td>'.$nama_prov_kab.'</td>
                                                        <td>'.$OBJECTID.'</td>
                                                        <td>'.$nama_kabupaten.'</td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#ModalShowMapKabupaten" data-id="'.$OBJECTID .'">
                                                                <i class="bi bi-map"></i>
                                                            </button>
                                                        </td>
                                                    </tr>';
                                                $noKab++;
                                            }
                                        }
                                        $noProv++;
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>