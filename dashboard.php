<?php
session_start();
$logged = $_SESSION['logged'];

if(!$logged)
{
  echo "Ingreso no autorizado";
  die();
}

$devices = $_SESSION['devices'];
$user_id = $_SESSION['user_id'];


//DB connection
$conn = mysqli_connect($_ENV['MYSQL_HOST'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD'], $_ENV['MYSQL_DB']);

if ($conn==false)
{
  echo "Hubo un problema al conectarse a María DB";
  die();
}

$array = array();
foreach ($devices as $device)
{
  array_push($array, $device['devices_serial']);
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <title>Cosmos IoT - ¡Automatización del hogar, soluciones IoT personalizadas, soporte B2B y más!</title>
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
      </div>
    </div>

    <div id="content" class="app-content box-shadow-z0" role="main">
      <div class="app-header white box-shadow">
        <div class="navbar navbar-toggleable-sm flex-row align-items-center">
          <!-- Open side - Naviation on mobile -->
          <a data-toggle="modal" data-target="#aside" class="hidden-lg-up mr-3">
            <i class="material-icons">&#xe5d2;</i>
          </a>
          <!-- New access display -->
          <div class="">
            <b id="display_new_access"> </b>
          </div>
          <!-- Page title - Bind to $state's title -->
          <div class="mb-0 h5 no-wrap" ng-bind="$state.current.data.title" id="pageTitle"></div>
        </div>
      </div>

      <!-- PIE DE PAGINA -->
      <div class="app-footer">
        <div class="p-2 text-xs">
          <div class="pull-right text-muted py-1">
            &copy; Copyright <strong>Cosmoskiller</strong> <span class="hidden-xs-down">- Hecho con Amor v1.0</span>
            <a ui-scroll-to="content"><i class="fa fa-long-arrow-up p-x-sm"></i></a>
          </div>
          <div class="nav">
            <a class="nav-link" href="">Contacto</a>
          </div>
        </div>
      </div>

      <div ui-view class="app-body" id="view">
        <!-- SECCION CENTRAL -->
        <div class="padding">
        <!--Light -->
          <div class="row">
            <div class="col-sm-6">
              <div class="box p-a">
                <div class="pull-left m-r">
                  <span class="w-40 rounded  accent">
                    <i class="material-icons md-24">&#xe42e;</i>
                  </span>
                </div>
                <div class="box-header">
                  <h2>Luces</h2>
                </div>
                <div class="table-responsive">
                  <table class="table table-striped b-t">
                    <thead>
                      <tr>
                        <th>Alias</th>
                        <th>Color</th>
                        <th>Brillo</th>
                        <th>On/Off</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                    
                    <?php foreach ($devices as $device){ ?> <!-- We genrate a row for each device -->
                      <?php if (substr($device['devices_serial'], 0, 3) == "LSC"){ ?> <!-- We check if the device is a light source -->
                      <tr>
                        <td><?php echo utf8_encode(($device['owners_devAlias'])) ?></td>
                        <td>
                          <select 
                          id="<?php echo "cc_id".$device["devices_serial"]?>"
                          onchange="lightControll(
                            '<?php echo $device["devices_serial"]?>',
                            '<?php echo $device["users_account_id"]?>',
                            '<?php echo "cc_id".$device["devices_serial"]?>', 
                            '<?php echo "br_id".$device["devices_serial"]?>',
                            1)" 
                            > <!-- We create a different id for each select, using the device serial -->
                          <?php if (substr($device['devices_serial'], 4, 1) == "c") { ?> <!-- We if the light source is RGB or white-only -->
                            <!-- If RGB we make a color picker, using a <select> element -->
                            <option value="255/255/255/">Blanco</option>
                            <option value="255/000/000/">Rojo</option>
                            <option value="000/255/000/">Verde</option>
                            <option value="000/000/255/">Azul</option>
                            <option value="255/255/000/">Amarillo</option>
                            <option value="255/127/000/">Naranja</option>
                            <option value="255/000/255/">Violeta</option>
                            <option value="000/255/255/">Cian</option>
                          <?php } else {?>
                            <!-- If white-only we only show the option "Blanco" -->
                            <option value="255/255/255/">Blanco</option>
                          <?php }?>
                          </select>
                        </td>
                        <td> 
                          <!-- Now for the brigthness controll, we're using a simple text input -->
                          <select
                          id="<?php echo "br_id".$device["devices_serial"]?>" 
                          onchange="lightControll(
                            '<?php echo $device["devices_serial"]?>',
                            '<?php echo $device["users_account_id"]?>',
                            '<?php echo "cc_id".$device["devices_serial"]?>', 
                            '<?php echo "br_id".$device["devices_serial"]?>',
                            1)"  
                            >
                            <option value="100">100%</option>
                            <option value="090">90%</option>
                            <option value="080">80%</option>
                            <option value="070">70%</option>
                            <option value="060">60%</option>
                            <option value="050">50%</option>
                            <option value="040">40%</option>
                            <option value="030">30%</option>
                            <option value="020">20%</option>
                            <option value="010">10%</option>
                            <option value="000">0%</option>
                          </select>
                        </td>
                        <td>
                          <button
                          class="btn btn-sm warn" 
                          onclick="lightControll(
                            '<?php echo $device["devices_serial"]?>',
                            '<?php echo $device["users_account_id"]?>',
                            '<?php echo "cc_id".$device["devices_serial"]?>', 
                            '<?php echo "br_id".$device["devices_serial"]?>',
                            0)" 
                            >
                          <i id="<?php echo "lc_id".$device["devices_serial"]?>" class="material-icons md-24">&#xe8ac;</i>
                          </button>
                        </td>
                        <td>
                          <!-- This last column displays an indicator to show the current status of the device -->
                          <i id="<?php echo $device["devices_serial"]?>" class="material-icons md-24">&#xe8ac;</i>
                        </td>
                      </tr>
                      <?php } ?>
                    <?php } ?>

                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="box p-a">
                <div class="pull-left m-r">
                  <span class="w-40 rounded  accent">
                    <i class="material-icons md-24">&#xe63c;</i>
                  </span>
                </div>
                <div class="box-header">
                  <h2>Tomacorrientes</h2>
                </div>
                <table class="table table-striped b-t">
                  <thead>
                    <tr>
                      <th>Alias</th>
                      <th>On</th>
                      <th>Off</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>

                    <?php $j = 0 ?>
                    <?php foreach ($devices as $device){ ?> <!-- We genrate a row for each device -->
                      <?php $j++ ?>
                      <?php if (substr( $device['devices_serial'], 0, 3) == "SKT"){ ?> <!-- We check if the device is a socket-->
                      <tr>
                        <td><?php echo utf8_encode(($device['owners_devAlias'])) ?></td>
                        <td>
                          <button
                          class="btn btn-sm success" 
                          onclick="socketControll('<?php echo $device["devices_serial"]?>', '<?php echo $device["users_account_id"]?>')" 
                          > <!-- Indivudual id for each button, using the device serial -->
                          I
                          </button>
                        </td>
                        <td>
                          <button
                          class="btn btn-sm danger" 
                          onclick="socketControll('<?php echo $device["devices_serial"]?>', '<?php echo $device["users_account_id"]?>')" 
                          > <!-- Indivudual id for each button, using the device serial -->
                          O
                          </button>
                        </td>
                        <td>
                          <!-- This last column displays an indicator to show the current status of the device -->
                          <i id="<?php echo $device["devices_serial"]?>" class="material-icons md-24">&#xe8ac;</i>
                        </td>
                      </tr>
                      <?php } ?>
                    <?php } ?>

                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-6">
              <div class="box p-a">
                <div class="pull-left m-r">
                  <span class="w-40 rounded info">
                    <i class="material-icons md-24">&#xe8bf;</i>
                  </span>
                </div>
                <div class="box-header">
                  <h2>Sensores</h2>
                </div>
                <table class="table table-striped b-t">
                  <thead>
                    <tr>
                      <th>Alias</th>
                      <th>Tipo</th>
                      <th>Valor</th>
                    </tr>
                  </thead>
                  <tbody>

                    <?php foreach ($devices as $device){ ?> <!-- We genrate a row for each device -->
                      <?php $dev = substr($device['devices_serial'], 6, 7) ?> 
                      <?php if (substr($device['devices_serial'], 0, 3) == "SNR") { ?> <!-- We check if the device is a sensor-->
                        
                        <!--
                        And now we check what kind of sensor the device is.
                        For the particular case of a temperature and humudity sensor (usually DTH22 
                        or DHT11) we create two individual rows for each value out of the same device.
                        This is, so that we can display each indivual reading of the sensor.
                        -->
                        <?php if (substr($device['devices_serial'], 3, 2) == "Th"){ ?>
                        <tr>
                          <td><?php echo utf8_encode(($device['owners_devAlias'])) ?></td>
                          <td><i class="material-icons md-24">&#xe430;</i> Temperatura</td>
                          <td><b id="<?php echo "tempDisplay".$dev ?>" href ="">-- </b><span class="text-sm"> °C</span></td>
                        </tr>

                        <tr>
                          <td><?php echo utf8_encode(($device['owners_devAlias'])) ?></td>
                          <td><i class="material-icons md-24">&#xe42d;</i> Humedad relativa</td>
                          <td><b id="<?php echo "humDisplay".$dev ?>" href ="">-- </b><span class="text-sm"> %</span></td>
                        </tr>
                        <?php continue; } ?>

                        <!-- For the other subtypes we only create one row for each device -->
                        <tr>
                          <td><?php echo utf8_encode(($device['owners_devAlias'])) ?></td>

                          <?php if (substr($device['devices_serial'], 3, 2) == "Fh"){ ?>
                            <td><i class="material-icons md-24">&#xe42d;</i> Humedad del suelo</td>
                            <td><b id="<?php echo "fhDisplay".$dev ?>" href ="">-- </b><span class="text-sm"> %</td>

                          <?php } elseif (substr($device['devices_serial'], 3, 2) == "Vo"){ ?>
                            <td><i class="material-icons md-24">&#xe3e7;</i> Voltaje</td>
                            <td><b id="<?php echo "voDisplay".$dev ?>" href ="">-- </b><span class="text-sm"> V</td>

                          <?php } elseif (substr($device['devices_serial'], 3, 2) == "Li"){ ?>
                            <td><i class="material-icons md-24">&#xe3ab;</i> Luz ambiente</td>
                            <td><b id="<?php echo "liDisplay".$dev ?>" href ="">-- </b><span class="text-sm"> cd</td>

                          <?php } elseif (substr($device['devices_serial'], 3, 2) == "Po"){ ?>
                            <td><i class="material-icons md-24">&#xe002;</i> Polución</td>
                            <td><b id="<?php echo "poDisplay".$dev ?>" href ="">-- </b><span class="text-sm"> ppm</td>

                          <?php } elseif (substr($device['devices_serial'], 3, 2) == "Wl"){ ?>
                            <td><i class="material-icons md-24">&#xee891;</i> Nivel de agua</td>
                            <td><b id="<?php echo "humDisplay".$dev ?>" href ="">-- </b><span class="text-sm"> mts</td>
                          <?php } ?>
                        </tra
                      <?php } ?>
                    <?php } ?>

                  </tbody>
                </table>
              </div>
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
              <span class="hidden-folded">Panel Izquiero Oculto</span>
            </label>
            <label class="md-check m-y-xs" data-target="boxed">
              <input type="checkbox">
              <i class="green"></i>
              <span class="hidden-folded">Vista Reducida</span>
            </label>
            <label class="m-y-xs pointer" ui-fullscreen>
              <span class="fa fa-expand fa-fw m-r-xs"></span>
              <span>Pantalla Completa</span>
            </label>
          </p>
          <p>Colores:</p>
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
          <p>Temas:</p>
          <div data-target="bg" class="row no-gutter text-u-c text-center _600 clearfix">
            <label class="p-a col-sm-6 light pointer m-0">
              <input type="radio" name="theme" value="" hidden>
              Claro
            </label>
            <label class="p-a col-sm-6 grey pointer m-0">
              <input type="radio" name="theme" value="grey" hidden>
              Gris
            </label>
            <label class="p-a col-sm-6 dark pointer m-0">
              <input type="radio" name="theme" value="dark" hidden>
              Oscuro
            </label>
            <label class="p-a col-sm-6 black pointer m-0">
              <input type="radio" name="theme" value="black" hidden>
              Negro
            </label>
          </div>
        </div>
      </div>
    </div>
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

