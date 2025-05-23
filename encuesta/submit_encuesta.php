<?php
// submit_encuesta.php

// -----------------------------------------------------------
// --- INICIO: LÍNEAS PARA DEPURACIÓN (QUITAR EN PRODUCCIÓN) ---
ini_set('display_errors', 1);             // Muestra errores en el navegador
ini_set('display_startup_errors', 1);     // Muestra errores al inicio
error_reporting(E_ALL);                   // Reporta todos los tipos de errores
// --- FIN: LÍNEAS PARA DEPURACIÓN (QUITAR EN PRODUCCIÓN) -----
// -----------------------------------------------------------

// 1. Configuración de la base de datos
$servername = "localhost";
$username = "root";       // <-- ¡MUY IMPORTANTE: CAMBIA ESTO por tu usuario de MySQL!
$password = "usbw";           // <-- ¡MUY IMPORTANTE: CAMBIA ESTO por tu contraseña de MySQL!
                          //    (En XAMPP/MAMP suele ser VACÍA por defecto, es decir, "")
$dbname = "encuesta_simple_secundaria"; // Nombre de tu base de datos

// 2. Crear conexión a la base de datos
// Con try-catch para una mejor depuración de la conexión
try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        throw new Exception("Error de conexión a la base de datos: " . $conn->connect_error);
    }
} catch (Exception $e) {
    // Si hay un error en la conexión, lo mostramos claramente
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Error de Conexión</title>
        <link rel='stylesheet' href='style.css'>
        <style>
            .error-message {
                text-align: center;
                margin-top: 50px;
                padding: 30px;
                background-color: #ffe6e6;
                border: 1px solid #f6c6c6;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }
            .error-message h2 {
                color: #dc3545;
                margin-bottom: 20px;
            }
            .error-message p {
                font-size: 1.1em;
                color: #333;
            }
            .error-message a {
                display: inline-block;
                margin-top: 25px;
                padding: 10px 20px;
                background-color: #007bff;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                transition: background-color 0.3s ease;
            }
            .error-message a:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='error-message'>
                <h2>Error Grave: No se pudo conectar a la base de datos</h2>
                <p>" . $e->getMessage() . "</p>
                <p>Por favor, revisa tus credenciales de MySQL (`username`, `password`, `dbname`) en el archivo `submit_encuesta.php` y asegúrate de que MySQL esté ejecutándose en tu XAMPP/MAMP.</p>
                <a href='index.html'>Volver a la encuesta</a>
            </div>
        </div>
    </body>
    </html>";
    exit(); // Terminar el script aquí
}

// 3. Recopilar y sanear datos del formulario
// Usaremos la función sanitize_input para cada dato.
function sanitize_input($conn, $data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return mysqli_real_escape_string($conn, $data);
}

$p1_sufrido_ciberacoso = sanitize_input($conn, $_POST['p1_sufrido_ciberacoso'] ?? '');
$p2_tipo_mas_comun = sanitize_input($conn, $_POST['p2_tipo_mas_comun'] ?? '');
$p3_donde_ciberacoso = sanitize_input($conn, $_POST['p3_donde_ciberacoso'] ?? '');
$p4_quien_acosa = sanitize_input($conn, $_POST['p4_quien_acosa'] ?? '');
$p5_sentimiento_sufrir = sanitize_input($conn, $_POST['p5_sentimiento_sufrir'] ?? '');
$p6_primera_reaccion = sanitize_input($conn, $_POST['p6_primera_reaccion'] ?? '');
$p7_visto_ciberacoso = sanitize_input($conn, $_POST['p7_visto_ciberacoso'] ?? '');
$p8_accion_al_ver = sanitize_input($conn, $_POST['p8_accion_al_ver'] ?? '');
$p9_importancia_ayuda = sanitize_input($conn, $_POST['p9_importancia_ayuda'] ?? '');
$p10_confianza_adulto = sanitize_input($conn, $_POST['p10_confianza_adulto'] ?? '');
$p11_informacion_colegio = sanitize_input($conn, $_POST['p11_informacion_colegio'] ?? '');
$p12_fuente_principal_info = sanitize_input($conn, $_POST['p12_fuente_principal_info'] ?? '');
$p13_protocolo_colegio = sanitize_input($conn, $_POST['p13_protocolo_colegio'] ?? '');
$p14_utilidad_charlas = sanitize_input($conn, $_POST['p14_utilidad_charlas'] ?? '');
$p15_rol_colegio_prevencion = sanitize_input($conn, $_POST['p15_rol_colegio_prevencion'] ?? '');
$p16_horas_pantalla = sanitize_input($conn, $_POST['p16_horas_pantalla'] ?? '');
$p17_red_social_mas_usada = sanitize_input($conn, $_POST['p17_red_social_mas_usada'] ?? '');
$p18_seguridad_online = sanitize_input($conn, $_POST['p18_seguridad_online'] ?? '');
$p19_denunciarias_online = sanitize_input($conn, $_POST['p19_denunciarias_online'] ?? '');
$p20_mejorar_seguridad_online = sanitize_input($conn, $_POST['p20_mejorar_seguridad_online'] ?? '');

// Campo opcional de texto libre
$sugerencias_adicionales = sanitize_input($conn, $_POST['sugerencias_adicionales'] ?? '');


// 4. Preparar e insertar datos usando sentencias preparadas (¡Mejor seguridad!)
$sql = "INSERT INTO respuestas_adolescentes_simple (
    p1_sufrido_ciberacoso, p2_tipo_mas_comun, p3_donde_ciberacoso, p4_quien_acosa, p5_sentimiento_sufrir,
    p6_primera_reaccion, p7_visto_ciberacoso, p8_accion_al_ver, p9_importancia_ayuda, p10_confianza_adulto,
    p11_informacion_colegio, p12_fuente_principal_info, p13_protocolo_colegio, p14_utilidad_charlas, p15_rol_colegio_prevencion,
    p16_horas_pantalla, p17_red_social_mas_usada, p18_seguridad_online, p19_denunciarias_online, p20_mejorar_seguridad_online,
    sugerencias_adicionales
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("<div class='error-message container'><h2>Error al preparar la consulta SQL</h2><p>Por favor, revisa los nombres de las columnas y la tabla en tu script PHP contra tu base de datos.</p><p>Detalle técnico: " . $conn->error . "</p><a href='index.html'>Volver a la encuesta</a></div>");
}

