<?php
session_start();
require 'connection.php';
include("dbconnection.php");
include("checklogin.php");
check_login();
// ACTUALIZAR TICKET Y GUARDAR IMAGEN
if (isset($_POST['update'])) {
  $adminremark = $_POST['aremark'];
  $fid = $_POST['frm_id'];
  mysqli_query($con, "UPDATE ticket SET admin_remark='$adminremark', status='closed' WHERE id='$fid'");

  // GUARDAR IMAGEN (si se envió)
  if (
    isset($_FILES['imagen']) &&
    $_FILES['imagen']['error'] == 0 &&
    isset($_POST['ticket_id'])
  ) {
    $ticket_id = $_POST['ticket_id'];
    $og_name = basename($_FILES['imagen']['name']);
    $rutaTemporal = $_FILES['imagen']['tmp_name'];
    $rutaDestino = 'uploads/' . uniqid() . "_" . $og_name;

    // Asegurar que la carpeta existe
    if (!is_dir('uploads')) {
      mkdir('uploads', 0777, true);
    }

    if ($img['uploaded_by'] == 'admin') {
      echo '<strong>Imagen enviada por el administrador:</strong><br>';
    } else {
      echo '<strong>Imagen enviada por el usuario:</strong><br>';
    }
    echo '<img src="/uploads/' . htmlspecialchars($img['route_archivo']) . '" ... >';

    // Mover archivo al servidor
    if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
      // Guardar imagen en la base de datos con 'subido_por' = 'admin'
      $subido_por = 'admin';
      $stmt = mysqli_prepare($con, "INSERT INTO ticket_images (ticket_id, og_name, route_archivo, uploaded_by) VALUES (?, ?, ?, ?)");
      mysqli_stmt_bind_param($stmt, 'ssss', $ticket_id, $og_name, $rutaDestino, $subido_por);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
    }
  }

  echo '<script>alert("Ticket ha sido actualizado correctamente"); location.replace(document.referrer)</script>';
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Usuari@ | Soporte Ticket</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />
  <link href="../assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="../assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css" />
  <link href="../assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="../assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css" />
  <link href="../assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
  <link href="../assets/css/animate.min.css" rel="stylesheet" type="text/css" />
  <link href="../assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" />
  <link href="../assets/css/style.css" rel="stylesheet" type="text/css" />
  <link href="../assets/css/responsive.css" rel="stylesheet" type="text/css" />
  <link href="../assets/css/custom-icon-set.css" rel="stylesheet" type="text/css" />
</head>

