<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Token
 *
 * @author RONI
 */
class Token {

    //put your code here
    protected $modul = "";
    protected $periode = "";
    protected $prefix = "";
    protected $format = "";
    protected $increment = 1;
    protected $generate = false;
    protected $model;
    protected $changePrefix = false;

    public function __construct() {
        $this->model = & get_instance();
        $this->model->load->model('m_Token');
    }

    public function noUrut(string $modul, string $periode, $increment = false) {
        $this->modul = $modul;
        $this->periode = $periode;
        $this->increment = $increment;
        return $this;
    }

    public function generate(string $prefix = "", string $format = "") {
        $this->generate = true;
        $this->format = $format;
        $this->prefix = $prefix;
        return $this;
    }

    public function prefixAdd(string $prefix) {
        $this->prefix .= $prefix;
        $this->changePrefix = true;
        return $this;
    }

    public function get() {
        $data = $this->model->m_Token->getToken($this->modul, $this->periode, $this->prefix, $this->format, $this->increment);
        if (!$data) {
            return false;
        }
        if ($this->generate) {
            if ($this->changePrefix) {
                return sprintf($this->prefix . $this->format, $data);
            }
            return sprintf($this->prefix . $this->periode . $this->format, $data);
        }
        return $data;
    }
}
