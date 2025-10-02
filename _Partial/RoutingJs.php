<?php 
    $date_version=date('YmdHis');
    if(empty($_GET['Page'])){
        //Dafault Javascript Diarahkan Ke Dashboard

        //Routing Javascript berdasarkan Level akses

        //Tangkap 'access_client'
        $access_client = GetDetailData($Conn, 'access', 'id_access', $SessionIdAccess, 'access_client');
        if(empty($access_client)){

            //Jika Kosong maka dia Seorang Admin
            echo '<script type="text/javascript" src="_Page/Dashboard/Dashboard.js?V='.$date_version.'"></script>';
        }else{
            //Jika bukan admin
            echo '<script type="text/javascript" src="_Page/DashboardClient/DashboardClient.js?V='.$date_version.'"></script>';
        }
        
    }else{
        $Page=$_GET['Page'];
        // Routing Javascript Berdasarkan Halaman
        $scripts = [
            "MyProfile"         => "_Page/MyProfile/MyProfile.js",
            "AksesFitur"        => "_Page/AksesFitur/AksesFitur.js",
            "AksesEntitas"      => "_Page/AksesEntitas/AksesEntitas.js",
            "Akses"             => "_Page/Akses/Akses.js",
            "Client"            => "_Page/Client/Client.js",
            "SettingGeneral"    => "_Page/SettingGeneral/SettingGeneral.js",
            "SettingEmail"      => "_Page/SettingEmail/SettingEmail.js",
            "Wilayah"           => "_Page/Wilayah/Wilayah.js",
            "Sekolah"           => "_Page/Sekolah/Sekolah.js",
            "Jabatan"           => "_Page/Jabatan/Jabatan.js",
            "Instansi"          => "_Page/Instansi/Instansi.js",
            "JabatanPerWilayah" => "_Page/JabatanPerWilayah/JabatanPerWilayah.js",
            "AbkPerSekolah"     => "_Page/AbkPerSekolah/AbkPerSekolah.js",
            "CornJob"           => "_Page/CornJob/CornJob.js",
            "GeoJson"           => "_Page/GeoJson/GeoJson.js",
            "Aktivitas"         => "_Page/Aktivitas/Aktivitas.js",
            "Help"              => "_Page/Help/Help.js"
        ];

        // Cek apakah halaman ada dalam daftar dan sertakan file JS yang sesuai
        if (!empty($_GET['Page']) && isset($scripts[$_GET['Page']])) {
            echo '<script type="text/javascript" src="' . $scripts[$_GET['Page']] . '?V='.$date_version.'"></script>';
        }
    }
    echo '<script type="text/javascript" src="_Partial/Universal.js?V='.$date_version.'"></script>';
?>