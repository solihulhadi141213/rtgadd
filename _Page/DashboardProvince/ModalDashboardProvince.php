<div class="modal fade" id="ModalFilterKebutuhanGuruByKabKot" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesFilterKebutuhanGuruByKabKot">
                <input type="hidden" name="page" id="page_kebutuhan_guru_by_kabkot" value="1">
                <input type="hidden" name="province_code" id="province_code" value="<?php echo "$province_code"; ?>">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-funnel"></i> Filter Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="school_level">
                                <b>Jenjang Pendidikan</b>
                            </label>
                            <?php
                                echo '
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="school_level" id="school_level" value="" checked="">
                                        <label class="form-check-label" for="school_level">Semua Jenjang</label>
                                    </div>
                                ';
                                //Looping Semua Sekolah dengan DISTINCT 
                                $no_row = 1;
                                $query = mysqli_query($Conn, "SELECT DISTINCT school_level FROM school ");
                                while ($data = mysqli_fetch_array($query)) {
                                    $school_level = $data['school_level'];
                                    echo '
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="school_level" id="school_level'.$no_row.'" value="'.$school_level.'">
                                            <label class="form-check-label" for="school_level'.$no_row.'"><small>'.$school_level.'</small></label>
                                        </div>
                                    ';
                                    $no_row++;
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-rounded">
                        <i class="bi bi-save"></i> Filter
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDetailKabKot" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="index.php" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-funnel"></i> Detail Kab/Kota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12" id="FormDetailKabKot">
                            <!-- Menampilkan Detail Kabupaten/Kota -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" disabled class="btn btn-primary btn-rounded" id="ButtonSelengkapnya">
                        Selengkapnya <i class="bi bi-arrow-right-circle"></i>
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>