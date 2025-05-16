<?php
session_start();
require 'connection.php';
include("dbconnection.php");
include("checklogin.php");
check_login();

if (isset($_POST['send'])) {
    $count_my_page = ("hitcounter.txt");
    $hits = file($count_my_page);
    $hits[0]++;
    $fp = fopen($count_my_page, "w");
    fputs($fp, "$hits[0]");
    fclose($fp);
    $tid = $hits[0];
    $email = $_SESSION['login'];
    $subject = $_POST['subject'];
    $tt = $_POST['tasktype'];
    $priority = $_POST['priority'];
    $ticket = $_POST['description'];
    $st = "Open";
    $pdate = date('Y-m-d');

    // Subir imagen (si hay)
    $img_name = "";
    $target_file = "";
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $img_name = basename($_FILES['imagen']['name']);
        $target_dir = "uploads/";
        $target_file = $target_dir . $img_name;

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
            echo "<script>alert('Error al subir la imagen.');</script>";
            $target_file = ""; // Asegura que no se guarde nada si falla
        }
    }

    // Insertar ticket con la ruta de imagen si se subió
    $a = mysqli_query($con, "INSERT INTO ticket(ticket_id,email_id,subject,task_type,prioprity,ticket,status,posting_date,image)  
        VALUES('$tid','$email','$subject','$tt','$priority','$ticket','$st','$pdate','$target_file')");

    if ($a) {
        // También registrar en ticket_images si se subió imagen
        if (!empty($target_file)) {
            $stmt_img = mysqli_prepare($con, "INSERT INTO ticket_images (ticket_id, og_name, route_archivo) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt_img, 'sss', $tid, $img_name, $target_file);
            mysqli_stmt_execute($stmt_img);
            mysqli_stmt_close($stmt_img);
        }

        echo "<script>alert('Ticket Registrado Correctamente'); location.replace(document.referrer)</script>";
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>SPE Crear Ticket</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="" name="description" />
    <meta content="" name="author" />

    <link href="assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/animate.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/custom-icon-set.css" rel="stylesheet" type="text/css" />
</head>

<body class="">
    <?php include("header.php"); ?>
    <div class="page-container row-fluid">
        <?php include("leftbar.php"); ?>
        <div class="clearfix"></div>
        <!-- END SIDEBAR MENU -->
    </div>
    </div>
    <!-- END SIDEBAR -->
    <!-- BEGIN PAGE CONTAINER-->
    <div class="page-content">
        <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
        <div id="portlet-config" class="modal hide">
            <div class="modal-header">
                <button data-dismiss="modal" class="close" type="button"></button>
                <h3>Widget Settings</h3>
            </div>
            <div class="modal-body"> Widget settings form goes here </div>
        </div>
        <div class="clearfix"></div>
        <div class="content">
            <div class="page-title">
                <h3>Crear ticket</h3>
                <div class="row">
                    <div class="col-md-12">

                        <form class="form-horizontal" name="form1" method="post" action="" onSubmit="return valid();" enctype="multipart/form-data">
                            <div class="panel panel-default">

                                <div class="panel-body bg-white">
                                    <?php if (isset($_SESSION['msg1'])) : ?>
                                        <p align="center" style="color:#FF0000"><?= $_SESSION['msg1']; ?><?= $_SESSION['msg1'] = ""; ?></p>
                                    <?php endif; ?>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Asunto</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="subject" id="subject" value="" required class="form-control" />
                                            </div>

                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Tipo de Tarea</label>
                                        <div class="col-md-6 col-xs-12">
                                            <select name="tasktype" class="form-control select" required>
                                                <option value="">Seleccionar</option>
                                                <option>Incidente Lógica</option>
                                                <option>Fallo a Nivel de Servidor</option>
                                                <option>Error capa de aplicación</option>

                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Prioridad</label>
                                        <div class="col-md-6 col-xs-12">
                                            <select name="priority" class="form-control select">
                                                <option value="">Seleccionar</option>
                                                <option value="Importante">Importante</option>
                                                <option value="Urgente-(Problema Funcional)">Urgente (Problema Funcional)</option>
                                                <option value="No-Urgente">No Urgente</option>
                                                <option value="Pregunta">Pregunta</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Descripción</label>
                                        <div class="col-md-6 col-xs-12">
                                            <textarea name="description" required class="form-control" rows="5"></textarea>

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label"> Subir Imágen</label>
                                        <div class="col-md-6 col-xs-12">
                                            <input type="file" class="form-control-file" name="imagen" id="imagen" accept="image/*">
                                            <img src="<?= $row['route_archivo'] ?>" alt="" style="max-width: 300px;">
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="panel-footer">
                                <button class="btn btn-default">Resetear</button>
                                <input type="submit" value="Enviar" name="send" class="btn btn-primary pull-right">
                            </div>
                    </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
    </div>

    </div>
    <script src="assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="assets/plugins/breakpoints.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-block-ui/jqueryblockui.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
    <script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-numberAnimate/jquery.animateNumbers.js" type="text/javascript"></script>
    <script src="assets/js/core.js" type="text/javascript"></script>
    <script src="assets/js/chat.js" type="text/javascript"></script>
    <script src="assets/js/demo.js" type="text/javascript"></script>

</body>

</html>