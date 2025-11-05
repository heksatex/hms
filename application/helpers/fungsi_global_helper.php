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
        if (is_array($value)) {

            $hasil .= " " . ($key + 1) . " " . logArrayToString($seperator, $value, $indikatorVal);
        } else {
            $hasil = implode($seperator, array_map(
                            function ($v, $k) use ($indikatorVal) {
                                return sprintf("%s{$indikatorVal}%s", $k, $v);
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
    return "http://202.150.151.163:8880/" . $param;
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

?>