<!-- Web backned -->
<script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
<script type="text/javascript">

// Variable declaration 

// Socket controll
function socketControll(devSerial, usersAccountID)
{
  if (document.getElementById(devSerial).style.color == 'lime')
    client.publish(devSerial + "/tx_controll", usersAccountID + ",apagado", (error) => {});
  else
    client.publish(devSerial + "/tx_controll", usersAccountID + ",encendido", (error) => {});
} 

// Ligth controll
function lightControll(devSerial, usersAccountID, colId, briId, command)
{
  var briValue = "";  
  var colValue = $("#" + colId).val();
  var briValue = $("#" + briId).val();

  var ledValue = "," + briValue + "/" + colValue;
  
  if (command == 0 && document.getElementById(devSerial).style.color == 'lime')
    client.publish(devSerial + "/tx_controll", usersAccountID + ",000/000/000/000/", (error) => {});
  else
    client.publish(devSerial + "/tx_controll", usersAccountID + ledValue, (error) => {});
}

// Msg proccesing
var grantedAudio = new Audio("mixkit-sci-fi-reject-notification-896.wav");
var refusededAudio = new Audio("mixkit-double-beep-tone-alert-2868.wav");
function msgRecived (topic, message)
{
  var msg = message.toString()
  var splitted_topic = topic.split("/");
  var serial_number = splitted_topic[0];
  var type = splitted_topic[1];

  if (type == "Sensors")
  {
    var subtype = splitted_topic[2];
    var splitted_msg = msg.split(",");
    var userId = splitted_msg[0];
    var value1 = splitted_msg[1];
    var devSn = serial_number.slice(6, 13);

    if (userId == <?php echo $user_id ?>)
    {
      if (subtype = "Th")
      {
        var value2 = splitted_msg[2];
        $("#tempDisplay" + devSn).html(value1);
        $("#humDisplay"+ devSn).html(value2);
      }
      else if (subtype = "Fh")
        $("#fhDisplay" + devSn).html(value1);
      else if (subtype = "Vo")
        $("#voDisplay" + devSn).html(value1);   
      else if (subtype = "Li")
        $("#liDisplay" + devSn).html(value1);
      else if (subtype = "Po")
        $("#poDisplay" + devSn).html(value1);
      else if (subtype = "Wl")
        $("#wlDisplay" + devSn).html(value1);
    } 
  }

  if (type == "Coms")
  {
    if (msg == "<?php echo $user_id ?>,Granted")
    {
      $("#display_new_access").html("Acción realizada con exito");
      grantedAudio.play();
      setTimeout(function()
      {
        $("#display_new_access").html("");
      }, 750)
    }

    if (msg == "<?php echo $user_id ?>,Refused")
    {
      $("#display_new_access").html("¡Atención! Usted no posee permiso para utilizar el dispositivo. Por favor comuniquese con el administardor del mismo");
      refusededAudio.play();
      setTimeout(function()
      {
        $("#display_new_access").html("");
      }, 3000)
    }

    if (msg == "<?php echo $user_id ?>,Nonexistent")
    {
      $("#display_new_access").html("¡Atención! Un usuario desconocido está intentando acceder a su dispositivo");
      refusededAudio.play();
      setTimeout(function()
      {
        $("#display_new_access").html("");
      }, 3000)
    }

    if (msg == "<?php echo $user_id ?>,Undefined")
    {
      $("#display_new_access").html("Oh oh, parece que el dispositivo al que intentas acceder no está conectado...");
      refusededAudio.play();
      setTimeout(function()
      {
        $("#display_new_access").html("");
      }, 3000)
    }
  }

  if (type == "Status")
  {
    
    var subtype = splitted_topic[2];

    if (subtype == "online")
    {
      if (msg == "1")
      {
        document.getElementById(serial_number).style.color = 'lime';
      }
      else if (msg == "0")
      {
        document.getElementById(serial_number).style.color = 'red';
      }
      else if (msg == "2")
      {
        document.getElementById(serial_number).style.color = 'yellow';
      }
    }
    else if (subtype == "rgbState")
    {
      var splitted_msg = msg.split("/");
      var brValue = splitted_msg[0];
      var rgbValue = splitted_msg[1] + "/" + splitted_msg[2] + "/" + splitted_msg[3] + "/" 

      document.getElementById("cc_id" + serial_number).value = rgbValue;
      document.getElementById("br_id" + serial_number).value = brValue;
    }  
  }
}

