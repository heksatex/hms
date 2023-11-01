<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Print
 *
 * @author RONI
 */
class Prints {

    //put your code here

    protected $view = null;
    protected $image = array();
    protected $data = array();
    protected $ci = null;

    public function __construct(string $view = null) {

        $this->view = $view;
        $this->ci = & load_class('Loader', 'core');
    }

    public function setView(string $path) {
        $this->view = $path;
        return $this;
    }

    /**
     * 
     * @param string $key
     * @param string $imagePath
     * @param string $title
     */
    public function addImage(string $key, string $imagePath, $title) {
        $this->image = array_merge($this->image, [$key => ['title' => $title, 'path' => $imagePath]]);
        return $this;
    }

    /**
     * 
     * @param string $key
     * @param string $data
     * @param string $title
     */
    public function addData(string $key, $data) {
        $this->data = array_merge($this->data, [$key => $data]);
        return $this;
    }

    public function generate() {
        $data = $this->ci->view($this->view, ['image' => $this->image, 'data' => $this->data], true);
        return $data;
    }
}
