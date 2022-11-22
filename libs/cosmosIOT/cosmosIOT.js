var grantedAudio = new Audio("mixkit-sci-fi-reject-notification-896.wav");
var refusededAudio = new Audio("mixkit-double-beep-tone-alert-2868.wav");

// Socket controll
function socketControll(devSerial, usersAccountID) {
    if (document.getElementById(devSerial).style.color == 'lime')
        client.publish(devSerial + "/tx_controll", usersAccountID + ",apagado", (error) => { });
    else
        client.publish(devSerial + "/tx_controll", usersAccountID + ",encendido", (error) => { });
}

// Ligth controll
function lightControll(devSerial, usersAccountID, colId, briId, command) {
    var colValue = $("#" + colId).val();
    var briValue = $("#" + briId).val();

    var ledValue = "," + briValue + "/" + colValue;

    if (command == 0 && document.getElementById(devSerial).style.color == 'lime')
        client.publish(devSerial + "/tx_controll", usersAccountID + ",000/000/000/000/", (error) => { });
    else
        client.publish(devSerial + "/tx_controll", usersAccountID + ledValue, (error) => { });
}

// Servo controll
function servoControll(devSerial, usersAccountID) {

}

/**
 * This function is called when a new event is sent
 * by the MQTT broker
 * 
 * @param {string} topic 
 * @param {string} message 
 * @param {JSON} data 
 */
function msgRecieved(topic, message, data) {
    var msg = message.toString()
    var splitted_topic = topic.split("/");
    var serial_number = splitted_topic[0];
    var type = splitted_topic[1];

    if (type == "Sensors") {
        var subtype = splitted_topic[2];
        var splitted_msg = msg.split(",");
        var userId = splitted_msg[0];
        var value1 = splitted_msg[1];
        var devSn = serial_number.slice(6, 13);

        if (userId == data['user_id']) {
            if (subtype = "Th") {
                var value2 = splitted_msg[2];
                $("#tempDisplay" + devSn).html(value1);
                $("#humDisplay" + devSn).html(value2);
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

    if (type == "Coms") {
        if (msg == "<?php echo $user_id ?>,Granted") {
            $("#display_new_access").html("Acción realizada con exito");
            grantedAudio.play();
            setTimeout(function () {
                $("#display_new_access").html("");
            }, 750)
        }

        if (msg == "<?php echo $user_id ?>,Refused") {
            $("#display_new_access").html("¡Atención! Usted no posee permiso para utilizar el dispositivo. Por favor comuniquese con el administardor del mismo");
            refusededAudio.play();
            setTimeout(function () {
                $("#display_new_access").html("");
            }, 3000)
        }

        if (msg == "<?php echo $user_id ?>,Nonexistent") {
            $("#display_new_access").html("¡Atención! Un usuario desconocido está intentando acceder a su dispositivo");
            refusededAudio.play();
            setTimeout(function () {
                $("#display_new_access").html("");
            }, 3000)
        }

        if (msg == "<?php echo $user_id ?>,Undefined") {
            $("#display_new_access").html("Oh oh, parece que el dispositivo al que intentas acceder no está conectado...");
            refusededAudio.play();
            setTimeout(function () {
                $("#display_new_access").html("");
            }, 3000)
        }
    }

    if (type == "Status") {

        var subtype = splitted_topic[2];

        if (subtype == "online") {
            if (msg == "1") {
                document.getElementById(serial_number).style.color = 'lime';
            }
            else if (msg == "0") {
                document.getElementById(serial_number).style.color = 'red';
            }
            else if (msg == "2") {
                document.getElementById(serial_number).style.color = 'yellow';
            }
        }
        else if (subtype == "rgbState") {
            var splitted_msg = msg.split("/");
            var brValue = splitted_msg[0];
            var rgbValue = splitted_msg[1] + "/" + splitted_msg[2] + "/" + splitted_msg[3] + "/"

            document.getElementById("cc_id" + serial_number).value = rgbValue;
            document.getElementById("br_id" + serial_number).value = brValue;
        }
    }
}
 
/**
 * This function is the MQTT broker connection handler
 * 
 * @param {JSON} devices JSON contaning all user devices info
 */
function cosmosIoTMqttServerConn(data) {
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
    const WebSocket_URL = 'wss://' + $_ENV['EMQX_HOST'] + ':' + $_ENV['EMQX_WSS_PORT'] + '/mqtt';

    const client = mqtt.connect(WebSocket_URL, options);

    client.on('connect', () => {
        console.log('Mqtt conectado por WS! Exito!')

        for (let i = 0; i < data['devices'].length; i++) {
            client.subscribe(data['devices'][i] + '/Sensors/+', { qos: 0 }, (error) => { });
            client.subscribe(data['devices'][i] + '/Status/+', { qos: 0 }, (error) => { });
            client.subscribe(data['devices'][i] + '/Coms', { qos: 0 }, (error) => { });
        }
    });

    client.on('message', (topic, message) => {
        msgRecieved(topic, message, data);
    });

    client.on('reconnect', (error) => {
        console.log('Ups... Error al reconectar', error);
    });

    client.on('error', (error) => {
        console.log('Oh oh... Error de conexión:', error);
    });
}

/**
 * test func
 */
function testFunc(data) {
    for (let index = 0; index < data['devices'].length; index++) {
        console.log(data['devices'][index]);
    }
}