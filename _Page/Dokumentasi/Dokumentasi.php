<div class="pagetitle">
    <h1>
        <a href="">
            <i class="bi bi-question-circle"></i> Dokumentasi</a>
        </a>
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Dokumentasi</li>
        </ol>
    </nav>
</div>
<section class="section dashboard">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <small>
                    Berikut ini adalah halaman dokumentasi. Halaman ini berfungsi menampilkan catatan dan informasi pembaharuan aplikasi dan pengembangan yang telah dilakukan.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </small>
            </div>
        </div>
    </div>
    <?php
        if(empty($_GET['Sub'])){
            $Sub = "";
        }else{
            $Sub = $_GET['Sub'];
        }
        if($Sub=="Detail"){
            include "_Page/Dokumentasi/DetailDokumentasi.php";
        }else{
            include "_Page/Dokumentasi/DokumentasiHome.php";
        }
    ?>
</div>