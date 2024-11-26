<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Check whether the site is offline or not.
*
*/
class Maintenance_hook
{
    public function __construct(){
        log_message('debug','Accessing maintenance hook!');
    }
    
    public function offline_check(){
        if(file_exists(APPPATH.'config/config.php')){
            include(APPPATH.'config/config.php');

            $tgl_mt = strtotime(date('Y-m-t 23:50:00'));
            $tgl_mt_ = date("Y-m-t 23:59:59");
            $tgl_mt2 = strtotime($tgl_mt_);
            
            $tgl_now = strtotime(date("Y-m-d H:i:s"));
            $tgl_akhir_bln = date("Y-m-t",time());
            $tgl_hari_ini  = date("Y-m-d");
            $akhir_bulan   = FALSE;
            if($tgl_akhir_bln == $tgl_hari_ini){
                $akhir_bulan = TRUE;
            }
            if(isset($config['maintenance_mode']) && (($tgl_now > $tgl_mt && $tgl_now < $tgl_mt2) OR  $config['maintenance_mode'] == TRUE)){
            // if(isset($config['maintenance_mode']) && $config['maintenance_mode'] == TRUE){
                log_message('error','Accessing maintenance!');
                $date_finish_mt = date("M d, Y H:i:s", strtotime($tgl_mt_));
                $last_day_month = $akhir_bulan;
                include(APPPATH.'views/maintenance.php');
                exit;
            }
        }
    }
}