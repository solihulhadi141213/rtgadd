<div class="row mb-3">
    <div class="col-md-4">
        <label for="district_edit">
            <small>Kab/Kota <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
        </label>
    </div>
    <div class="col-md-8">
        <select name="district" id="district_edit" class="form-control" required>
            <option value="">Pilih</option>
            <?php
                //Koneksi
                include "../../_Config/Connection.php";
                include "../../_Config/GlobalFunction.php";
                
                //Apabila ada 'province_id' Tampilkan Provinsi
                if(!empty($_POST['province_id'])){

                    //Buat variabel province_id
                    $province_id=$_POST['province_id'];

                    //Buka province_code
                    $province_code=GetDetailData($Conn, 'region', 'id_region', $province_id, 'province_code');

                    //Tampilkan District berdasarkan province_code
                    $query = mysqli_query($Conn, "SELECT id_region, district_name FROM region WHERE category='District' AND province_code='$province_code' ORDER BY district_name ASC");
                    while ($data = mysqli_fetch_array($query)) {
                        $id_region = $data['id_region'];
                        $district_name = $data['district_name'];
                        echo '<option value="'.$id_region.'">'.$district_name.'</option>';
                    }
                }
            ?>
        </select>
    </div>
</div>