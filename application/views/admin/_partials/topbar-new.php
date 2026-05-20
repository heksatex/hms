<style type="text/css">
    .link{
        color: white;
    }
</style>

<?php
if (empty($deptid)) {
    $deptid = '';
} else {
    $deptid = $deptid;
}

if (!empty($hms_top)) {
    ?>
    <style type="text/css">
        @media (max-width: 767px) {
            .logo {
                display: none !important;
            }

            .content-wrapper .content-header{
                padding: 0px !important;
            }
        }
    </style>
    <?php
}
?>

<!-- Top Bar -->
<nav class="top-bar" style="background:#222d32;">
    <div class="col-md-5 col-xs-12 hidden-xs hidden-sm" style="color:white; ">
        <h4>   
            <a class="link" href="<?php echo base_url($this->uri->segment(1)); ?>">
                <?php echo ucfirst($this->uri->segment(1)) ?><!--ini URI 1 tampil di layar-->
            </a>
            <?php if (!empty($this->uri->segment(2))) echo " / "; ?><!-- cek apa ada uri -->
            <?php echo ucfirst($this->uri->segment(2)) ?><!--ini URI 2 tampil di layar-->
            <?php if (!empty($this->uri->segment(3))) echo " / "; ?><!-- cek apa ada uri -->

            <?php echo ucfirst($this->uri->segment(3)) ?><!--ini URI 3 tampil di layar-->
        </h4>
    </div>
    <div class="col-md-7 col-xs-12" id="btnShow" >
        <div style="padding-top: 0px; text-align: center">
            <?php
            $username = $this->session->userdata('username');

            if ($this->uri->segment(3) == 'add' or $this->uri->segment(3) == 'edit' or $this->uri->segment(3) == 'edit_barcode') {
                if ($this->session->userdata("topbar_{$username}")) {
                    $level_akses = unserialize($this->session->userdata("topbar_{$username}"));
                } else {
                    $level_akses = $this->_module->get_level_akses_by_user($username)->row_array();
                    $this->session->set_userdata("topbar_{$username}", serialize($level_akses));
                }

                $data['level'] = $level_akses['level'];

                // cek departemen by user
                if ($this->session->userdata("topbar_depar_{$username}")) {
                    $cek_dept = unserialize($this->session->userdata("topbar_depar_{$username}"));
                } else {
                    $cek_dept = $this->_module->cek_departemen_by_user($username)->row_array();
                    $this->session->set_userdata("topbar_depar_{$username}", serialize($cek_dept));
                }




                if ($this->session->userdata("topbar_depar_form_button{$username}_{$deptid}")) {
                    $row = unserialize($this->session->userdata("topbar_depar_form_button{$username}_{$deptid}"));
                } else {
                    $row = $this->m_button->form_button($username, $this->uri->segment(2), $deptid);
                    $this->session->set_userdata("topbar_depar_form_button{$username}_{$deptid}", serialize($row));
                }

                $akses_menu = false;
                foreach ($row as $val) {
                    $akses_menu = true;

                    if ($val->jenis_button == 'hold' or $val->jenis_button == 'unhold') {
                        if ($data['level'] == "Super Administrator" OR $data['level'] == "Administrator" OR strpos($cek_dept['dept'], 'PPIC') !== false) {
                            ?>
                            <button type="button" id="<?php echo $val->id_button; ?>" class="<?php echo $val->class_button; ?>" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."><i class="<?php echo $val->ikon; ?>"></i> <?php echo $val->caption; ?></button>
                            <?php
                        }
                    } else {
                        ?>
                        <button type="button" id="<?php echo $val->id_button; ?>" class="<?php echo $val->class_button; ?>" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."><i class="<?php echo $val->ikon; ?>"></i> <?php echo $val->caption; ?></button>
                        <?php
                    }
                }
                if ($akses_menu == false) {

                    if ($this->session->userdata("topbar_depar_print_button{$username}_{$deptid}")) {
                        $row_p = unserialize($this->session->userdata("topbar_depar_print_button{$username}_{$deptid}"));
                    } else {
                        $row_p = $this->m_button->form_button_print($this->uri->segment(2), $deptid);
                        $this->session->set_userdata("topbar_depar_print_button{$username}_{$deptid}", serialize($row_p));
                    }
                    // aktif ketika tidak ada akses dan dibukain hanya btn print saja
                    foreach ($row_p as $row_pr) {
                        ?>
                        <button type="button" id="<?php echo $row_pr->id_button; ?>" class="<?php echo $row_pr->class_button; ?>" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."><i class="<?php echo $row_pr->ikon; ?>"></i> <?php echo $row_pr->caption; ?></button>
                        <?php
                    }
                }
            } else {

                if ($this->session->userdata("topbar_depar_view_button{$username}_{$deptid}")) {
                    $row = unserialize($this->session->userdata("topbar_depar_view_button{$username}_{$deptid}"));
                } else {
                    $row = $this->m_button->view_button($username, $this->uri->segment(2), $deptid);
                    $this->session->set_userdata("topbar_depar_view_button{$username}_{$deptid}", serialize($row));
                }



                foreach ($row as $val) {
                    if (empty($val->link_button)) {
                        $link = "javascript:void(0);";
                    } else {
                        $link = base_url($val->link_button);
                    }
                    ?>

                    <a href ="<?php echo $link; ?>">
                        <button type="button" id="<?php echo $val->id_button; ?>" class="<?php echo $val->class_button; ?>"  ><i class="<?php echo $val->ikon; ?>"></i> <?php echo $val->caption; ?></button>
                    </a>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</nav>

