<?php
    include "../../_Config/Connection.php";
    if(empty($_POST['keyword_by'])){
        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
    }else{
        $keyword_by=$_POST['keyword_by'];

        //Kategori
        if($keyword_by=="kategori"){
            echo '<select type="text" name="keyword" id="keyword" class="form-control">';
            echo '  <option value="">Pilih</option>';
            $query = mysqli_query($Conn, "SELECT DISTINCT kategori FROM help ORDER BY kategori ASC");
            while ($data = mysqli_fetch_array($query)) {
                $kategori= $data['kategori'];
                echo '  <option value="'.$kategori.'">'.$kategori.'</option>';
            }
            echo '</select>';
        }else{

            //datetime_update
            if($keyword_by=="datetime_update"){
                echo '<input type="date" name="keyword" id="keyword" class="form-control">';
            }else{

                //read_status
                if($keyword_by=="read_status"){
                    echo '
                        <select type="text" name="keyword" id="keyword" class="form-control">
                            <option value="0">Belum Dibaca</option>
                            <option value="1">Sudah Dibaca</option>
                        </select>
                    ';
                }else{
                    echo '<input type="text" name="keyword" id="keyword" class="form-control">';
                }
            }
        }
    }
?>