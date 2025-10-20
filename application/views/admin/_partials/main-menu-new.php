<!-- Logo -->
<a href="<?php ?>" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><b>HMS</b></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><b>HMS</b></span>
</a>
<!-- Header Navbar: style can be found in header.less -->
<nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </a>

    <!-- togle main-menu bar -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
            <i class="fa fa-bars"></i>
        </button>
    </div>

    <!-- Main Menu -->
    <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
        <ul class="nav navbar-nav">

  <!--li class="active"><a href="#">Admin <span class="sr-only">(current)</span></a></li-->
            <?php
            $username = $this->session->userdata('username');
            $name = $this->session->userdata('nama');
            $menuss = unserialize(decrypt_url($this->session->userdata('mainmenu')));
            $subMenu = [];
            $submainmenu = $this->session->userdata('submainmenu');
            foreach ($menuss as $menus):
                $link = $menus->inisial_class;
                if (!$submainmenu){
                    $subMenu[$menus->inisial_class] = $this->m_menu->sub_main_menu($username, $link);
                    
                }
                if ($this->uri->segment(1) == $link) {
                    $active = 'active';
                } else {
                    $active = '';
                }
                ?>
                <li class="<?php echo $active; ?>"><a href="<?php echo base_url($link); ?>"></span><?php echo $menus->nama ?></a></li>
                <?php
            endforeach;
            if (!$submainmenu) {
                $text = serialize($subMenu);
                $this->session->set_userdata('submainmenu', encrypt_url($text));
            }
            ?>

        </ul>
    </div>
    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            <li>
                <a class="show_printer" href="javascript:void(0);" title="Printer Share">
                    <i class="fa fa-print"></i>
                </a>
            </li>
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <img src="<?php echo base_url('dist/img/profile_default.png') ?>" class="user-image" alt="User Image">
                    <span class="hidden-xs"><?php echo $name['nama']; ?></span>
                </a>
                <ul class="dropdown-menu">
                    <!-- User image -->
                    <li class="user-header">
                        <img src="<?php echo base_url('dist/img/profile_default.png') ?>" class="img-circle" alt="User Image">
                        <p>
                            <?php echo $name['nama']; ?>
                        </p>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <div class="pull-left">
                            <?php
                            $sub_menu = $this->db->get_where('user_priv as mms', array('main_menu_sub_kode' => 'mms91', 'username' => $username));
                            if ($sub_menu->num_rows() > 0) {
                                ?>
                                <a href="<?php echo base_url('setting/ganti_pass/edit'); ?>" class="btn btn-default btn-flat">Ganti Password</a>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="pull-right">
                            <a href="<?php echo base_url('login/logout'); ?>" class="btn btn-default btn-flat">Sign out</a>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
