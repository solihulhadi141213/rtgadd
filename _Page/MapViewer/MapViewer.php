<div class="pagetitle">
    <h1>
        <a href="">
            <i class="bi bi-map"></i> Map Viewer</a>
        </a>
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Map Viewer</li>
        </ol>
    </nav>
</div>
<section class="section dashboard">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <small>
                    Berikut ini adalah halaman 'Map Viewer' yang berfungsi untuk menampilkan peta dari GeoJson. <br>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <b class="card-title">GeoJSon Viewer</b>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <label for="geo_json_example">
                                <small>Silahkan Masukan GeoJson String Pada Form Berikut</small>
                            </label>
                            <textarea name="geo_json_example" id="geo_json_example" class="form-control mb-3" style="width:100%; height:400px;"></textarea>
                            <button type="button" class="btn btn-md btn-primary btn-block" id="button_show_map">
                                Tampilkan Peta
                            </button>
                        </div>
                        <div class="col-8 text-center">
                            <div id="map_viewer" style="width:100%; height:500px;">Menunggu Proses</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <small>Map From Leaflet</small>
                </div>
            </div>
            
        </div>
    </div>
</section>