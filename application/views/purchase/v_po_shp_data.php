<table class="table">
    <thead>
    <th>
        Kode RCV
    </th>
    <th>
        Tanggal
    </th>
    <th>
        Lokasi Asal
    </th>
    <th>
        Lokasi Tujuan
    </th>
    <th>
        Status
    </th>
    <th>
        Reff Note
    </th>
</thead>
<tbody>
    <?php
    foreach ($data as $key => $value) {
        ?>
        <tr>
            <td>
                <?= $value->kode ?>
            </td>
            <td>
                <?= $value->tanggal ?>
            </td>
            <td>
                <?= $value->lokasi_dari ?>
            </td>
            <td>
                <?= $value->lokasi_tujuan ?>
            </td>
            <td>
                <?= $value->status ?>
            </td>
            <td>
                <?= $value->reff_note ?>
            </td>
        </tr>
        <?php
    }
    ?>
</tbody>
</table>