// Connect options
const options = {
  connectTimeout: 4000,

  // Authentication
  clientId: 'cosmosiot_web_' + Math.round(Math.random() * (0 - 10000) * -1),
  username: $_ENV['EMQX_WEBCLIENT_USER'],
  password: $_ENV['EMQX_WEBCLIENT_PASSWORD'],

  keepalive: 60,
  clean: true,
}

var connected = false;

// WebSocket connect url
const WebSocket_URL = 'wss://' + $_ENV['EMQX_HOST'] + ':' + $_ENV['EMQX_WSS_PORT'] + '/mqtt'

const client = mqtt.connect(WebSocket_URL, options)

client.on('connect', () => {
  console.log('Mqtt conectado por WS! Exito!')

  <?php foreach ($devices as $device) {?>
    client.subscribe('<?php echo $device['devices_serial'] ?>/Sensors/+', {qos: 0}, (error) => {})
    client.subscribe('<?php echo $device['devices_serial'] ?>/Status/+', {qos: 0}, (error) => {})
    client.subscribe('<?php echo $device['devices_serial'] ?>/Coms', {qos: 0}, (error) => {})
  <?php } ?>
})

client.on('message', (topic, message) =>{
  msgRecived(topic, message)
})

client.on('reconnect', (error) => {
  console.log('Ups... Error al reconectar', error)
})

client.on('error', (error) => {
  console.log('Oh oh... Error de conexión:', error)
})

</script>
<!-- endbuild -->
</body>
</html>
