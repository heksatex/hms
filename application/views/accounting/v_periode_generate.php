<table class="table table-condesed table-hover rlstable over" width="100%">
    <thead>
    <th>No</th>
    <th>Periode ACC</th>
    <th>Start Periode ACC</th>
    <th>End Periode ACC</th>
    </thead>
    <tbody>
        <?php 
        foreach($data as $key =>$value){
            ?>
        <tr>
            <td>
                <?= $key +1 ?>
            </td>
            <td>
                <?= $value["periode"] ?>
            </td>
            <td>
                <?= $value["start_periode"] ?>
            </td>
            <td>
                <?= $value["end_periode"] ?>
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>