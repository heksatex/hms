<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Dompdf
 *
 * @author RONI
 */
require_once APPPATH . '/third_party/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class Dompdflib {

    public function generate($html) {
        $options = new Options();
        $options->set('defaultFont', 'Courier');
        $options->set(
                'isRemoteEnabled', true
        );
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper("a4");
        $dompdf->render();
        $dompdf->stream("testse.pdf", array("Attachment" => 0));
//        exit();
    }
}
