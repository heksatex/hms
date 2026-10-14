<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin();

        $this->load->model(
            'M_menu2',
            'menu'
        );
    }


    /* =========================================================
     * INDEX
     * ========================================================= */

    public function index()
    {
        $data['id_dept']   = 'SMM';
        $data['menu_tree'] =$this->menu->get_menu_tree();
        $this->load->view('setting/v_menu',$data);
    }


    /* =========================================================
     * GET MAIN MENU
     * ========================================================= */

    public function get_main_menu()
    {
        $kode = trim($this->input->post('kode'));
        $data =$this->menu->get_main_menu_by_kode($kode);
        echo json_encode([
            'status' => !empty($data),
            'data'   => $data
        ]);
    }


    /* =========================================================
     * GET SUB MENU
     * ========================================================= */

    public function get_sub_menu()
    {
        $kode =trim($this->input->post('kode'));
        $data =$this->menu->get_sub_menu_by_kode($kode);
        echo json_encode([
            'status' => !empty($data),
            'data'   => $data
        ]);
    }


    /* =========================================================
     * SAVE MAIN MENU
     * ========================================================= */

    public function save_main()
    {
        $kode =trim($this->input->post('kode'));
        $nama =trim($this->input->post('nama'));
        if ($kode == '' || $nama == '') {
            $this->json_error('Kode dan nama menu wajib diisi.');
            return;
        }

        if ($this->menu->main_exists($kode)) {
            $this->json_error('Kode menu sudah digunakan.');
            return;
        }

        $data = [
            'kode'          => $kode,
            'nama'          => $nama,
            'inisial_class' =>trim($this->input->post('inisial_class')),
            'row_order' =>(int) $this->input->post('row_order')
        ];

        $result =$this->menu->insert_main_menu($data);

        echo json_encode([
            'status'  => $result,
            'message' => $result
                ? 'Menu utama berhasil ditambahkan.'
                : 'Menu utama gagal ditambahkan.'
        ]);
    }


    /* =========================================================
     * UPDATE MAIN MENU
     * ========================================================= */

    public function update_main()
    {
        $kode =trim($this->input->post('kode'));
        if ($kode == '') {
            $this->json_error('Kode menu tidak ditemukan.');
            return;
        }

        $data = [
            'nama' =>trim($this->input->post('nama')),
            'inisial_class' =>trim($this->input->post('inisial_class')),
            'row_order' =>(int) $this->input->post('row_order')
        ];
        $result =$this->menu->update_main_menu($kode,$data);

        echo json_encode([
            'status'  => $result,
            'message' => $result
                ? 'Menu utama berhasil diubah.'
                : 'Menu utama gagal diubah.'
        ]);
    }


    /* =========================================================
     * DELETE MAIN MENU
     * ========================================================= */

    public function delete_main()
    {
        $kode =
            trim($this->input->post('kode'));

        if ($kode == '') {

            $this->json_error(
                'Kode menu tidak ditemukan.'
            );

            return;
        }

        if ($this->menu->has_sub_menu($kode)) {

            $this->json_error(
                'Menu masih memiliki submenu. Hapus atau pindahkan submenu terlebih dahulu.'
            );

            return;
        }

        $result =
            $this->menu->delete_main_menu(
                $kode
            );

        echo json_encode([
            'status'  => $result,
            'message' => $result
                ? 'Menu utama berhasil dihapus.'
                : 'Menu utama gagal dihapus.'
        ]);
    }


    /* =========================================================
     * SAVE SUB MENU
     * ========================================================= */

    public function save_sub()
    {
        $kode = trim($this->input->post('kode'));
        $nama = trim($this->input->post('nama'));
        $main_kode = trim($this->input->post('main_menu_kode'));
        $parent_kode = trim($this->input->post('is_menu_sub'));
        $parent = $this->input->post('parent') === 'true' ? 'true' : '';

        if ($kode == '' || $nama == '' || $main_kode == '') {
            $this->json_error(
                'Menu utama, kode dan nama wajib diisi.'
            );
            return;
        }

        if (!$this->menu->main_exists($main_kode)) {
            $this->json_error('Menu utama tidak ditemukan.');
            return;
        }

        if ($this->menu->sub_exists($kode)) {
            $this->json_error('Kode submenu sudah digunakan.');
            return;
        }

        // Child tidak boleh menjadi Parent
        if ($parent_kode != '' && $parent === 'true') {
            $this->json_error('Child tidak boleh dijadikan Parent.');
            return;
        }

        $data = [
            'kode'          => $kode,
            'nama'          => $nama,
            'ikon'          => trim($this->input->post('ikon')),
            'link_menu'     => trim($this->input->post('link_menu')),
            'inisial_class' => trim($this->input->post('inisial_class')),
            'row_order'     => (int)$this->input->post('row_order'),
            'status_bar'    => (int)$this->input->post('status_bar'),
            'dept_id'       => trim($this->input->post('dept_id')),
            'parent'        => $parent,
            'is_menu_sub'   => $parent_kode,
            
        ];

        // Jika mempunyai parent berarti child
        if ($parent_kode != '') {
            $data['parent'] = '';
        }

        $this->db->trans_start();

        // Insert submenu
        $this->menu->insert_sub_menu($data);

        // Hubungkan ke main menu
        $this->menu->insert_rel([
            'main_menu_kode'     => $main_kode,
            'main_menu_sub_kode' => $kode
        ]);

        // Jika child, parent otomatis menjadi parent
        if ($parent_kode != '') {
            $this->menu->update_sub_menu($parent_kode, [
                'parent' => 'true'
            ]);
        }

        $this->db->trans_complete();

        $success = $this->db->trans_status();

        echo json_encode([
            'status' => $success,
            'message' => $success
                ? 'Submenu berhasil ditambahkan.'
                : 'Submenu gagal ditambahkan.'
        ]);
    }


    /* =========================================================
     * UPDATE SUB MENU
     * ========================================================= */

    public function update_sub()
    {
        $kode = trim($this->input->post('kode'));
        $nama = trim($this->input->post('nama'));
        $main_kode = trim($this->input->post('main_menu_kode'));
        $new_parent = trim($this->input->post('is_menu_sub'));

        if ($kode == '') {
            $this->json_error('Kode submenu tidak ditemukan.');
            return;
        }

        if ($nama == '') {
            $this->json_error('Nama submenu wajib diisi.');
            return;
        }

        if ($main_kode == '') {
            $this->json_error('Menu utama wajib dipilih.');
            return;
        }

        $old_data = $this->menu->get_sub_menu_by_kode($kode);

        if (empty($old_data)) {
            $this->json_error('Data submenu tidak ditemukan.');
            return;
        }

        $parent = $this->input->post('parent') === 'true'   ? 'true'  : '';

        if ($parent !== 'true' && $this->menu->has_child($kode)) {
            $this->json_error(
                'Tidak bisa menonaktifkan Parent karena masih memiliki Child.'
            );
            return;
        }

        $old_parent = trim($old_data['is_menu_sub']);

        $data = [
            'nama'          => $nama,
            'ikon'          => trim($this->input->post('ikon')),
            'link_menu'     => trim($this->input->post('link_menu')),
            'inisial_class' => trim($this->input->post('inisial_class')),
            'row_order'     => (int)$this->input->post('row_order'),
            'status_bar'    => (int)$this->input->post('status_bar'),
            'dept_id'       => trim($this->input->post('dept_id')),
            'is_menu_sub'   => $new_parent,
            'parent'        => $parent
        ];

        $this->db->trans_start();

        // Update data submenu
        $this->menu->update_sub_menu($kode, $data);

        // Pastikan relasi ke main menu ada
        $this->menu->delete_rel_by_sub($kode);

        $this->menu->insert_rel([
            'main_menu_kode'     => $main_kode,
            'main_menu_sub_kode' => $kode
        ]);

        // Parent lama sudah tidak punya child?
        if ($old_parent != '' && $old_parent != $new_parent) {

            if (!$this->menu->has_child($old_parent)) {

                $this->menu->update_sub_menu($old_parent, [
                    'parent' => ''
                ]);
            }
        }

        // Kalau sekarang menjadi child,
        // parent baru harus ditandai sebagai parent
        if ($new_parent != '') {

            $this->menu->update_sub_menu($new_parent, [
                'parent' => 'true'
            ]);
        }

        // Kalau submenu ini sendiri masih mempunyai child,
        // pastikan parent tetap true
        if ($this->menu->has_child($kode)) {

            $this->menu->update_sub_menu($kode, [
                'parent' => 'true'
            ]);

        } else {

            $this->menu->update_sub_menu($kode, [
                'parent' => ''
            ]);
        }

        $this->db->trans_complete();

        $success = $this->db->trans_status();

        echo json_encode([
            'status' => $success,
            'message' => $success
                ? 'Submenu berhasil diubah.'
                : 'Submenu gagal diubah.'
        ]);
    }

    /* =========================================================
     * DELETE SUB MENU
     * ========================================================= */

    public function delete_sub()
    {
        $kode = trim($this->input->post('kode'));

        if ($kode == '') {
            $this->json_error(
                'Kode submenu tidak ditemukan.'
            );
            return;
        }

        $data = $this->menu->get_sub_menu_by_kode($kode);

        if (empty($data)) {
            $this->json_error('Data submenu tidak ditemukan.');
            return;
        }

        // Simpan parent sebelum data dihapus
        $parent_kode = trim($data['is_menu_sub']);

        // Tidak boleh hapus jika masih punya child
        if ($this->menu->has_child($kode)) {
            $this->json_error('Menu masih memiliki child menu. Hapus child terlebih dahulu.');
            return;
        }

        if ($this->menu->has_button($kode)) {
            $this->json_error('Submenu tidak bisa dihapus karena masih memiliki button.');
            return;
        }

        // 3. Tidak boleh dihapus jika memiliki status
        if ($this->menu->has_status($kode)) {
            $this->json_error('Submenu tidak bisa dihapus karena masih memiliki status.');
            return;
        }
        
        // 4. Tidak boleh dihapus jika masih digunakan user
        if ($this->menu->has_user_priv($kode)) {
            $this->json_error('Submenu tidak bisa dihapus karena masih digunakan atau diberikan akses kepada user.');
            return;
        }


        $this->db->trans_start();

        // Hapus relasi
        $this->menu->delete_rel_by_sub($kode);

        // Hapus submenu
        $this->menu->delete_sub_menu($kode);

        // Jika sebelumnya merupakan child,
        // cek apakah parent masih punya child
        if ($parent_kode != '') {

            if (!$this->menu->has_child($parent_kode)) {

                $this->menu->update_sub_menu(
                    $parent_kode,
                    [
                        'parent' => ''
                    ]
                );
            }
        }

        $this->db->trans_complete();

        $success = $this->db->trans_status();

        echo json_encode([
            'status' => $success,
            'message' => $success
                ? 'Submenu berhasil dihapus.'
                : 'Submenu gagal dihapus.'
        ]);
    }


    /* =========================================================
     * ERROR JSON
     * ========================================================= */

    private function json_error($message)
    {
        echo json_encode([
            'status'  => false,
            'message' => $message
        ]);
    }

    public function get_sub_detail()
    {
        $kode = trim($this->input->post('kode'));

        if ($kode == '') {
            $this->json_error('Kode submenu tidak ditemukan.');
            return;
        }

        $data = $this->menu->get_sub_menu_by_kode($kode);

        if (empty($data)) {
            $this->json_error('Data submenu tidak ditemukan.');
            return;
        }

        $main = $this->menu->get_main_menu_by_sub($kode);

        $data['main_menu_kode'] = !empty($main)
            ? $main['kode']
            : '';

        echo json_encode([
            'status' => true,
            'data'   => $data
        ]);
    }


    public function get_parent_menu()
    {
        $main_kode = trim(
            $this->input->post('main_menu_kode')
        );

        if ($main_kode == '') {
            echo json_encode([
                'status' => false,
                'data'   => []
            ]);
            return;
        }

        $data = $this->menu->get_sub_menu_by_main($main_kode);

        echo json_encode([
            'status' => true,
            'data'   => $data
        ]);
    }

    public function get_next_sub_code()
    {
        $kode = $this->menu->get_next_sub_menu_code();

        echo json_encode([
            'status' => true,
            'kode'   => $kode
        ]);
    }

    public function get_buttons()
    {
        $sub_kode = trim($this->input->post('main_menu_sub_kode'));

        if ($sub_kode == '') {
            $this->json_error('Kode submenu tidak ditemukan.');
            return;
        }

        $sub = $this->menu->get_sub_menu_by_kode($sub_kode);
        if (empty($sub)) {
            $this->json_error('Data submenu tidak ditemukan.');
            return;
        }

        $data = $this->menu->get_buttons_by_sub($sub_kode);
        echo json_encode([
            'status' => true,
            'data'   => $data
        ]);
    }

    public function save_button()
    {
        $sub_kode = trim($this->input->post('main_menu_sub_kode'));
        $kategori = trim($this->input->post('kategori_button'));
        $jenis = trim($this->input->post('jenis_button'));

        if ($sub_kode == '') {
            $this->json_error('Kode submenu tidak ditemukan.');
            return;
        }

        if ($kategori == '') {
            $this->json_error('Kategori button wajib diisi.');
            return;
        }

        if ($jenis == '') {
            $this->json_error('Jenis button wajib diisi.');
            return;
        }

        if (!$this->menu->get_sub_menu_by_kode($sub_kode)) {
            $this->json_error('Submenu tidak ditemukan.');
            return;
        }

        if ($this->menu->button_exists($sub_kode,$kategori,$jenis)) {
            $this->json_error('Button dengan kategori dan jenis tersebut sudah ada.');
            return;
        }

        $data = [
            'main_menu_sub_kode' => $sub_kode,
            'kategori_button'    => $kategori,
            'jenis_button'       => $jenis,
            'link_button'        => trim($this->input->post('link_button')),
            'class_button'       => trim($this->input->post('class_button')),
            'id_button'          => trim($this->input->post('id_button')),
            'ikon'               => trim($this->input->post('ikon')),
            'caption'            => trim($this->input->post('caption')),
            'row_order'          => $this->menu->get_next_button_order($sub_kode)
        ];

        $result = $this->menu->insert_button($data);

        echo json_encode([
            'status' => $result,
            'message' => $result
                ? 'Button berhasil ditambahkan.'
                : 'Button gagal ditambahkan.'
        ]);
    }

    public function get_button_detail()
    {
        $sub_kode = trim($this->input->post('main_menu_sub_kode'));

        $kategori = trim($this->input->post('kategori_button'));

        $jenis = trim($this->input->post('jenis_button'));

        if ($sub_kode == '' || $kategori == '' || $jenis == '') {
            $this->json_error('Data button tidak lengkap.');
            return;
        }

        $data = $this->menu->get_button($sub_kode,$kategori,$jenis);

        if (empty($data)) {
            $this->json_error('Data button tidak ditemukan.');
            return;
        }

        echo json_encode([
            'status' => true,
            'data'   => $data
        ]);
    }

    public function update_button()
    {
        $sub_kode = trim($this->input->post('main_menu_sub_kode'));

        // Primary key lama
        $old_kategori = trim($this->input->post('old_kategori_button'));

        $old_jenis = trim($this->input->post('old_jenis_button'));

        // Primary key baru
        $kategori = trim($this->input->post('kategori_button'));

        $jenis = trim($this->input->post('jenis_button'));

        if ($sub_kode == '' || $old_kategori == '' || $old_jenis == '' ) {
            $this->json_error('Data button lama tidak lengkap.');
            return;
        }

        if ($kategori == '' || $jenis == '') {
            $this->json_error('Kategori dan jenis button wajib diisi.');
            return;
        }

        $old_data = $this->menu->get_button($sub_kode,$old_kategori,$old_jenis);

        if (empty($old_data)) {
            $this->json_error('Data button tidak ditemukan.');
            return;
        }

        /*
        * Jika kategori/jenis diubah,
        * cek apakah kombinasi barunya sudah dipakai.
        */
        if ($old_kategori != $kategori || $old_jenis != $jenis) {
            if ($this->menu->button_exists($sub_kode,$kategori,$jenis)) {      
               $this->json_error('Kategori dan jenis button tersebut sudah digunakan.');
                return;
            }
        }

        $data = [
            'kategori_button' => $kategori,
            'jenis_button'    => $jenis,
            'link_button'     => trim($this->input->post('link_button')),
            'class_button'    => trim($this->input->post('class_button')),
            'id_button'       => trim($this->input->post('id_button')),
            'ikon'            => trim($this->input->post('ikon')),
            'caption'         => trim($this->input->post('caption')),
            'row_order'       => (int)$this->input->post('row_order')
        ];

        $result = $this->menu->update_button($sub_kode,$old_kategori,$old_jenis,$data   );

        echo json_encode([
            'status' => $result,
            'message' => $result
                ? 'Button berhasil diubah.'
                : 'Button gagal diubah.'
        ]);
    }

    public function delete_button()
    {
        $sub_kode = trim($this->input->post('main_menu_sub_kode'));
        $kategori = trim($this->input->post('kategori_button'));
        $jenis = trim($this->input->post('jenis_button'));

        if ($sub_kode == '' || $kategori == '' || $jenis == '') {
            $this->json_error('Data button tidak lengkap.');
            return;
        }

        $data = $this->menu->get_button($sub_kode,$kategori,$jenis);

        if (empty($data)) {
            $this->json_error('Data button tidak ditemukan.');
            return;
        }

        $result = $this->menu->delete_button($sub_kode,$kategori,$jenis);

        echo json_encode([
            'status' => $result,
            'message' => $result
                ? 'Button berhasil dihapus.'
                : 'Button gagal dihapus.'
        ]);
    }
    
    public function get_status()
    {
        $sub_kode = trim($this->input->post('main_menu_sub_kode'));

        if ($sub_kode == '') {
            $this->json_error('Kode submenu tidak ditemukan.');
            return;
        }

        $sub = $this->menu->get_sub_menu_by_kode($sub_kode);

        if (empty($sub)) {
            $this->json_error('Data submenu tidak ditemukan.');
            return;
        }

        $data = $this->menu->get_status_by_sub($sub_kode);

        echo json_encode([
            'status' => true,
            'data'   => $data
        ]);
    }

    public function get_status_detail()
    {
        $sub_kode = trim($this->input->post('main_menu_sub_kode'));

        $jenis_status = trim($this->input->post('jenis_status'));

        if ($sub_kode == '' || $jenis_status == '' ) {
            $this->json_error('Data status tidak lengkap.');
            return;
        }

        $data = $this->menu->get_status($sub_kode,$jenis_status);

        if (empty($data)) {
            $this->json_error('Data status tidak ditemukan.');
            return;
        }

        echo json_encode([
            'status' => true,
            'data'   => $data
        ]);
    }

    public function save_status()
    {
        $sub_kode = trim($this->input->post('main_menu_sub_kode'));

        $jenis_status = trim($this->input->post('jenis_status'));
        $nama_status = trim($this->input->post('nama_status'));

        if ($sub_kode == '') {
            $this->json_error('Kode submenu tidak ditemukan.');
            return;
        }

        if ($jenis_status == '') {
            $this->json_error('Jenis status wajib diisi.');
            return;
        }

        if ($nama_status == '') {
            $this->json_error('Nama status wajib diisi.');
            return;
        }

        if (!$this->menu->get_sub_menu_by_kode($sub_kode)) {
            $this->json_error('Submenu tidak ditemukan.');
            return;
        }

        if ($this->menu->status_exists($sub_kode,$jenis_status)) {
            $this->json_error('Jenis status tersebut sudah ada.');
            return;
        }

        $data = [
            'main_menu_sub_kode' => $sub_kode,
            'jenis_status'       => $jenis_status,
            'nama_status'        => $nama_status,
            'row_order'          => $this->menu->get_next_status_order($sub_kode)
        ];

        $result = $this->menu->insert_status($data);

        echo json_encode([
            'status' => $result,
            'message' => $result
                ? 'Status berhasil ditambahkan.'
                : 'Status gagal ditambahkan.'
        ]);
    }

    public function update_status()
    {
        $sub_kode = trim($this->input->post('main_menu_sub_kode'));

        $old_jenis_status = trim($this->input->post('old_jenis_status')
        );

        $jenis_status = trim($this->input->post('jenis_status'));

        $nama_status = trim($this->input->post('nama_status'));

        if ($sub_kode == '' ||$old_jenis_status == '') {
            $this->json_error('Data status lama tidak lengkap.');
            return;
        }

        if ($jenis_status == '') {
            $this->json_error('Jenis status wajib diisi.');
            return;
        }

        if ($nama_status == '') {
            $this->json_error('Nama status wajib diisi.');
            return;
        }

        $old_data = $this->menu->get_status($sub_kode,$old_jenis_status);

        if (empty($old_data)) {
            $this->json_error('Data status tidak ditemukan.');
            return;
        }

        if ($old_jenis_status != $jenis_status) {

            if ($this->menu->status_exists($sub_kode,$jenis_status)) {
                $this->json_error('Jenis status tersebut sudah digunakan.');
                return;
            }
        }

        $data = [
            'jenis_status' => $jenis_status,
            'nama_status'  => $nama_status,
            'row_order'    => (int)$this->input->post('row_order')
        ];

        $result = $this->menu->update_status($sub_kode,$old_jenis_status,$data);

        echo json_encode([
            'status' => $result,
            'message' => $result
                ? 'Status berhasil diubah.'
                : 'Status gagal diubah.'
        ]);
    }

    public function delete_status()
    {
        $sub_kode = trim($this->input->post('main_menu_sub_kode'));
        $jenis_status = trim($this->input->post('jenis_status'));

        if ($sub_kode == '' ||$jenis_status == '') {
            $this->json_error('Data status tidak lengkap.');
            return;
        }

        $data = $this->menu->get_status($sub_kode,$jenis_status);

        if (empty($data)) {
            $this->json_error('Data status tidak ditemukan.');
            return;
        }

        $result = $this->menu->delete_status($sub_kode,$jenis_status);

        echo json_encode([
            'status' => $result,
            'message' => $result
                ? 'Status berhasil dihapus.'
                : 'Status gagal dihapus.'
        ]);
    }

    public function save_sub_order()
    {
        $orders = $this->input->post('orders');

        if (!is_array($orders) || empty($orders)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Data urutan submenu tidak ditemukan.'
            ]);
            return;
        }

        $this->db->trans_start();

        foreach ($orders as $item) {

            $kode = trim($item['kode'] ?? '');
            $order = (int)($item['row_order'] ?? 0);

            if ($kode == '') {
                continue;
            }

            if (!$this->menu->sub_exists($kode)) {
                continue;
            }

            $this->menu->update_sub_menu_order(
                $kode,
                $order
            );
        }

        $this->db->trans_complete();

        $status = $this->db->trans_status();

        echo json_encode([
            'status'  => $status,
            'message' => $status
                ? 'Urutan submenu berhasil disimpan.'
                : 'Gagal menyimpan urutan submenu.'
        ]);
    }


    public function save_child_order()
    {
        $parent_kode = trim($this->input->post('parent_kode'));
        $orders      = $this->input->post('orders');

        if ($parent_kode == '' || !is_array($orders) || empty($orders)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Data urutan child tidak ditemukan.'
            ]);
            return;
        }

        // Pastikan parent memang ada
        if (!$this->menu->sub_exists($parent_kode)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Parent submenu tidak ditemukan.'
            ]);
            return;
        }

        $this->db->trans_start();

        foreach ($orders as $item) {

            $kode  = trim($item['kode'] ?? '');
            $order = (int)($item['row_order'] ?? 0);

            if ($kode == '') {
                continue;
            }

            // Ambil data child
            $child = $this->menu->get_sub_menu_by_kode($kode);

            if (!$child) {
                continue;
            }

            /*
            * PENTING:
            * Pastikan child memang milik parent yang sedang
            * diurutkan.
            */
            if ((string)$child['is_menu_sub'] !== (string)$parent_kode) {
                continue;
            }

            $this->menu->update_sub_menu_order($kode,$order);
        }

        $this->db->trans_complete();
        $status = $this->db->trans_status();

        echo json_encode([
            'status'  => $status,
            'message' => $status
                ? 'Urutan child berhasil disimpan.'
                : 'Gagal menyimpan urutan child.'
        ]);
    }


    public function get_sub_user_access()
    {
        $kode = trim($this->input->post('kode'));

        if ($kode == '') {
            $this->json_error('Kode submenu wajib diisi.');
            return;
        }

        if (!$this->menu->sub_exists($kode)) {
            $this->json_error('Submenu tidak ditemukan.');
            return;
        }

        $users = $this->menu->get_users_by_sub_access($kode);

        echo json_encode([
            'status' => true,
            'data'   => $users
        ]);
    }


    public function delete_sub_user_access()
    {
        $kode      = trim($this->input->post('kode'));
        $username  = trim($this->input->post('username'));
        $main_kode = trim($this->input->post('main_menu_kode'));

        if ($kode == '' || $username == '' || $main_kode == '') {
            $this->json_error('Data akses user belum lengkap.');
            return;
        }

        if (!$this->menu->sub_exists($kode)) {
            $this->json_error('Submenu tidak ditemukan.');
            return;
        }

        if (!$this->menu->user_sub_access_exists(
            $username,
            $main_kode,
            $kode
        )) {
            $this->json_error('Akses user tidak ditemukan.');
            return;
        }

        $success = $this->menu->delete_user_sub_access(
            $username,
            $main_kode,
            $kode
        );

        echo json_encode([
            'status'  => $success,
            'message' => $success
                ? 'Akses user berhasil dicabut.'
                : 'Gagal mencabut akses user.'
        ]);
    }
}