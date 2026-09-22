<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Karyawan extends MY_Controller
{

    protected $path_photo;

	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model("_module");
		$this->load->model("m_karyawan");
        $this->path_photo = "/var/foto/karyawan/";
	}



    public function index()
    {
        $data['total_karyawan']         = $this->m_karyawan->count_all();
        $data['id_dept']                = 'HKAR';
        $this->load->view('hr/v_karyawan', $data);      
    }


    public function load_data()
    {
        $keyword = trim($this->input->post('keyword', true));

        $page = (int) $this->input->post('page', true);

        if ($page < 1) {
            $page = 1;
        }

        $limit = 10;
        $offset = ($page - 1) * $limit;

        $total = $this->m_karyawan->count_all();

        $total_filtered = $this->m_karyawan->count_filtered($keyword);
        $data =  $this->m_karyawan->get_karyawan($keyword,$limit,$offset);

        $total_pages = ceil($total_filtered / $limit);

        echo json_encode([
            'status'         => true,
            'data'           => $data,
            'total'          => $total,
            'total_filtered' => $total_filtered,
            'page'           => $page,
            'total_pages'    => $total_pages
        ]);
    }

    /*
     * =====================================================
     * DETAIL
     * =====================================================
     */
    public function detail($id)
    {
        $data = $this->m_karyawan->get_by_id($id);
        echo json_encode(['status' =>!empty($data),'data' => $data]);
    }

    public function foto($id)
    {
        $data = $this->m_karyawan->get_foto_by_id($id);

        if (empty($data) || empty($data['nrp'])) {
            $this->foto_default();
            return;
        }

        $nrp = trim($data['nrp']);

        // Folder foto
        // $folder = FCPATH . 'upload/hi/';
        $folder = $this->path_photo;

        // Ekstensi yang diperbolehkan
        $extensions = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp'
        ];

        $path = false;

        // Cari file berdasarkan NRP
        foreach ($extensions as $ext) {

            $file = $folder . $nrp . '.' . $ext;

            if (is_file($file) && is_readable($file)) {
                $path = $file;
                break;
            }
        }

        // Tidak ditemukan
        if ($path === false) {
            $this->foto_default();
            return;
        }

        // Deteksi MIME
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($path);

        $allowed = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];

        // File bukan gambar yang valid
        if (!in_array($mime, $allowed, true)) {
            $this->foto_default();
            return;
        }

        /*
        * Bersihkan output buffer
        * supaya tidak ada HTML / whitespace / output lain
        * yang ikut terkirim sebelum binary image.
        */
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Header image
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: public, max-age=86400');

        // Kirim file gambar
        readfile($path);
        exit;
    }
    
    public function foto_thumb($id)
    {
        $data = $this->m_karyawan->get_foto_by_id($id);

        if (empty($data) || empty($data['nrp'])) {
            $this->foto_default();
            return;
        }

        $nrp = trim($data['nrp']);

        // $folder = FCPATH . 'upload/hi/';
        // $thumb_folder = FCPATH . 'upload/hi/thumb/';

        $folder = $this->path_photo;
        $thumb_folder = $this->path_photo . 'thumb/';


        $extensions = ['jpg','jpeg','png','gif','webp'];

        $path = false;
        foreach ($extensions as $ext) {

            $file = $folder . $nrp . '.' . $ext;

            if (is_file($file) && is_readable($file)) {
                $path = $file;
                break;
            }
        }

        if ($path === false) {
            $this->foto_default();
            return;
        }

        /*
        * Nama thumbnail
        */
        $thumb = $thumb_folder . $nrp . '_thumb.jpg';

        /*
        * Jika thumbnail belum ada,
        * buat dari foto asli.
        */
        if (!is_file($thumb)) {

            if (!is_dir($thumb_folder)) {
                mkdir($thumb_folder, 0755, true);
            }

            $this->create_thumbnail($path, $thumb,  300, 300, 80);
        }

        /*
        * Kalau thumbnail gagal dibuat,
        * gunakan foto asli.
        */
        $display_file = is_file($thumb) ? $thumb : $path;

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($display_file);

        if (strpos($mime, 'image/') !== 0) {
            $this->foto_default();
            return;
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($display_file));
        header('Cache-Control: public, max-age=604800');

        readfile($display_file);
        exit;
    }
    

    public function foto_thumb900($id)
    {
        $data = $this->m_karyawan->get_foto_by_id($id);

        if (empty($data) || empty($data['nrp'])) {
            $this->foto_default();
            return;
        }

        $nrp = trim($data['nrp']);

        // $folder = FCPATH . 'upload/hi/';
        // $thumb_folder = FCPATH . 'upload/hi/thumb900/';

        $folder = $this->path_photo;
        $thumb_folder = $this->path_photo . 'thumb900/';

        $extensions = ['jpg','jpeg','png','gif','webp'];

        $path = false;

        foreach ($extensions as $ext) {

            $file = $folder . $nrp . '.' . $ext;

            if (is_file($file) && is_readable($file)) {
                $path = $file;
                break;
            }
        }

        if ($path === false) {
            $this->foto_default();
            return;
        }

        /*
        * Nama thumbnail
        */
        $thumb = $thumb_folder . $nrp . '_thumb.jpg';

        /*
        * Jika thumbnail belum ada,
        * buat dari foto asli.
        */
        if (!is_file($thumb)) {

            if (!is_dir($thumb_folder)) {
                mkdir($thumb_folder, 0755, true);
            }

            $this->create_thumbnail($path, $thumb, 900, 900, 80);
        }

        /*
        * Kalau thumbnail gagal dibuat,
        * gunakan foto asli.
        */
        $display_file = is_file($thumb) ? $thumb : $path;

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($display_file);

        if (strpos($mime, 'image/') !== 0) {
            $this->foto_default();
            return;
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($display_file));
        header('Cache-Control: public, max-age=604800');

        readfile($display_file);
        exit;
    }

    private function create_thumbnail($source,$destination,$max_width = 300, $max_height = 300, $quality = 80) {
        $info = getimagesize($source);

        if (!$info) {
            return false;
        }

        $width  = $info[0];
        $height = $info[1];

        // Buat image dari file asli
        switch ($info['mime']) {

            case 'image/jpeg':
                $image = imagecreatefromjpeg($source);
                break;

            case 'image/png':
                $image = imagecreatefrompng($source);
                break;

            case 'image/gif':
                $image = imagecreatefromgif($source);
                break;

            case 'image/webp':
                $image = imagecreatefromwebp($source);
                break;

            default:
                return false;
        }

        if (!$image) {
            return false;
        }

        /*
        * Hitung ukuran thumbnail
        * dengan mempertahankan aspect ratio
        */
        $ratio = min(
            $max_width / $width,
            $max_height / $height
        );

        // Jangan memperbesar gambar jika lebih kecil dari target
        $ratio = min($ratio, 1);

        $new_width  = max(1, round($width * $ratio));
        $new_height = max(1, round($height * $ratio));

        /*
        * Buat canvas thumbnail
        */
        $thumbnail = imagecreatetruecolor($new_width,$new_height);

        /*
        * Background putih
        * karena output akan menjadi JPG
        */
        $white = imagecolorallocate($thumbnail,255,255,255);

        imagefill($thumbnail,0,0,$white);

        /*
        * Resize
        */
        imagecopyresampled($thumbnail,$image,0,0,0,0,$new_width,$new_height,$width,$height);

        /*
        * Pastikan folder tujuan ada
        */
        $destination_folder = dirname($destination);

        if (!is_dir($destination_folder)) {
            mkdir($destination_folder, 0755, true);
        }

        /*
        * Output SELALU JPG
        */
        $result = imagejpeg($thumbnail,$destination,$quality);

        /*
        * Bersihkan memory
        */
        imagedestroy($image);
        imagedestroy($thumbnail);

        return $result;
    }

    private function foto_default()
    {
        $default  = FCPATH . 'dist/img/user2-160x160.jpg';

        // File default tidak ada
        if (!is_file($default) || !is_readable($default) || filesize($default) <= 0) {
            $this->output->set_status_header(404)->set_output('Foto default tidak ditemukan');
            return;
        }

        // Bersihkan output buffer
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Pastikan file adalah image
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($default);

        if (strpos($mime, 'image/') !== 0) {
            $this->output->set_status_header(500)->set_output('File default bukan gambar');
            return;
        }

        // Header
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($default));
        header('Cache-Control: public, max-age=86400');
        header('Pragma: public');

        // Output gambar
        readfile($default);
        exit;
    }

    private function foto_default1()
    {
        $default = FCPATH . 'dist/img/user2-160x160.jpg';

        if (!is_file($default)) {
            $this->output
                ->set_status_header(404)
                ->set_output('');
            return;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($default);

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($default));
        header('Cache-Control: public, max-age=86400');

        readfile($default);
        exit;
    }

    public function cek_foto()
    {
        $path = '\\10.10.0.1\image\FOTO_KARYAWAN\HI\S039957.jpg';

        echo '<pre>';

        echo "PATH:\n";
        var_dump($path);

        echo "\nFILE EXISTS:\n";
        var_dump(file_exists($path));

        echo "\nIS FILE:\n";
        var_dump(is_file($path));

        echo "\nIS READABLE:\n";
        var_dump(is_readable($path));

        echo '</pre>';
    }


    public function foto_thumb1($id)
    {
        $data = $this->m_karyawan->get_foto_by_id($id);

        if (empty($data) || empty($data['nrp'])) {
            $this->foto_default();
            return;
        }

        $nrp = trim($data['nrp']);

        $folder = FCPATH . 'upload/hi/';
        $thumb_folder = FCPATH . 'upload/hi/thumb/';

        $extensions = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp'
        ];

        $path = false;

        foreach ($extensions as $ext) {

            $file = $folder . $nrp . '.' . $ext;

            if (is_file($file) && is_readable($file)) {
                $path = $file;
                break;
            }
        }

        if ($path === false) {
            $this->foto_default();
            return;
        }

        /*
        * Nama thumbnail
        */
        $thumb = $thumb_folder . $nrp . '.jpg';

        /*
        * Jika thumbnail belum ada,
        * buat dari foto asli.
        */
        if (!is_file($thumb)) {

            if (!is_dir($thumb_folder)) {
                mkdir($thumb_folder, 0755, true);
            }

            $this->create_thumbnail($path, $thumb);
        }

        /*
        * Kalau thumbnail gagal dibuat,
        * gunakan foto asli.
        */
        $display_file = is_file($thumb) ? $thumb : $path;

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($display_file);

        if (strpos($mime, 'image/') !== 0) {
            $this->foto_default();
            return;
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($display_file));
        header('Cache-Control: public, max-age=604800');

        readfile($display_file);
        exit;
    }

    private function create_thumbnail1($source, $destination)
    {
        $info = getimagesize($source);

        if (!$info) {
            return false;
        }

        $width  = $info[0];
        $height = $info[1];

        switch ($info['mime']) {

            case 'image/jpeg':
                $image = imagecreatefromjpeg($source);
                break;

            case 'image/png':
                $image = imagecreatefrompng($source);
                break;

            case 'image/gif':
                $image = imagecreatefromgif($source);
                break;

            case 'image/webp':
                $image = imagecreatefromwebp($source);
                break;

            default:
                return false;
        }

        /*
        * Ukuran maksimal thumbnail
        */
        $max_width  = 300;
        $max_height = 300;

        $ratio = min(
            $max_width / $width,
            $max_height / $height
        );

        $new_width  = max(1, round($width * $ratio));
        $new_height = max(1, round($height * $ratio));

        $thumbnail = imagecreatetruecolor(
            $new_width,
            $new_height
        );

        imagecopyresampled(
            $thumbnail,
            $image,
            0,
            0,
            0,
            0,
            $new_width,
            $new_height,
            $width,
            $height
        );

        imagejpeg(
            $thumbnail,
            $destination,
            80
        );

        imagedestroy($image);
        imagedestroy($thumbnail);

        return true;
    }


    


}
