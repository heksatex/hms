<table class="table">
    <thead>
    <th>
        Barcode Invalid
    </th>
    <th>
        Cek Error
    </th>
</thead>
<tbody>
    <?php foreach ($data as $key => $value) {
        ?>

        <tr>
            <td>
                <?= $value->barcode ?>
            </td>
            <td>
                <span id="show_err_<?= $value->barcode ?>">
                    <?php
                    if ($value->message === "") {
                        ?>
                        <a href="#" onclick="check('<?= $value->barcode ?>')"> Check Error</a>
                        <?php
                    } else {
                        echo $value->message;
                    }
                    ?>

                </span>
            </td>
        </tr>
    <?php } ?>
</tbody>
</table>