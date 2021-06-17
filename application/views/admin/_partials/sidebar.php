
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

      $username = $this->session->userdata('username'); 
      $get_inisial = $this->m_menu->get_inisial($id, $this->uri->segment(1), $this->uri->segment(2) )->row_array();//untuk mengambil inisial kelas di mms agar sub menu aktif
      $menu     = $this->m_menu->sub_main_menu($username,$this->uri->segment(1));//mengambil data sub main menu

          foreach ($menu as $menus): 
            if($get_inisial['inisial_class']==$menus->inisial_class AND $id == $menus->dept_id){
              $active = 'active';
            }else{
              $active = '';
            }
            ?>
            <li class="<?php echo $active; ?>">
              <a href="<?php echo  base_url($menus->link_menu); ?>"><i class="<?php echo $menus->ikon; ?>"></i> <span><?php echo $menus->nama ?></span></a>
            </li>
       
    <?php endforeach; ?>
    <!--li><a href="#"><i class="fa fa-book"></i> <span><?php echo $username;?></span></a></li-->
     <li class="header">END NAVIGATION</li>
  </ul>

</section>
