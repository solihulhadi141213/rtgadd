<div class="row mb-3">
    <div class="col-md-4">
        <label for="province_edit">
            <small>Provinsi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
        </label>
    </div>
    <div class="col-md-8">
        <select name="province" id="province_edit" class="form-control" required>
            <option value="">Pilih</option>
            <?php
                //Koneksi
                include "../../_Config/Connection.php";
                //Tampilkan Provinsi
                $query = mysqli_query($Conn, "SELECT id_region, province_name FROM region WHERE category='Province'  ORDER BY province_name ASC");
                while ($data = mysqli_fetch_array($query)) {
                    $id_region = $data['id_region'];
                    $province_name = $data['province_name'];
                    echo '<option value="'.$id_region.'">'.$province_name.'</option>';
                }
            ?>
        </select>
    </div>
</div>