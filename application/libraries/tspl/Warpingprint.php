<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH . 'vendor/autoload.php';

class Warpingprint {

    protected $sizePageIncWidth = 4;
    protected $sizePageIncHeight = 2;
    protected $gap = 0.11;
    protected $lots = [];
    protected $reffPicking = [];
    protected $produk = [];
    protected $textPrint = "";
    protected $reffnote = [];

    public function __construct() {
        $this->CI = & get_instance();
    }

    public function setup($pageWInch, $pageHInch, $gap) {
        $this->sizePageIncWidth = $pageWInch;
        $this->sizePageIncHeight = $pageHInch;
        $this->gap = $gap;
        return $this;
    }

    public function setLot($lot) {
        $this->lots[] = $lot;
        return $this;
    }

    public function setReffPicking($reff) {
        $this->reffPicking[] = $reff;
        return $this;
    }

    public function setProduk($produk) {
        $this->produk[] = $produk;
        return $this;
    }

    public function setNote($note) {
        $this->reffnote[] = $note;
        return $this;
    }

//123456789123456789123456789123456789123456789
    public function print() {
        try {
            $this->textPrint .= "SIZE {$this->sizePageIncWidth}, {$this->sizePageIncHeight}\r\n";
            $this->textPrint .= "GAP {$this->gap}\r\n";
            $this->textPrint .= "DIRECTION 0 \r\nREFERENCE 0,0 \r\nCLS \r\n";

            $posisiX = 600;
            $posisiY = 10;
            foreach ($this->lots as $key => $value) {
                $lots = str_split($value, 37);
                foreach ($lots as $k => $vls) {
                    $vls = str_pad(trim($vls), 37, " ", STR_PAD_BOTH);
                    $this->textPrint .= "TEXT {$posisiX},{$posisiY},\"3\",90,1,2,\"{$vls}\" \r\n";
                    $posisiX -= 45;
                }
                $posisiX -= 25;
                $rf = str_pad("Reff Picking : ", 39, " ", STR_PAD_BOTH);
                $this->textPrint .= "TEXT {$posisiX},{$posisiY},\"2\",90,1,1,\"{$rf}\"  \r\n";
                $posisiX -= 30;

                $rrff = str_split($this->reffPicking[$key], 39);
                foreach ($rrff as $k => $vls) {
                    $vls = str_pad(trim($vls), 39, " ", STR_PAD_BOTH);
                    $this->textPrint .= "TEXT {$posisiX},{$posisiY},\"2\",90,1,1,\"{$vls}\"  \r\n";
                    $posisiX -= 25;
                }
                $prod = str_split($this->produk[$key], 39);
                $posisiX -= 10;
                foreach ($prod as $k => $vls) {
                    $vls = str_pad(trim($vls), 39, " ", STR_PAD_BOTH);
                    $this->textPrint .= "TEXT {$posisiX},{$posisiY},\"2\",90,1,1,\"{$vls}\" \r\n";
                    $posisiX -= 25;
                }
                $notes = str_split(($this->reffnote[$key] ?? ""), 39);
                $posisiX -= 10;
                foreach ($notes as $k => $vls) {
                    $vls = str_pad(trim($vls), 39, " ", STR_PAD_BOTH);
                    $this->textPrint .= "TEXT {$posisiX},{$posisiY},\"2\",90,1,1,\"{$vls}\" \r\n";
                    $posisiX -= 25;
                }

                $posisiX = 300;
                $next = $key + 1;
                if ($next % 2 === 0) {
                    if (isset($this->lots[$next])) {
                        $posisiX = 600;
                        $this->textPrint .= "PRINT 1 \r\nCLS \r\n";
                    } else {
                        $this->textPrint .= "PRINT 1\r\n";
                    }
                } else {
                    if (!isset($this->lots[$next])) {
                        $this->textPrint .= "PRINT 1\r\n";
                    }
                }
            }
//            log_message("error", $this->textPrint);
            $this->_print($this->textPrint);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    protected function _print($text) {
        $this->CI->config->load('additional');
        $printers = $this->CI->session->userdata('printer');
        if ($printers === null) {
            throw new \exception("Printer Direct belum ditentukan, silakan pilih pada tab atas", 500);
        }
        $printers = json_decode($printers);
        $client = new GuzzleHttp\Client();
        $resp = $client->request("POST", $this->CI->config->item('url_web_print_tspl'), [
            "form_params" => [
                "data" => $text,
                "printer" => "\\\\{$printers->ip_share}\\{$printers->nama_printer_share}"
            ]
        ]);
        $rawBody = $resp->getBody()->getContents();
//        log_message("error", $rawBody);

        return $rawBody;
    }
}
