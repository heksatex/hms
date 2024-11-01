<?php

defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Service
 *
 * @author RONI
 */
require FCPATH . 'vendor/autoload.php';

use Mpdf\Mpdf;

class Service extends CI_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->load->model("m_gtp");
        $this->load->library("wa_message");
        $this->config->load('additional');
    }

    public function generate_gtp() {
        try {
            $query = [
                "Finish_Goods_Over_14d" => [
                    "where" => [
                        "lokasi" => "GJD/Stock",
                        "category" => "14d"
                    ],
                    "group" => ["corak", "customer_name", "lebar_jadi"],
                ],
                "Finish_Goods_Over_30d" => [
                    "where" => [
                        "lokasi" => "GJD/Stock",
                        "category" => "30d"
                    ],
                    "group" => ["corak", "customer_name", "lebar_jadi"]
                ],
                "Finish_Goods_Over_90d" => [
                    "where" => [
                        "lokasi" => "GJD/Stock",
                        "category" => "90d"
                    ],
                    "group" => ["corak", "customer_name", "lebar_jadi"]
                ],
                "Greige_Over_14d" => [
                    "where" => [
                        "lokasi" => "GRG/Stock",
                        "category" => "14d"
                    ],
                    "group" => ["corak", "customer_name", "lebar_jadi"]
                ],
                "Greige_Over_30d" => ["where" => [
                        "lokasi" => "GRG/Stock",
                        "category" => "30d"
                    ],
                    "group" => ["corak", "customer_name", "lebar_jadi"]],
                "Greige_Over_90d" => ["where" => [
                        "lokasi" => "GRG/Stock",
                        "category" => "90d"
                    ],
                    "group" => ["corak", "customer_name", "lebar_jadi"]]
            ];
            $now = date("Y-m-d");
            $model = new $this->m_gtp;
            $sales = $model->setSelects(["nama_sales_group,report_date"])->setOrder(["report_date" => "desc"])
                    ->setWhereRaw("nama_sales_group not in ('','RONALD')")
                    ->setWheres(["DATE(report_date)" => $now])
                    ->setGroups(["nama_sales_group"])
                    ->getData();
            $datas = [];
//            $_POST['length'] = 50;
//            $_POST['start'] = 0;
            $url = "dist/storages/report/gtp";
            if (!is_dir(FCPATH . $url)) {
                mkdir(FCPATH . $url, 0775, TRUE);
            }
            $groups = $this->config->item('additional_gtp_bc_group') ?? [];
            ini_set("pcre.backtrack_limit", "50000000");
            foreach ($sales as $keys => $values) {
                $datas = [];
//                $date = date("Y-m-d", strtotime($values->report_date));
                $dateSave = date("Ymd", strtotime($values->report_date));
                $datas[$values->nama_sales_group] = [];
                foreach ($query as $key => $value) {
                    $qr = new $this->m_gtp;
                    $datas[$values->nama_sales_group][$key] = $qr->setOrder(["lokasi,corak,category"])->setWheres($value["where"])
                            ->setWheres(["nama_sales_group" => $values->nama_sales_group])->setWhereRaw("DATE(report_date) = '{$now}'")
                            ->setSelects(["corak,uom,uom2,qty as total_qty,qty2 as total_qty2,lot as total_data,jml_warna as total_warna,customer_name,lebar_jadi"])
                            ->getData();
                }
                $footer = "<table name='footer' width=\"1000\">
           <tr>
             <td style='width: 33.33%;font-size: 18px; padding-bottom: 20px;' align=\"left\">GOODS To PUSH</td>
             <td style='width: 33.33%;font-size: 18px; padding-bottom: 20px;' align=\"center\">{$values->nama_sales_group}</td>
             <td style='width: 33.33%;font-size: 18px; padding-bottom: 20px;' align=\"right\">{PAGENO}</td>
           </tr>
         </table>";
                $dates = date("Y-M-d H:i:s", strtotime($values->report_date));
                $html = $this->load->view('service/v_gtp', ['data' => $datas, 'date' => $dates], true);
                $mpdf = new Mpdf(['tempDir' => FCPATH . '/tmp']);

                $mpdf->WriteHTML($html);
                $mpdf->SetHTMLFooter($footer);
                $pathFile = $url . "/{$dateSave}_gtp_{$values->nama_sales_group}.pdf";
                $mpdf->Output(FCPATH . $pathFile, "F");

                $wa = new $this->wa_message;
                if (is_file(FCPATH . $pathFile)) {
                    $nm = date("Y-M-d", strtotime($values->report_date));
                    $wa->sendMessageToGroup('service_gtp', ["{message}" => "GOODS To PUSH *{$values->nama_sales_group}* \n {$nm}"], $groups)
                            ->setFile(getIpPubic("hms_staging_2/" . $pathFile))
                            ->setMentions([])->setFooter('footer_hms')->send();
                } else {
                    $wa->sendMessageToGroup('error', ["{message}" => "File GTP sales *{$values->nama_sales_group}* Tidak terbuat."], ['IT WDT'])
                            ->setMentions([])->setFooter('footer_hms')->send();
                }
            }

//            $html = $this->load->view('service/v_gtp', ['data' => $datas]);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("data" => [])));
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            ini_set("pcre.backtrack_limit", "1000000");
        }
    }
}
