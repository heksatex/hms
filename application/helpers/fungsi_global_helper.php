<?php

if (!defined("BASEPATH"))
    exit("No direct script access allowed");

function tgl_indo($tanggal) {
    $bulan = array(
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );
    $pecahkan = explode('-', $tanggal);

    // variabel pecahkan 0 = tanggal
    // variabel pecahkan 1 = bulan
    // variabel pecahkan 2 = tahun

    return $pecahkan[0] . ' ' . $bulan[(int) $pecahkan[1]] . ' ' . $pecahkan[2];
}

function tgl_indo2($tanggal) {
    $bulan = array(
        1 => 'Jan',
        'Feb',
        'Mar',
        'Apr',
        'Mei',
        'Jun',
        'Jul',
        'Ags',
        'Sep',
        'Okt',
        'Nov',
        'Des'
    );
    $pecahkan = explode('-', $tanggal);

    // variabel pecahkan 0 = tanggal
    // variabel pecahkan 1 = bulan
    // variabel pecahkan 2 = tahun

    return $pecahkan[0] . '-' . $bulan[(int) $pecahkan[1]] . '-' . $pecahkan[2];
}

function tgl_eng($tanggal) {
    $bulan = array(
        1 => 'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    );
    $pecahkan = explode('-', $tanggal);

    // variabel pecahkan 0 = tanggal
    // variabel pecahkan 1 = bulan
    // variabel pecahkan 2 = tahun

    return $pecahkan[0] . ' ' . $bulan[(int) $pecahkan[1]] . ' ' . $pecahkan[2];
}

function bln_indo($tanggal) {
    $bulan = array(
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );
    $pecahkan = explode('-', $tanggal);

    return $bulan[(int) $pecahkan[1]] . ' ' . $pecahkan[2];
}

function logArrayToString(string $seperator, array $data, string $indikatorVal = "=") {
    $hasil = "";
    foreach ($data as $key => $value) {
        if(is_object($value)) {
            $value = (array)$value;
        }
        if (is_array($value)) {

            $hasil .= " " . ($key + 1) . " " . logArrayToString($seperator, $value, $indikatorVal);
        } else {
            $hasil = implode($seperator, array_map(
                            function ($v, $k) use ($indikatorVal) {
                                return sprintf("%s{$indikatorVal}%s", addslashes($k), addslashes($v));
                            },
                            $data,
                            array_keys($data)
            ));
        }
    }
    return $hasil;
}

function getClientIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function getRomawi($bln) {

    switch ($bln) {

        case 1:

            return "I";

            break;

        case 2:

            return "II";

            break;

        case 3:

            return "III";

            break;

        case 4:

            return "IV";

            break;

        case 5:

            return "V";

            break;

        case 6:

            return "VI";

            break;

        case 7:

            return "VII";

            break;

        case 8:

            return "VIII";

            break;

        case 9:

            return "IX";

            break;

        case 10:

            return "X";

            break;

        case 11:

            return "XI";

            break;

        case 12:

            return "XII";

            break;
    }
}

function getIpPubic(string $param) {
    return "http://157.20.244.218:8880/" . $param;
}

function searchOnArray(array $data, string $keySearch, string $valueSearch) {
    $hasil = [];
    foreach ($data as $key => $value) {
        if (gettype($value) !== "string") {
            $hasil = searchOnArray((array) $value, $keySearch, $valueSearch);
            if (count($hasil) > 0)
                break;
        } else {
            if (strtolower($key) === strtolower($keySearch) && strtolower($value) === strtolower($valueSearch)) {
                $hasil = $data;
                continue;
            }
        }
    }
    return $hasil;
}

function Kwitansi($x) {
    $bilangan = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
    if ($x < 12) {
        return " " . $bilangan[$x];
    } elseif ($x < 20) {
        return Kwitansi($x - 10) . " Belas";
    } elseif ($x < 100) {
        return Kwitansi($x / 10) . " Puluh" . Kwitansi($x % 10);
    } elseif ($x < 200) {
        return " Seratus" . Kwitansi($x - 100);
    } elseif ($x < 1000) {
        return Kwitansi($x / 100) . " Ratus" . Kwitansi($x % 100);
    } elseif ($x < 2000) {
        return " Seribu" . Kwitansi($x - 1000);
    } elseif ($x < 1000000) {
        return Kwitansi($x / 1000) . " Ribu" . Kwitansi($x % 1000);
    } elseif ($x < 1000000000) {
        return Kwitansi($x / 1000000) . " Juta" . Kwitansi($x % 1000000);
    }
}

function KwitansiDesimal($x) {
    $rst = "";
    $bilangan = array("Nol", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan");
    $char = str_split($x);
    if (end($char) === "0") {
        unset($char[count($char) - 1]);
        return KwitansiDesimal(join("", $char));
    }
    foreach ($char as $value) {
        $rst .= " {$bilangan[$value]}";
    }

    return $rst;
}

function getBrowser() 
{ 
    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
    
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Internet Explorer'; 
        $ub = "MSIE"; 
    } 
    elseif(preg_match('/Firefox/i',$u_agent)) 
    { 
        $bname = 'Mozilla Firefox'; 
        $ub = "Firefox"; 
    } 
    elseif(preg_match('/Chrome/i',$u_agent)) 
    { 
        $bname = 'Google Chrome'; 
        $ub = "Chrome"; 
    } 
    elseif(preg_match('/Safari/i',$u_agent)) 
    { 
        $bname = 'Apple Safari'; 
        $ub = "Safari"; 
    } 
    elseif(preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Opera'; 
        $ub = "Opera"; 
    } 
    elseif(preg_match('/Netscape/i',$u_agent)) 
    { 
        $bname = 'Netscape'; 
        $ub = "Netscape"; 
    } 
    
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
    
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
    
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
} 

?>