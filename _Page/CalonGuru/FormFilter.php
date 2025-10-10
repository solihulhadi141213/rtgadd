<?php
    if(!empty($_POST['KeywordBy'])){
        $KeywordBy = $_POST['KeywordBy'];
        if($KeywordBy=="ppg_blm_diangkat"){
            echo '
                <select name="keyword" id="keyword" class="form-control">
                    <option value="Belum">Belum Diangkat ASN</option>
                    <option value="Sudah">Sudah Diangkat ASN</option>
                </select>
            ';
        }else{
            echo '<input type="text" name="keyword" id="keyword" class="form-control">';
        }

    }else{
        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
    }
?>