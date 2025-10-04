<?php
    include "_Page/Logout/ModalLogout.php";
    if(empty($_GET['Page'])){
        //Jika Tidak Ada Get 'Page' maka modal diarahkan ke dashboard
        //Routing modal berdasarkan Level akses
        //Tangkap 'access_client'
        $access_client = GetDetailData($Conn, 'access', 'id_access', $SessionIdAccess, 'access_client');
        if(empty($access_client)){

            //Jika Kosong maka dia Seorang Admin
            include "_Page/Dashboard/ModalDashboard.php";
        }else{
            //Jika bukan admin
            include "_Page/DashboardClient/ModalDashboardClient.php";
        }
    }else{
        $Page=$_GET['Page'];
        
        // Daftar halaman dan modal yang terkait
        $modals = [
            "MyProfile"                 => "_Page/MyProfile/ModalMyProfile.php",
            "DashboardProvince"         => "_Page/DashboardProvince/ModalDashboardProvince.php",
            "AksesFitur"                => "_Page/AksesFitur/ModalAksesFitur.php",
            "AksesEntitas"              => "_Page/AksesEntitas/ModalAksesEntitas.php",
            "Akses"                     => "_Page/Akses/ModalAkses.php",
            "Client"                    => "_Page/Client/ModalClient.php",
            "SettingEmail"              => "_Page/SettingEmail/ModalSettingEmail.php",
            "Wilayah"                   => "_Page/Wilayah/ModalWilayah.php",
            "Sekolah"                   => "_Page/Sekolah/ModalSekolah.php",
            "Jabatan"                   => "_Page/Jabatan/ModalJabatan.php",
            "Instansi"                  => "_Page/Instansi/ModalInstansi.php",
            "JabatanPerWilayah"         => "_Page/JabatanPerWilayah/ModalJabatanPerWilayah.php",
            "JabatanPerInstansi"        => "_Page/JabatanPerInstansi/ModalJabatanPerInstansi.php",
            "AbkPerSekolah"             => "_Page/AbkPerSekolah/ModalAbkPerSekolah.php",
            "GeoJson"                   => "_Page/GeoJson/ModalGeoJson.php",
            "GeoJsonBps"                => "_Page/GeoJsonBps/ModalGeoJsonBps.php",
            "MapViewer"                 => "_Page/MapViewer/ModalMapViewer.php",
            "CornJob"                   => "_Page/CornJob/ModalCornJob.php",
            "Aktivitas"                 => "_Page/Aktivitas/ModalAktivitas.php",
            "Help"                      => "_Page/Help/ModalHelp.php"
        ];

        // Cek apakah halaman memiliki modal terkait dan sertakan file modalnya
        if (!empty($_GET['Page']) && isset($modals[$_GET['Page']])) {
            include $modals[$_GET['Page']];
        }
    }
?>