
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo "HMS : Log In"; ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?php echo base_url('bootstrap/css/bootstrap.min.css') ?>">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url('dist/fa/css/font-awesome.min.css') ?>">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url('dist/ionicons/css/ionicons.min.css') ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url('dist/css/AdminLTE.css') ?>">
  <!-- AdminLTE Skins. Choose a skin from the css/skins -->
  <link rel="stylesheet" href="<?php echo base_url('dist/css/skins/_all-skins.min.css') ?>">
  <!-- Favicon -->
  <link rel="shortcut icon"  href="<?php echo base_url('dist/img/favicon_heksa.ico') ?>">

  <style>
    body {
      background-color:#eee;
    }
    .row {
      margin:100px auto;
      width:300px;
      text-align:center;
    }
    .login {
      background-color:#fff;
      padding:20px;
      margin-top:20px;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="row">            
      <div class="login-logo">
        <a href="index.php"><b>HMS </b></a>
          <br>
          Log In
      </div>

      <div class="login">
         <?php if ($this->session->flashdata('gagal')): ?>
          <div class="alert alert-danger" role="alert">
            <?php echo $this->session->flashdata('gagal'); ?>
          </div>
          <?php endif; ?>

        <form role="form" action="<?php echo base_url('login/aksi_login'); ?>" method="post">
          <div class="form-group">
            <input type="text" name="username" class="form-control" placeholder="Username" autocomplete="off" required autofocus />
          </div>
          <div class="form-group">
            <input type="password" name="password" class="form-control" placeholder="Password" autocomplete="off" required autofocus />
          </div>          
          <div class="form-group">
            <input type="submit" name="login" class="btn btn-primary btn-block" value="Log In" />                        
          </div>
        </form>
      </div>
    </div>
  </div>

<!-- jQuery 2.2.3 -->
<script src="<?php echo base_url('plugins/jQuery/jquery-2.2.3.min.js') ?>"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo base_url('bootstrap/js/bootstrap.min.js') ?>"></script>
<!-- SlimScroll -->
<script src="<?php echo base_url('plugins/slimScroll/jquery.slimscroll.min.js') ?>"></script>
<!-- FastClick -->
<script src="<?php echo base_url('plugins/fastclick/fastclick.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url('dist/js/app.min.js') ?>"></script>

</body>
</html>
