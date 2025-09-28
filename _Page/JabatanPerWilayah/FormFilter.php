<?php
    include "../../_Config/Connection.php";
    if(empty($_POST['KeywordBy'])){
        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
    }else{
        $keyword_by=$_POST['KeywordBy'];
        if($keyword_by=="province"||$keyword_by=="regency"||$keyword_by=="department"){
            echo '<select type="text" name="keyword" id="keyword" class="form-control">';
            echo '  <option value="">Pilih</option>';
            $query = mysqli_query($Conn, "SELECT DISTINCT $keyword_by FROM  position_region ORDER BY $keyword_by ASC");
            while ($data = mysqli_fetch_array($query)) {
                $REGION_COLUM= $data[$keyword_by];
                echo '  <option value="'.$REGION_COLUM.'">'.$REGION_COLUM.'</option>';
            }
            echo '</select>';
        }else{
            echo '<input type="text" name="keyword" id="keyword" class="form-control">';
        }
    }
?>