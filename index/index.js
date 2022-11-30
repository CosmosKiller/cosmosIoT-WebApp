var mqtt = require('mqtt');

var ledValue = "";
var rValue = "255";
var gValue = "255"; 
var bValue = "255"; 
var briValue = "100";  

//Socket controll
function socketControll(devSerial, usersAccountID, power)
{
  if (power == "1")
  {
    client.publish(devSerial + "/tx_controll", usersAccountID + ",encendido", (error) => {});
  }
  else
  {
    client.publish(devSerial + "/tx_controll", usersAccountID + ",apagado", (error) => {});
  }
} 

//Ligth controll
function lightControll(devSerial, usersAccountID)
{
  client.publish(devSerial + "/tx_controll", usersAccountID + ",000/000/000/000/", (error) => {});
}

//RGB LED Color
function colorControll(devSerial, usersAccountID, devId)
{
  var color = $("#" + devId).val();
  var splitted_color = color.split("/")
  rValue = splitted_color[0];
  gValue = splitted_color[1];
  bValue = splitted_color[2];

  ledValue = "," + briValue + "/" + rValue + "/" + gValue + "/" + bValue + "/" ;
  client.publish(devSerial + "/tx_controll", usersAccountID + ledValue, (error) => {});
}

function brightnessControll(devSerial, usersAccountID, brValue)
{
  if (brValue.length < 3)
  {
    briValue = "0" + brValue;
  }
  else
  {
    briValue = brValue;
  }
  ledValue = "," + briValue + "/" + rValue + "/" + gValue + "/" + bValue + "/" ;
  client.publish(devSerial + "/tx_controll", usersAccountID + ledValue, (error) => {});
}

// TX controll
function command(action)
{
  var devices_serial = $("#device_id").val();
  if (action == "open")
  {
    client.publish(devices_serial + "/tx_controll", "<?php echo $device['users_account_id'] ?>,encendido", (error) => {});
  }
  else
  {
    client.publish(devices_serial + "/tx_controll", "<?php echo $device['users_account_id'] ?>,apagado", (error) => {});
  }
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

  if (type == "coms")
  {
    if (msg == "<?php echo $user_id ?>,Granted")
    {
      $("#display_new_access").html("Acción realizada con exito");
      grantedAudio.play();;
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

  if (type == "State")
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
}

// Connect options
const options = {
  connectTimeout: 4000,

  // Authentication
  clientId: 'cosmosiot_web_'+ Math.round(Math.random() * (0 - 10000) * -1),
  username: 'web_client',
  password: 'A_3?4$r[![sFBKgs',

  keepalive: 60,
  clean: true,
}

var connected = false;

// WebSocket connect url
const WebSocket_URL = 'wss://cosmos-iot.ml:8094/mqtt'

const client = mqtt.connect(WebSocket_URL, options)

client.on('connect', () => {
  console.log('Mqtt conectado por WS! Exito!')
  // client.subscribe('sensorValues', {qos: 0}, (error) => {});

  <?php foreach ($devices as $device) {?>
    client.subscribe('<?php echo $device['devices_serial'] ?>/Sensors/+', {qos: 0}, (error) => {})
    client.subscribe('<?php echo $device['devices_serial'] ?>/State', {qos: 0}, (error) => {})
    client.subscribe('<?php echo $device['devices_serial'] ?>/coms', {qos: 0}, (error) => {})
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