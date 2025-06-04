<table class="table table-condesed table-hover rlstable over" width="100%">
    <thead>
    <th>No</th>
    <th>Periode</th>
    <th>Start Periode</th>
    <th>End Periode</th>
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