// 's' indica que el tipo de dato es string para todas las variables
$stmt->bind_param("sssssssssssssssssssss",
    $p1_sufrido_ciberacoso, $p2_tipo_mas_comun, $p3_donde_ciberacoso, $p4_quien_acosa, $p5_sentimiento_sufrir,
    $p6_primera_reaccion, $p7_visto_ciberacoso, $p8_accion_al_ver, $p9_importancia_ayuda, $p10_confianza_adulto,
    $p11_informacion_colegio, $p12_fuente_principal_info, $p13_protocolo_colegio, $p14_utilidad_charlas, $p15_rol_colegio_prevencion,
    $p16_horas_pantalla, $p17_red_social_mas_usada, $p18_seguridad_online, $p19_denunciarias_online, $p20_mejorar_seguridad_online,
    $sugerencias_adicionales
);

// 5. Ejecutar la consulta y dar retroalimentación al usuario
if ($stmt->execute()) {
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Encuesta Enviada</title>
        <link rel='stylesheet' href='style.css'>
        <style>
            .thank-you-message {
                text-align: center;
                margin-top: 50px;
                padding: 30px;
                background-color: #e6ffe6;
                border: 1px solid #c6f6c6;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }
            .thank-you-message h2 {
                color: #28a745;
                margin-bottom: 20px;
            }
            .thank-you-message p {
                font-size: 1.1em;
                color: #333;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='thank-you-message'>
                <h2>¡Encuesta Enviada Correctamente!</h2>
                <p>¡Muchas gracias por tu valiosa participación! Tus respuestas han sido guardadas de forma anónima y nos ayudarán a entender mejor el ciberacoso y ciberbullying en tu colegio.</p>
                <div class='back-button-container'>
                    <a href='index.html' class='back-button'>Volver a la Encuesta</a>
                    <a href='estadisticas.php' class='back-button'>Ver Estadísticas</a>
                </div>
            </div>
        </div>
    </body>
    </html>";
} else {
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Error en Envío</title>
        <link rel='stylesheet' href='style.css'>
        <style>
            .error-message {
                text-align: center;
                margin-top: 50px;
                padding: 30px;
                background-color: #ffe6e6;
                border: 1px solid #f6c6c6;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }
            .error-message h2 {
                color: #dc3545;
                margin-bottom: 20px;
            }
            .error-message p {
                font-size: 1.1em;
                color: #333;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='error-message'>
                <h2>Error al Enviar Encuesta</h2>
                <p>Lo sentimos, hubo un problema al guardar tus respuestas. Por favor, inténtalo de nuevo más tarde.</p>
                <p>Detalle del error: " . $stmt->error . "</p>
                <div class='back-button-container'>
                    <a href='index.html' class='back-button'>Volver a la Encuesta</a>
                </div>
            </div>
        </div>
    </body>
    </html>";
}

$stmt->close();
$conn->close();
?>
