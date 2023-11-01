<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Code;

/**
 * Description of Dimension
 *
 * @author RONI
 */
 class Dimension {

//put your code here


    private $height;
    private $width;

    public function __construct(int $height, int $width) {
        $this->$height = $height;
        $this->$width = $width;
    }

    /**
     * @return mixed
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     * @param mixed $height
     */
    public function setHeight($height) {
        $this->height = $height;
    }

    /**
     * @return mixed
     */
    public function getWidth() {
        return $this->width;
    }

    /**
     * @param mixed $width
     */
    public function setWidth($width) {
        $this->width = $width;
    }
}
