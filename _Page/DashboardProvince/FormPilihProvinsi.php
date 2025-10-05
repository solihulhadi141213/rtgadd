<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <b class="card-title"><i class="bi bi-pin-map"></i> Pilih Provinsi</b>
            </div>
            <div class="card-body">
                <div class="table table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th><b>No</b></th>
                                <th><b>Provinsi</b></th>
                                <th><b>Kab/Kota</b></th>
                                <th class="text-end"><b>Opsi</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                //Jumlah Provinsi
                                $jumlah_provinsi = mysqli_num_rows(mysqli_query($Conn, "SELECT id_geo_region FROM geo_region WHERE level_region='Province'"));
                                if(empty($jumlah_provinsi)){
                                    echo '
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <small class="text-danger">Tidak Ada Data Provinsi Yang Ditampilkan!</small>
                                            </td>
                                        </tr>
                                    ';
                                }else{
                                    $no=1;
                                    $query_province = mysqli_query($Conn, "SELECT id_geo_region, province_code, province_name FROM geo_region WHERE level_region='Province'");
                                    while ($data_province = mysqli_fetch_array($query_province)) {
                                        $id_geo_region = $data_province['id_geo_region'];
                                        $province_code = $data_province['province_code'];
                                        $province_name = $data_province['province_name'];

                                        //Hitung Jumlah Kab/Kota
                                        $jumlah_district = mysqli_num_rows(mysqli_query($Conn, "SELECT id_geo_region FROM geo_region WHERE level_region='District' AND province_code='$province_code'"));

                                        //Tampilkan
                                        echo '
                                            <tr>
                                                <td><small>'.$no.'</small></td>
                                                <td><small>'.$province_name.'</small></td>
                                                <td><small>'.$jumlah_district.' Record</small></td>
                                                <td class="text-end">
                                                    <a href="index.php?Page=DashboardProvince&province_code='.$province_code.'" class="btn btn-sm btn-outline-primary btn-floating">
                                                        <i class="bi bi-chevron-right"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        ';
                                        $no++;
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <small>
                    Data Count : <?php echo "$jumlah_provinsi Record"; ?>
                </small>
            </div>
        </div>
    </div>
</div>