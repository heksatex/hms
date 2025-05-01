<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of MY_Exceptions
 *
 * @author RONI
 */
class MY_Exceptions extends CI_Exceptions {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->CI = & get_instance();
    }

    public function show_404($page = '', $log_error = TRUE) {
        if (is_cli()) {
            $heading = 'Not Found';
            $message = 'The controller/method pair you requested was not found.<a href="javascript:history.back()">Kembali</a>';
        } else {
            $heading = '404 Page Not Found';
            $message = 'Halaman Tidak Tersedia.(Tidak Ada Akses kehalaman ini) <a href="javascript:history.back()">Kembali</a>';
        }

        // By default we log this, but allow a dev to skip it
        if ($log_error) {
            log_message('error', $heading . ': ' . $page);
        }
//        $url = base_url("errorpage/");
//        $post_data = array('heading' => $heading,"message"=>$message);
//        $ch = curl_init($url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        echo $this->show_error($heading, $message, 'error_404', 404);
        exit(4); // EXIT_UNKNOWN_FILE
    }
}
