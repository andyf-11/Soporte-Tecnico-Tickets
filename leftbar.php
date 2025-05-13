 <!-- BEGIN SIDEBAR -->
 <div class="page-sidebar" id="main-menu">
   <!-- BEGIN MINI-PROFILE -->
   <div class="page-sidebar-wrapper scrollbar-dynamic" id="main-menu-wrapper">
     <div class="user-info-wrapper">
       <div class="profile-wrapper"> <img src="Logo-Gobierno_small.png" alt="Logo inicio" width="60" height="60" /> </div>
       <div class="user-info">
         <div class="greeting" style="font-size:14px;">Bienvenid@</div>
         <div class="username" style="font-size:12px;"><?php echo $_SESSION['name']; ?></div>
         <div class="status" style="font-size:10px;"><a href="#">
             <div class="status-icon green"></div>
             Conectado
           </a></div>
       </div>
     </div>
     <!-- END MINI-PROFILE -->
     <!-- BEGIN SIDEBAR MENU -->
     <p class="menu-title">Opciones <span class="pull-right"><a href="javascript:;"><i class="fa fa-refresh"></i></a></span></p>
     <ul>
       <li class="start"> <a href="dashboard.php"> <i class="icon-custom-home"></i> <span class="title">Dashboard</span> <span class="selected"></span> </a>
       </li>

       <li><a href="get-quote.php"> <span class="fa fa-tasks"></span> Solicitar un Servicio</a></li>
       <li><a href="create-ticket.php"><span class="fa fa-ticket"></span> Crear Ticket</a></li>
       <li><a href="view-tickets.php"><span class="fa fa-ticket"></span> Ver Tickets</a></li>
       <li><a href="profile.php"><span class="fa fa-user"></span> Perfil</a></li>
       <li><a href="change-password.php"><span class="fa fa-file-text-o"></span> Cambiar Contraseña</a></li>

     </ul>