<?php
$persenProd = 0;
$persenDye = 0;
$persenFin = 0;
foreach ($data as $key => $value) {
    $persenProd = (($data_kedua[$key]["qty_cacat_grade_a_DF"] + ($data_kedua[$key]["qty_total_grade_a_GJD"] ?? 0)) / ($value->total_qty ?? 0)) * 100;
    $persenDye = (($data_kedua[$key]["qty_cacat_grade_a_TF"] + ($data_kedua[$key]["qty_total_grade_a_GJD"] ?? 0)) / ($value->total_qty ?? 0)) * 100;
    $persenFin = (($data_kedua[$key]["qty_cacat_grade_a_TD"] + ($data_kedua[$key]["qty_total_grade_a_GJD"] ?? 0)) / ($value->total_qty ?? 0)) * 100;
    ?>
    <tr>
        <td><?= substr($value->nama_produk, 0, 50) ?></td>
        <td><?= $value->total_qty ?? 0 ?></td>
        <td><?= $data_kedua[$key]["qty_total_grade_a_GJD"] ?></td>
        <td><?= $data_kedua[$key]["qty_cacat_grade_a_DF"] + ($data_kedua[$key]["qty_total_grade_a_GJD"] ?? 0) ?></td>
        <td><?= round($persenProd,2) ?></td>
        <td><?= $data_kedua[$key]["qty_cacat_grade_a_TF"] + ($data_kedua[$key]["qty_total_grade_a_GJD"] ?? 0) ?></td>
        <td><?= round($persenDye,2) ?></td>
        <td><?= $data_kedua[$key]["qty_cacat_grade_a_TD"] + ($data_kedua[$key]["qty_total_grade_a_GJD"] ?? 0) ?></td>
        <td><?= round($persenFin,2) ?></td>
    </tr>
    <?php
}
