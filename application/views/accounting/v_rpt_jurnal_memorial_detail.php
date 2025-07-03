<?php
$no = 0;
$kredits = 0;
$debets = 0;
foreach ($data as $key => $value) {
    $no++;
    $debet = 0;
    $kredit = 0;
    if ($value->posisi === "D") {
        $debet = $value->nominal;
        $debets += $debet;
    } else {
        $kredit = $value->nominal;
        $kredits += $kredit;
    }
    ?>
    <tr>
        <td><?= $no ?></td>
        <td><?= $value->periode ?></td>
        <td><?= $value->tanggal_posting ?></td>
        <td><?= $value->kode ?></td>
        <td><?= $value->origin ?></td>
        <td><?= $value->kode_coa ?></td>
        <td><?= $value->nama_coa ?></td>
        <td><?= $value->nama ?></td>
        <td><?= $value->reff_note ?></td>
        <td><?= $value->nama_partner ?></td>
        <td class="text-right"><?= number_format($debet,2) ?></td>
        <td class="text-right"><?= number_format($kredit,2) ?></td>
    </tr>
    <?php
}
if ($no > 0) {
    ?>
    <tr>
        <td></td>
        <td></td>
        <td></td> 
        <td></td> 
        <td></td>
        <td></td> 
        <td></td> 
        <td></td> 
        <td></td>
        <td></td> 
        <td></td> 
        <td></td> 
    </tr>
    <tr>
        <td></td>
        <td></td> 
        <td></td> 
        <td></td>
        <td></td> 
        <td></td>
        <td></td> 
        <td></td> 
        <td></td> 
        <td></td> 
        <td class="text-right"><?= number_format($debets,2) ?></td> 
        <td class="text-right"><?= number_format($kredits,2) ?></td> 
    </tr>
    <?php
}