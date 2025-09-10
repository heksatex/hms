
<style>
  .slimScrollBar {
    background-color: white !important;
    opacity: 1 !important;
    cursor:pointer;
    width:5px !important;
    /* visibility: hidden; */
  }
  .sidebar:hover,
  .sidebar:focus {
    visibility: visible;
  }



  
</style>

<section class="sidebar">
  <!-- Logo Heksatex -->
  <div class="user-panel">
    <div class="image text-center">
      <img src="<?php echo base_url('dist/img/logo.png') ?>"  >
    </div>
  </div>
  <!-- Menu Side Bar-->
  <ul class="sidebar-menu">
    <li class="header">MAIN NAVIGATION</li>

    <?php
      if(!empty($list->dept_id)){
        $id = $list->dept_id;//di dapat dari parsingan controller masing" view(edit/add) (berasal dari tabel head dengan array nya harus list)
      }else{
        $id = $id_dept;// di dapat dari parsingan controller yang view nya list aja dengan array nya harus id_dept
      }

      $username    = $this->session->userdata('username'); 
      $get_inisial = $this->m_menu->get_inisial($id, $this->uri->segment(1), $this->uri->segment(2) )->row_array();//untuk mengambil inisial kelas di mms agar sub menu aktif
//      $menu        = $this->m_menu->sub_main_menu($username,$this->uri->segment(1));//mengambil data sub main menu
      $menuss = unserialize(decrypt_url($this->session->userdata('submainmenu')));
      foreach ($menuss[$this->uri->segment(1)] as $menus): 
     
        $sub_menu = $this->db->order_by('mms.row_order','ASC')->JOIN('user_priv as up','mms.kode = up.main_menu_sub_kode','INNER')
              ->get_where('main_menu_sub as mms', array('mms.is_menu_sub' => $menus->kode, 'up.username' => $username));
//        $this->db->order_by('mms.row_order','ASC');
        $child    = '';

        // jika terdapat is_menu_sub by mms
        if($sub_menu->num_rows() > 0 ){// sub menu dengan child nya

          // cek child dari parent/treeview apa aktif
          foreach($sub_menu->result() as $sub){

              if($get_inisial['inisial_class']==$sub->inisial_class AND $id == $sub->dept_id){
                $child = 'active';
              }
          }

          // cek apakah is_menu_sub nya tidak kosong
          if(!empty($get_inisial['is_menu_sub']) and $child == 'active'){
            $treeview_ = 'treeview active';
          }else{
            $treeview_ = '';
          }
          ?>
            <!-- treeview menu-->
            <li class="<?php echo $treeview_; ?> ">
              <a href="#">
                <i class="<?php echo $menus->ikon; ?>"></i>
                <span><?php echo $menus->nama ?></span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>

              <ul class="treeview-menu">
                <?php 
                foreach($sub_menu->result() as $sub){
                  $active2 = '';
                  if($get_inisial['inisial_class']==$sub->inisial_class AND $id == $sub->dept_id){
                    $active2 = 'active';
                  }
                ?>
                  <li class="<?php echo $active2; ?>">
                    <a href="<?php echo  base_url($sub->link_menu); ?>"><i class="<?php echo $sub->ikon; ?>"></i> <span><?php echo $sub->nama ?></span></a>
                  </li>

                <?php 
                } ?>
              </ul>

            </li> 
            <!--// treeview menu-->
          <?php
        
        }else{  // sub menu tanpa child

            // jika is_menu_sub nya kosong
            if($get_inisial['inisial_class']==$menus->inisial_class AND $id == $menus->dept_id){
              $active = 'active';
            }else{
              $active = '';
            }
          ?>
            <li class="<?php echo $active; ?>">
              <a href="<?php echo  base_url($menus->link_menu); ?>"><i class="<?php echo $menus->ikon; ?>"></i> <span><?php echo $menus->nama ?></span></a>
            </li>
          <?php  
        }

      endforeach;
    ?>
     
     <li class="header">END NAVIGATION</li>
  </ul>

</section>
