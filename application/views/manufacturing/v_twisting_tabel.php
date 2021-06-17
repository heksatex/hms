   <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Photo</th>
                    <th>Description</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($products as $product): ?>
                  <tr>
                    <td width="150">
                      <?php echo $product->name ?>
                    </td>
                    <td>
                      <?php echo $product->price ?>
                    </td>
                    <td>
                      <img src="<?php echo base_url('upload/product/'.$product->image) ?>" width="64" />
                    </td>
                    <td class="small">
                      <?php echo substr($product->description, 0, 120) ?>...</td>
                    <td width="250">
                      <a href="<?php echo site_url('admin/products/edit/'.$product->product_id) ?>"
                       class="btn btn-small"><i class="fa fa-edit"></i> Edit</a>
                      <a onclick="deleteConfirm('<?php echo site_url('admin/products/delete/'.$product->product_id) ?>')"
                       href="#!" class="btn btn-small text-danger"><i class="fa fa-trash"></i> Hapus</a>
                    </td>
                  </tr>
                  <?php endforeach; ?>

                </tbody>
              </table>
  </div>