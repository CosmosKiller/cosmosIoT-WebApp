<?php

session_start();
$logged = $_SESSION['logged'];

if(!$logged)
{
  echo "Ingreso no autorizado";
  die();
}

$alias = "";
$serial = "";
$user_id = "";
$msg = "";
$msg_color = "";
$user_id = $_SESSION['user_id'];

//DB connection
$conn = mysqli_connect($_ENV['MYSQL_HOST'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD'], $_ENV['MYSQL_DB']);

if ($conn==false)
{
  echo "Hubo un problema al conectarse a María DB";
  die();
}

if (isset($_POST['id_to_delete']) && $_POST['id_to_delete'] != "")
{
  $id_to_delete = strip_tags($_POST['id_to_delete']);
  $conn->query("DELETE FROM `admin_cosmosiot`.`devices` WHERE  `devices_id`=$id_to_delete");
}

if (isset($_POST['alias']) && isset($_POST['serial']))
{
  $alias = strip_tags($_POST['alias']);
  $serial = strip_tags($_POST['serial']);

  if (!is_numeric($serial))
  {
    $msg = "El numero de serie no puede conterner letras ni caracteres especiales...";
    $msg_color = "red";
  }
  else
  {

    $conn->query("INSERT INTO `admin_cosmosiot`.`devices` (`devices_alias`, `devices_serial`, `devices_user_id`) VALUES ('".$alias."', '".$serial."', '".$user_id."');");
    $msg = "¡Dispositivo agregado correctamente!";
    $msg_color = "green";
  }
}

$result = $conn->query("SELECT * FROM `admin_cosmosiot`.`devices` WHERE `devices_user_id` = '".$user_id."'");
$devices = $result->fetch_all(MYSQLI_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Cosmos IoT - Home Automation, Custom IoT Solutions, B2B Support and More!!</title>
  <meta name="description" content="Your Cosmic IoT site"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimal-ui" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <!-- for ios 7 style, multi-resolution icon of 152x152 -->
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-barstyle" content="black-translucent">
  <link rel="apple-touch-icon" href="assets/images/logo.png">
  <meta name="apple-mobile-web-app-title" content="Flatkit">
  <!-- for Chrome on Android, multi-resolution icon of 196x196 -->
  <meta name="mobile-web-app-capable" content="yes">
  <link rel="shortcut icon" sizes="196x196" href="assets/images/logo.png">

  <!-- style -->
  <link rel="stylesheet" href="assets/animate.css/animate.min.css" type="text/css" />
  <link rel="stylesheet" href="assets/glyphicons/glyphicons.css" type="text/css" />
  <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css" type="text/css" />
  <link rel="stylesheet" href="assets/material-design-icons/material-design-icons.css" type="text/css" />

  <link rel="stylesheet" href="assets/bootstrap/dist/css/bootstrap.min.css" type="text/css" />
  <!-- build:css assets/styles/app.min.css -->
  <link rel="stylesheet" href="assets/styles/app.css" type="text/css" />
  <!-- endbuild -->
  <link rel="stylesheet" href="assets/styles/font.css" type="text/css" />
</head>
<body>
  <div class="app" id="app">

    <!-- ############ LAYOUT START-->

    <!-- BARRA IZQUIERDA -->
    <!-- aside -->
    <div id="aside" class="app-aside modal nav-dropdown">
      <!-- fluid app aside -->
      <div class="left navside dark dk" data-layout="column">
        <div class="navbar no-radius">
          <!-- brand -->
          <a class="navbar-brand">
            <div ui-include="'assets/images/logo.svg'"></div>
            <img src="assets/images/logo.png" alt="." class="hide">
            <span class="hidden-folded inline">Cosmos IoT</span>
          </a>
          <!-- / brand -->
        </div>
        <div class="hide-scroll" data-flex>
          <nav class="scroll nav-light">

            <ul class="nav" ui-nav>
              <li class="nav-header hidden-folded">
                <small class="text-muted">Navegación</small>
              </li>

              <li>
                <a href="dashboard.php" >
                  <span class="nav-icon">
                    <i class="fa fa-building-o"></i>
                  </span>
                  <span class="nav-text">Principal</span>
                </a>
              </li>

              <li>
                <a href="devices.php" >
                  <span class="nav-icon">
                    <i class="fa fa-cogs"></i>
                  </span>
                  <span class="nav-text">Dispositivos</span>
                </a>
              </li>
            </ul>
          </nav>
        </div>
        <!--/
        <div class="b-t">
          <div class="nav-fold">
            <a href="profile.html">
              <span class="pull-left">
                <img src="assets/images/a0.jpg" alt="..." class="w-40 img-circle">
              </span>
              <span class="clear hidden-folded p-x">
                <span class="block _500">Jean Reyes</span>
                <small class="block text-muted"><i class="fa fa-circle text-success m-r-sm"></i>online</small>
              </span>
            </a>
          </div>
        </div>
      -->
      </div>
    </div>
    <!-- / -->

    <!-- content -->
    <div id="content" class="app-content box-shadow-z0" role="main">
      <div class="app-header white box-shadow">
        <div class="navbar navbar-toggleable-sm flex-row align-items-center">
          <!-- Open side - Naviation on mobile -->
          <a data-toggle="modal" data-target="#aside" class="hidden-lg-up mr-3">
            <i class="material-icons">&#xe5d2;</i>
          </a>
          <!-- / -->

          <!-- Page title - Bind to $state's title -->
          <div class="mb-0 h5 no-wrap" ng-bind="$state.current.data.title" id="pageTitle"></div>

          <!-- navbar collapse -->
          <div class="collapse navbar-collapse" id="collapse">
            <!-- link and dropdown -->
            <ul class="nav navbar-nav mr-auto">
              <li class="nav-item dropdown">
                <a class="nav-link" href data-toggle="dropdown">
                  <i class="fa fa-fw fa-plus text-muted"></i>
                  <span>New</span>
                </a>
                <div ui-include="'views/blocks/dropdown.new.html'"></div>
              </li>
            </ul>

            <div ui-include="'views/blocks/navbar.form.html'"></div>
            <!-- / -->
          </div>
          <!-- / navbar collapse -->

          <!-- BARRA DE LA DERECHA -->
          <ul class="nav navbar-nav ml-auto flex-row">
            <li class="nav-item dropdown pos-stc-xs">
              <a class="nav-link mr-2" href data-toggle="dropdown">
                <i class="material-icons">&#xe7f5;</i>
                <span class="label label-sm up warn">3</span>
              </a>
              <div ui-include="'views/blocks/dropdown.notification.html'"></div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link p-0 clear" href="#" data-toggle="dropdown">
                <span class="avatar w-32">
                  <img src="assets/images/a0.jpg" alt="...">
                  <i class="on b-white bottom"></i>
                </span>
              </a>
              <div ui-include="'views/blocks/dropdown.user.html'"></div>
            </li>
            <li class="nav-item hidden-md-up">
              <a class="nav-link pl-2" data-toggle="collapse" data-target="#collapse">
                <i class="material-icons">&#xe5d4;</i>
              </a>
            </li>
          </ul>
          <!-- / navbar right -->
        </div>
      </div>


      <div class="app-footer">
        <!-- PIE DE PAGINA -->
        <div class="p-2 text-xs">
          <div class="pull-right text-muted py-1">
            &copy; Copyright <strong>Flatkit</strong> <span class="hidden-xs-down">- Built with Love v1.1.3</span>
            <a ui-scroll-to="content"><i class="fa fa-long-arrow-up p-x-sm"></i></a>
          </div>
          <div class="nav">
            <a class="nav-link" href="">About</a>
          </div>
        </div>
      </div>

      <div ui-view class="app-body" id="view">


        <!-- SECCION CENTRAL -->
        <div class="padding">

          <!-- VALORES EN TIEMPO REAL -->
          <div class="row">
            <div class="col-md-6">
              <div class="box">
                <div class="box-header">
                  <h2>Agregar un dispositivo</h2>
                  <small>Ingresa el nombre (alias) y el N° de serie del dispositivo que quieres instalar</small>
                </div>
                <div class="box-divider m-0"></div>
                <div class="box-body">
                  <form role="form" method="post" action=""="">
                    <div class="form-group">
                      <label for"exampleInputAlias">Alias</label>
                      <input name="alias" type="text" class="form-control" placeholder="Ej: Comedor">
                    </div>
                    <div class="form-group">
                      <label for"exampleInputSerial">Serie</label>
                      <input name="serial" type="text" class="form-control" placeholder="Ej: 258746">
                    </div>
                    <button type="submit" class="btn white m-b">Registrar</button>
                    <div>
                      <?php if ($msg_color == "red") { ?>
                        <span style="color:red" class="">
                          <?php echo $msg; ?>
                        </span>
                      <?php } elseif ($msg_color == "green") { ?>
                        <span style="color:green" class="">
                          <?php echo $msg; ?>
                        <?php } ?>
                      </span>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="box">
                <div class="box-header">
                  <h2>Dispositivos agregados</h2>
                  <small>Aquí verá los dispositivos que estan asociados a su cuenta</small>
                </div>
                <div>
                  <table class="table table-striped b-t">
                    <thead>
                      <tr>
                        <td>Alias</td>
                        <td>Serie</td>
                        <td>Fecha</td>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($devices as $device) { ?>
                        <tr>
                          <td><?php echo $device['devices_alias']; ?></td>
                          <td><?php echo $device['devices_serial']; ?></td>
                          <td><?php echo $device['devices_date']; ?></td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <h5>Eliminar Dispositivos</h5>
              <form class="" action="" method="post">
                <div class="form-group">
                  <select name="id_to_delete" class="form-control select2" ui-jp="select2" ui-options="{theme: 'bootstrap'}">
                    <?php foreach ($devices as $device) { ?>
                      <option value="<?php echo $device['devices_id']; ?>"><?php echo $device['devices_alias']."<--->".$device['devices_serial']; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <button type="submit" class="btn btn-fw danger">Eliminar</button>
              </form>
            </div>
          </div>
          <!-- ############ PAGE END-->

        </div>

      </div>
      <!-- / -->

      <!-- SELECTOR DE TEMAS -->
      <div id="switcher">
        <div class="switcher box-color dark-white text-color" id="sw-theme">
          <a href ui-toggle-class="active" target="#sw-theme" class="box-color dark-white text-color sw-btn">
            <i class="fa fa-gear"></i>
          </a>
          <div class="box-header">
            <h2>Selector de temas</h2>
          </div>
          <div class="box-divider"></div>
          <div class="box-body">
            <p class="hidden-md-down">
              <label class="md-check m-y-xs"  data-target="folded">
                <input type="checkbox">
                <i class="green"></i>
                <span class="hidden-folded">Folded Aside</span>
              </label>
              <label class="md-check m-y-xs" data-target="boxed">
                <input type="checkbox">
                <i class="green"></i>
                <span class="hidden-folded">Boxed Layout</span>
              </label>
              <label class="m-y-xs pointer" ui-fullscreen>
                <span class="fa fa-expand fa-fw m-r-xs"></span>
                <span>Fullscreen Mode</span>
              </label>
            </p>
            <p>Colors:</p>
            <p data-target="themeID">
              <label class="radio radio-inline m-0 ui-check ui-check-color ui-check-md" data-value="{primary:'primary', accent:'accent', warn:'warn'}">
                <input type="radio" name="color" value="1">
                <i class="primary"></i>
              </label>
              <label class="radio radio-inline m-0 ui-check ui-check-color ui-check-md" data-value="{primary:'accent', accent:'cyan', warn:'warn'}">
                <input type="radio" name="color" value="2">
                <i class="accent"></i>
              </label>
              <label class="radio radio-inline m-0 ui-check ui-check-color ui-check-md" data-value="{primary:'warn', accent:'light-blue', warn:'warning'}">
                <input type="radio" name="color" value="3">
                <i class="warn"></i>
              </label>
              <label class="radio radio-inline m-0 ui-check ui-check-color ui-check-md" data-value="{primary:'success', accent:'teal', warn:'lime'}">
                <input type="radio" name="color" value="4">
                <i class="success"></i>
              </label>
              <label class="radio radio-inline m-0 ui-check ui-check-color ui-check-md" data-value="{primary:'info', accent:'light-blue', warn:'success'}">
                <input type="radio" name="color" value="5">
                <i class="info"></i>
              </label>
              <label class="radio radio-inline m-0 ui-check ui-check-color ui-check-md" data-value="{primary:'blue', accent:'indigo', warn:'primary'}">
                <input type="radio" name="color" value="6">
                <i class="blue"></i>
              </label>
              <label class="radio radio-inline m-0 ui-check ui-check-color ui-check-md" data-value="{primary:'warning', accent:'grey-100', warn:'success'}">
                <input type="radio" name="color" value="7">
                <i class="warning"></i>
              </label>
              <label class="radio radio-inline m-0 ui-check ui-check-color ui-check-md" data-value="{primary:'danger', accent:'grey-100', warn:'grey-300'}">
                <input type="radio" name="color" value="8">
                <i class="danger"></i>
              </label>
            </p>
            <p>Themes:</p>
            <div data-target="bg" class="row no-gutter text-u-c text-center _600 clearfix">
              <label class="p-a col-sm-6 light pointer m-0">
                <input type="radio" name="theme" value="" hidden>
                Light
              </label>
              <label class="p-a col-sm-6 grey pointer m-0">
                <input type="radio" name="theme" value="grey" hidden>
                Grey
              </label>
              <label class="p-a col-sm-6 dark pointer m-0">
                <input type="radio" name="theme" value="dark" hidden>
                Dark
              </label>
              <label class="p-a col-sm-6 black pointer m-0">
                <input type="radio" name="theme" value="black" hidden>
                Black
              </label>
            </div>
          </div>
        </div>
      </div>
      <!-- / -->

      <!-- ############ LAYOUT END-->

    </div>
    <!-- build:js scripts/app.html.js -->
    <!-- jQuery -->
    <script src="libs/jquery/jquery/dist/jquery.js"></script>
    <!-- Bootstrap -->
    <script src="libs/jquery/tether/dist/js/tether.min.js"></script>
    <script src="libs/jquery/bootstrap/dist/js/bootstrap.js"></script>
    <!-- core -->
    <script src="libs/jquery/underscore/underscore-min.js"></script>
    <script src="libs/jquery/jQuery-Storage-API/jquery.storageapi.min.js"></script>
    <script src="libs/jquery/PACE/pace.min.js"></script>

    <script src="html/scripts/config.lazyload.js"></script>

    <script src="html/scripts/palette.js"></script>
    <script src="html/scripts/ui-load.js"></script>
    <script src="html/scripts/ui-jp.js"></script>
    <script src="html/scripts/ui-include.js"></script>
    <script src="html/scripts/ui-device.js"></script>
    <script src="html/scripts/ui-form.js"></script>
    <script src="html/scripts/ui-nav.js"></script>
    <script src="html/scripts/ui-screenfull.js"></script>
    <script src="html/scripts/ui-scroll-to.js"></script>
    <script src="html/scripts/ui-toggle-class.js"></script>

    <script src="html/scripts/app.js"></script>

    <!-- ajax -->
    <script src="libs/jquery/jquery-pjax/jquery.pjax.js"></script>
    <script src="html/scripts/ajax.js"></script>

    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
    <script type="text/javascript">
    </script>

    <!-- endbuild -->
  </body>
  </html>
