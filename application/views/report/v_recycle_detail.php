<?php
$page = ($page > 0) ? ($page - 1) : 0;
$no = ($page * $perpage);
foreach ($header as $key => $value) {
    $no++;
    $datago = (object) $go[$key];
    $datago = explode("#", $datago->go);
    ?>
    <tr>
        <td><?= $no ?></td>
        <td><?= $value->kp ?></td>
        <td><?= $value->qty ?></td>
        <td><?= $value->uom ?></td>
        <td><?= $value->qty2 ?></td>
        <td><?= $value->uom2 ?></td>
        <td><?= $datago["0"] ? (($datago["0"] === "N/A") ? "" : $datago["0"]) : "" ?></td>
        <td><?= $datago["1"] ?? "" ?></td>
        <td><?= $datago["2"] ?? "" ?></td>
        <td><?= $datago["3"] ?? "" ?></td>
        <td><?= $datago["4"] ?? "" ?></td>
        <td><?= $datago["5"] ?? "" ?></td>
        <?php
        foreach ($value->detail as $keys => $values) {
            $values = (object) $values;
            $details = explode("#", $values->dt);
            ?>
            <?php
            if ($keys === 0) {
                ?>
                <td>
                    <?= $details[0] ?? "" ?>
                </td>
                <td>
                    <?= $details[1] ?? "" ?>
                </td>
                <td>
                    <?= isset($details[2]) ? str_replace(";", '"', $details[2]) : "" ?>
                </td>
                <?php
            }
            ?>
            <td>
                <?= $details[3] ?? "" ?>
            </td>
            <td>
                <?= $details[4] ?? "" ?>
            </td>
            <td>
                <?= $details[5] ?? "" ?>
            </td>
            <td>
                <?= $details[6] ?? "" ?>
            </td>
            <td>
                <?= $details[7] ?? "" ?>
            </td>
            <td>
                <?= $details[8] ?? "" ?>
            </td>
            <?php
        }
        ?>
    </tr>
    <?php
}
?>