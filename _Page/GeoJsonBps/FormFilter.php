<?php
    include "../../_Config/Connection.php";
    if(empty($_POST['KeywordBy'])){
        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
    }else{
        $keyword_by=$_POST['KeywordBy'];
        if($keyword_by=="level_region"){
            echo '
                <select type="text" name="keyword" id="keyword" class="form-control">
                    <option value="">Pilih</option>
                    <option value="Province">Provinsi</option>
                    <option value="District">Kab/Kota</option>
                </select>
            ';
        }else{
            echo '<input type="text" name="keyword" id="keyword" class="form-control">';
        }
    }
?>