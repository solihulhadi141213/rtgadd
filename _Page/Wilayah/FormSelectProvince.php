<div class="row mb-3">
    <div class="col-4">
        <label for="add_province_code">
            <small>Provinsi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
        </label>
    </div>
    <div class="col-8">
        <select name="province_code" id="add_province_code" class="form-control" required>
            <option value="">Pilih</option>
            <?php
                include "../../_Config/Connection.php";
                $query = mysqli_query($Conn, "SELECT DISTINCT province_code, province_name FROM region ORDER BY province_name");
                while ($data = mysqli_fetch_array($query)) {
                    $province_code=$data['province_code'];
                    $province_name=$data['province_name'];
                    echo '<option value="'.$province_code.'">'.$province_name.'</option>';
                }
            ?>
        </select>
    </div>
</div>