<body class="">
  <?php include("header.php"); ?>
  <div class="page-container row">

    <?php include("leftbar.php"); ?>

    <div class="clearfix"></div>
  </div>
  </div>
  <div class="page-content">
    <div id="portlet-config" class="modal hide">
      <div class="modal-header">
        <button data-dismiss="modal" class="close" type="button"></button>
        <h3>Widget Settings</h3>
      </div>
      <div class="modal-body"> Widget settings form goes here </div>
    </div>
    <div class="clearfix"></div>
    <div class="content">
      <ul class="breadcrumb">
        <li>
          <p>Inicio</p>
        </li>
        <li><a href="#" class="active">Ver Ticket</a></li>
      </ul>
      <div class="page-title">
        <h3>Lista de Tickets</h3>
      </div>
      <div class="clearfix"></div>
      <?php $rt = mysqli_query($con, "select * from ticket order by id desc");
      while ($row = mysqli_fetch_array($rt)) {
        ?>
        <div class="row">
          <div class="col-md-12">
            <div class="grid simple no-border">
              <div class="grid-title no-border descriptive clickable">
                <h4 class="semi-bold"><?php echo $row['subject']; ?></h4>
                <p><span class="text-success bold">Ticket #<?php echo $_SESSION['sid'] = $row['ticket_id']; ?></span> -
                  Fecha de creación <?php echo $row['posting_date']; ?>
                  <span class="label label-important"><?php echo $row['status']; ?></span>
                </p>
                <div class="actions"> <a class="view" href="javascript:;"><i class="fa fa-angle-down"></i></a> </div>
              </div>
              <div class="grid-body  no-border" style="display:none">
                <div class="post">
                  <div class="user-profile-pic-wrapper">
                    <div class="user-profile-pic-normal"> <img width="35" height="35"
                        data-src-retina="../assets/img/user.png" data-src="../assets/img/user.png"
                        src="../assets/img/user.png" alt=""> </div>
                  </div>
                  <div class="info-wrapper">
                    <div class="info"><?php echo $row['ticket']; ?> </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="clearfix"></div>
                </div>
                <br>
                <!-- Mostrar imagen adjunta -->
                <?php
                $ticketId = $row['ticket_id'];
                $resImg = mysqli_query($con, "SELECT * FROM ticket_images WHERE ticket_id = '$ticketId'");
                if (mysqli_num_rows($resImg) > 0) {
                  while ($img = mysqli_fetch_assoc($resImg)) {
                    $quien = ($img['uploaded_by'] === 'admin') ? 'Administrador' : 'Usuario';
                    echo '<div class="form-group">';
                    echo '<label>Imagen adjunta por: ' . $quien . '</label><br>';
                    echo '<img src="' . htmlspecialchars($img['route_archivo']) . '" style="max-width: 300px; margin-top: 10px; border: 1px solid #ccc; padding: 5px;">';
                    echo '</div>';
                  }
                } else {
                  echo '<p><em>No se adjuntó imagen para este ticket.</em></p>';
                }
                ?>
                <div class="form-actions">
                  <div class="post col-md-12">
                    <div class="user-profile-pic-wrapper">
                      <div class="user-profile-pic-normal"> <img width="35" height="35"
                          src="Logo-Gobierno-en-Vertical (1).png" alt=""> </div>
                    </div>
                    <div class="info-wrapper">
                      <form name="adminr" method="post" enctype="multipart/form-data">
                        <br>
                        <textarea name="aremark" cols="50" rows="4"
                          required="true"><?php echo $row['admin_remark']; ?></textarea>
                        <hr>
                        <div class="form-group_admin">
                        </div>
                        <input type="hidden" name="ticket_id" value="<?php echo $row['ticket_id']; ?>">
                        <label>Adjuntar imagen:</label>
                        <input type="file" name="imagen">
                        <p class="small-text">
                          <input name="update" type="submit" class="txtbox1" id="Update" value="Actualizar" size="40" />
                          <input name="frm_id" type="hidden" id="frm_id" value="<?php echo $row['id']; ?>" />
                      </form>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="clearfix"></div>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>


    </div>
  </div>
  </div>


  </div>
  </div>
  </div>

  </div>
  </div>
  </div>
  </div>
  </div>

  </div>
  <!-- END CONTAINER -->
  <!-- BEGIN CORE JS FRAMEWORK-->
  <script src="../assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
  <script src="../assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
  <script src="../assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
  <script src="../assets/plugins/breakpoints.js" type="text/javascript"></script>
  <script src="../assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
  <script src="../assets/plugins/jquery-block-ui/jqueryblockui.js" type="text/javascript"></script>
  <!-- END CORE JS FRAMEWORK -->
  <!-- BEGIN PAGE LEVEL JS -->
  <script src="../assets/plugins/pace/pace.min.js" type="text/javascript"></script>
  <script src="../assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
  <script src="../assets/plugins/jquery-numberAnimate/jquery.animateNumbers.js" type="text/javascript"></script>
  <script src="../assets/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script>
  <script src="../assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>
  <script src="../assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
  <!-- END PAGE LEVEL PLUGINS -->
  <script src="../assets/js/support_ticket.js" type="text/javascript"></script>
  <!-- BEGIN CORE TEMPLATE JS -->
  <script src="../assets/js/core.js" type="text/javascript"></script>
  <script src="../assets/js/chat.js" type="text/javascript"></script>
  <script src="../assets/js/demo.js" type="text/javascript"></script>
  <!-- END CORE TEMPLATE JS -->
</body>

</html>