<aside id="sidebar" class="sidebar menu_background">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu==""){echo "";}else{echo "collapsed";} ?>" href="index.php">
                <i class="bi bi-grid"></i> <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="SettingGeneral"||$PageMenu=="SettingEmail"||$PageMenu=="PaymentGateway"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#components-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-gear"></i>
                    <span>Pengaturan</span><i class="bi bi-chevron-down ms-auto">
                </i>
            </a>
            <ul id="components-nav" class="nav-content collapse <?php if($PageMenu=="SettingGeneral"||$PageMenu=="SettingEmail"||$PageMenu=="PaymentGateway"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=SettingGeneral" class="<?php if($PageMenu=="SettingGeneral"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Pengaturan Umum</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=SettingEmail" class="<?php if($PageMenu=="SettingEmail"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Email Gateway</span>
                    </a>
                </li> 
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="AksesFitur"||$PageMenu=="AksesEntitas"||$PageMenu=="Akses"||$PageMenu=="Client"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#components2-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-key"></i>
                    <span>Aksesibilitas</span><i class="bi bi-chevron-down ms-auto">
                </i>
            </a>
            <ul id="components2-nav" class="nav-content collapse <?php if($PageMenu=="AksesFitur"||$PageMenu=="AksesEntitas"||$PageMenu=="Akses"||$PageMenu=="Client"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=AksesFitur" class="<?php if($PageMenu=="AksesFitur"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Fitur Aplikasi</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=AksesEntitas" class="<?php if($PageMenu=="AksesEntitas"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Group/Entitas</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=Akses" class="<?php if($PageMenu=="Akses"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Akses Admin</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=Client" class="<?php if($PageMenu=="Client"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Akses Client</span>
                    </a>
                </li> 
            </ul>
        </li>
        <li class="nav-heading">Referensi</li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="Wilayah"){echo "";}else{echo "collapsed";} ?>" href="index.php?Page=Wilayah">
                <i class="bi bi-map"></i> <span>Wilayah</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="Sekolah"){echo "";}else{echo "collapsed";} ?>" href="index.php?Page=Sekolah">
                <i class="bi bi-building"></i> <span>Sekolah</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="Jabatan"){echo "";}else{echo "collapsed";} ?>" href="index.php?Page=Jabatan">
                <i class="bi bi-award"></i> <span>Jabatan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="Instansi"){echo "";}else{echo "collapsed";} ?>" href="index.php?Page=Instansi">
                <i class="bi bi-bank"></i> <span>Instansi</span>
            </a>
        </li>
        <li class="nav-heading">Distribusi Data</li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="JabatanPerWilayah"){echo "";}else{echo "collapsed";} ?>" href="index.php?Page=JabatanPerWilayah">
                <i class="bi bi-table"></i> <span>Jabatan Per Wilayah</span>
            </a>
        </li>
         <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="JabatanPerInstansi"){echo "";}else{echo "collapsed";} ?>" href="index.php?Page=JabatanPerInstansi">
                <i class="bi bi-table"></i> <span>Jabatan Per Instansi</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="AbkPerSekolah"){echo "";}else{echo "collapsed";} ?>" href="index.php?Page=AbkPerSekolah">
                <i class="bi bi-table"></i> <span>AKB Per Sekolah</span>
            </a>
        </li>
        <li class="nav-heading">Fitur Lainnya</li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu!=="GeoJson"){echo "collapsed";} ?>" href="index.php?Page=GeoJson">
                <i class="bi bi-globe"></i>
                <span>Geo Json</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu!=="Aktivitas"){echo "collapsed";} ?>" href="index.php?Page=Aktivitas">
                <i class="bi bi-circle"></i>
                <span>Log Aktivitas</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu!=="Help"){echo "collapsed";} ?>" href="index.php?Page=Help&Sub=HelpData">
                <i class="bi bi-question"></i>
                <span>Dokumentasi</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalLogout">
                <i class="bi bi-box-arrow-in-left"></i>
                <span>Keluar</span>
            </a>
        </li>
    </ul>
</aside> 