<?php

class Hanger {

    //put your code here

    protected $data = array();
    protected $ci = null;
    protected $isMultiplePage = false;

    public function __construct(string $view = null) {

        $this->view = $view;
        $this->ci = & load_class('Loader', 'core');
    }

    /**
     * 
     * @param string $key
     * @param string $data
     * @param string $title
     */
    public function addData(string $key, $data) {
        $this->isMultiplePage = false;
        $this->data = array_merge($this->data, [$key => $data]);
        return $this;
    }

    public function addDatas(array $data) {
        $this->isMultiplePage = true;
        $this->data[] = $data;
        return $this;
    }

    public function generate() {
        if (!$this->isMultiplePage) {
            $this->data = array($this->data);
        }
        $data = $this->ci->view("print/hanger", ['data' => $this->data], true);
        return $data;
    }
}
