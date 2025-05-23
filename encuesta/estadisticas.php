<?php
// estadisticas.php

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
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Error de conexión a la base de datos: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Error grave de conexión: " . $e->getMessage());
}

// 3. Recopilar datos de la base de datos
// Aquí seleccionamos todas las columnas de preguntas que queremos analizar.
// Excluimos 'id' y 'fecha_envio' y 'sugerencias_adicionales' por ser metadatos o texto libre.
$sql = "SELECT
    p1_sufrido_ciberacoso, p2_tipo_mas_comun, p3_donde_ciberacoso, p4_quien_acosa, p5_sentimiento_sufrir,
    p6_primera_reaccion, p7_visto_ciberacoso, p8_accion_al_ver, p9_importancia_ayuda, p10_confianza_adulto,
    p11_informacion_colegio, p12_fuente_principal_info, p13_protocolo_colegio, p14_utilidad_charlas, p15_rol_colegio_prevencion,
    p16_horas_pantalla, p17_red_social_mas_usada, p18_seguridad_online, p19_denunciarias_online, p20_mejorar_seguridad_online
FROM respuestas_adolescentes_simple";

$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
$conn->close();

// 4. Procesar los datos para las estadísticas
$statistics = [];
$total_responses = count($data);

if ($total_responses > 0) {
    // Definimos las preguntas y sus posibles valores para mostrar etiquetas amigables
    $questions_map = [
        'p1_sufrido_ciberacoso' => [
            'label' => '1. ¿Has sufrido ciberacoso?',
            'options' => [
                'si' => 'Sí',
                'no' => 'No',
                'no_estoy_seguro' => 'No estoy seguro/a'
            ]
        ],
        'p2_tipo_mas_comun' => [
            'label' => '2. Tipo de ciberacoso más común',
            'options' => [
                'mensajes_ofensivos' => 'Mensajes ofensivos',
                'rumores' => 'Difusión de rumores',
                'fotos_sin_permiso' => 'Fotos sin permiso',
                'exclusion_grupos' => 'Exclusión de grupos',
                'suplantacion' => 'Suplantación de identidad',
                'otro' => 'Otro tipo'
            ]
        ],
        'p3_donde_ciberacoso' => [
            'label' => '3. ¿Dónde ocurre más el ciberacoso?',
            'options' => [
                'redes_sociales' => 'Redes sociales',
                'apps_mensajeria' => 'Apps de mensajería',
                'juegos_online' => 'Juegos online',
                'dentro_colegio' => 'Relacionado con el colegio',
                'otro' => 'Otra plataforma'
            ]
        ],
        'p4_quien_acosa' => [
            'label' => '4. ¿Quiénes suelen ejercer ciberacoso?',
            'options' => [
                'companeros_colegio' => 'Compañeros del colegio',
                'amigos_conocidos' => 'Amigos o conocidos',
                'desconocidos_online' => 'Desconocidos online',
                'ex_parejas' => 'Ex-parejas',
                'profesores' => 'Profesores u otros adultos',
                'otro' => 'Otro'
            ]
        ],
        'p5_sentimiento_sufrir' => [
            'label' => '5. Sentimiento principal al sufrir ciberacoso',
            'options' => [
                'tristeza_depresion' => 'Tristeza o depresión',
                'rabia_frustracion' => 'Rabia o frustración',
                'miedo_ansiedad' => 'Miedo o ansiedad',
                'verguenza_humillacion' => 'Vergüenza o humillación',
                'indiferencia' => 'Indiferencia',
                'no_aplica' => 'No aplica'
            ]
        ],
        'p6_primera_reaccion' => [
            'label' => '6. Primera reacción al sufrir ciberacoso',
            'options' => [
                'ignore_bloquee' => 'Ignoré o bloqueé',
                'conte_adulto' => 'Conté a un adulto',
                'lo_enfrente' => 'Lo enfrenté',
                'no_hice_nada' => 'No hice nada',
                'no_aplica' => 'No aplica'
            ]
        ],
        'p7_visto_ciberacoso' => [
            'label' => '7. ¿Has sido testigo de ciberacoso?',
            'options' => [
                'si' => 'Sí, varias veces',
                'no' => 'No, nunca',
                'no_estoy_seguro' => 'No estoy seguro/a'
            ]
        ],
        'p8_accion_al_ver' => [
            'label' => '8. Acción al ver ciberacoso',
            'options' => [
                'denuncie' => 'Denuncié',
                'hable_victima' => 'Hablé con la víctima',
                'ignore' => 'Ignoré',
                'defendi_victima' => 'Defendí a la víctima',
                'no_aplica' => 'No aplica'
            ]
        ],
        'p9_importancia_ayuda' => [
            'label' => '9. Importancia de buscar ayuda',
            'options' => [
                'muy_importante' => 'Muy importante',
                'algo_importante' => 'Algo importante',
                'poco_importante' => 'Poco importante',
                'nada_importante' => 'Nada importante'
            ]
        ],
        'p10_confianza_adulto' => [
            'label' => '10. Confianza en adultos para ayudar',
            'options' => [
                'mucha_confianza' => 'Mucha confianza',
                'algo_confianza' => 'Algo de confianza',
                'poca_confianza' => 'Poca confianza',
                'nada_confianza' => 'Nada de confianza'
            ]
        ],
        'p11_informacion_colegio' => [
            'label' => '11. Información del colegio sobre ciberacoso',
            'options' => [
                'mucha_informacion' => 'Mucha información',
                'algo_informacion' => 'Algo de información',
                'poca_informacion' => 'Poca información',
                'nada_informacion' => 'Nada de información'
            ]
        ],
        'p12_fuente_principal_info' => [
            'label' => '12. Fuente principal de información',
            'options' => [
                'colegio_profesores' => 'Colegio/Profesores',
                'familiares' => 'Familiares',
                'internet_redes' => 'Internet/Redes',
                'amigos_companeros' => 'Amigos/Compañeros',
                'noticias_tv' => 'Noticias/TV'
            ]
        ],
        'p13_protocolo_colegio' => [
            'label' => '13. ¿Conoces el protocolo del colegio?',
            'options' => [
                'si_lo_conozco' => 'Sí, lo conozco',
                'no_lo_conozco' => 'No, no lo conozco',
                'no_estoy_seguro' => 'No estoy seguro/a'
            ]
        ],
        'p14_utilidad_charlas' => [
            'label' => '14. Utilidad de las charlas del colegio',
            'options' => [
                'muy_utiles' => 'Muy útiles',
                'utiles' => 'Útiles',
                'poco_utiles' => 'Poco útiles',
                'nada_utiles' => 'Nada útiles'
            ]
        ],
        'p15_rol_colegio_prevencion' => [
            'label' => '15. Importancia del rol del colegio en prevención',
            'options' => [
                'muy_importante' => 'Muy importante',
                'importante' => 'Importante',
                'neutro' => 'Neutro',
                'poco_importante' => 'Poco importante'
            ]
        ],
        'p16_horas_pantalla' => [
            'label' => '16. Horas al día en pantallas (no estudio)',
            'options' => [
                'mas_4_horas' => 'Más de 4 horas',
                '2_4_horas' => 'Entre 2 y 4 horas',
                '1_2_horas' => 'Entre 1 y 2 horas',
                'menos_1_hora' => 'Menos de 1 hora'
            ]
        ],
        'p17_red_social_mas_usada' => [
            'label' => '17. Red social/app más usada',
            'options' => [
                'tiktok' => 'TikTok',
                'instagram' => 'Instagram',
                'whatsapp' => 'WhatsApp',
                'youtube' => 'YouTube',
                'discord' => 'Discord',
                'twitter' => 'X (antes Twitter)',
                'facebook' => 'Facebook',
                'otra' => 'Otra'
            ]
        ],
        'p18_seguridad_online' => [
            'label' => '18. ¿Qué tan seguro/a te sientes online?',
            'options' => [
                'muy_seguro' => 'Muy seguro/a',
                'seguro' => 'Seguro/a',
                'ni_seguro_ni_inseguro' => 'Ni seguro/a ni inseguro/a',
                'inseguro' => 'Inseguro/a',
                'muy_inseguro' => 'Muy inseguro/a'
            ]
        ],
        'p19_denunciarias_online' => [
            'label' => '19. ¿Denunciarías ciberacoso online?',
            'options' => [
                'si_lo_denunciaria' => 'Sí, lo denunciaría',
                'no_es_mi_asunto' => 'No es mi asunto',
                'no_se_como' => 'No sé cómo'
            ]
        ],
        'p20_mejorar_seguridad_online' => [
            'label' => '20. ¿Qué acción mejoraría la seguridad online?',
            'options' => [
                'mas_charlas' => 'Más charlas/talleres',
                'buzon_anonimo' => 'Buzón anónimo',
                'sanciones_claras' => 'Sanciones claras',
                'apps_mas_seguras' => 'Apps más seguras',
                'padres_mas_informados' => 'Padres más informados',
                'otro' => 'Otro'
            ]
        ]
    ];

    foreach ($questions_map as $column => $q_info) {
        $statistics[$column] = [
            'label' => $q_info['label'],
            'counts' => [],
            'percentages' => []
        ];

        // Inicializar los contadores para cada opción
        foreach ($q_info['options'] as $value => $label) {
            $statistics[$column]['counts'][$label] = 0;
        }

        // Contar las respuestas
        foreach ($data as $row) {
            $value = $row[$column];
            $label = $q_info['options'][$value] ?? $value; // Usar la etiqueta amigable o el valor si no está mapeado
            $statistics[$column]['counts'][$label]++;
        }

        // Calcular porcentajes
        foreach ($statistics[$column]['counts'] as $label => $count) {
            $percentage = ($total_responses > 0) ? round(($count / $total_responses) * 100, 2) : 0;
            $statistics[$column]['percentages'][$label] = $percentage;
        }
    }
}

// Convertir los datos a JSON para que JavaScript los pueda leer
$json_statistics = json_encode($statistics);
$json_total_responses = json_encode($total_responses);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de la Encuesta de Ciberacoso</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 100%;
            margin-bottom: 40px;
            padding: 20px;
            background-color: #fcfcfc;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .chart-container h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
            font-size: 1.8em;
        }
        canvas {
            max-height: 400px; /* Limita la altura de los gráficos */
        }
        .total-responses {
            text-align: center;
            font-size: 1.3em;
            font-weight: bold;
            margin-bottom: 40px;
            color: #34495e;
        }
        /* back-button-container y back-button estilos ya están en style.css */
    </style>
</head>
<body>
    <div class="container">
        <h1>Resultados y Estadísticas de la Encuesta</h1>
        <p class="total-responses">Total de respuestas recibidas: <span id="totalResponsesCount"></span></p>

        <div id="chartsContainer">
            </div>

        <div class="back-button-container">
            <a href="index.html" class="back-button">Volver a la Encuesta</a>
        </div>
    </div>

    <script>
        // Paso los datos de PHP a JavaScript
        const statistics = <?php echo $json_statistics; ?>;
        const totalResponses = <?php echo $json_total_responses; ?>;

        document.getElementById('totalResponsesCount').textContent = totalResponses;
        const chartsContainer = document.getElementById('chartsContainer');

        const backgroundColors = [
            'rgba(52, 152, 219, 0.7)',  // Azul
            'rgba(46, 204, 113, 0.7)',  // Verde
            'rgba(241, 196, 15, 0.7)',  // Amarillo
            'rgba(231, 76, 60, 0.7)',   // Rojo
            'rgba(155, 89, 182, 0.7)',  // Púrpura
            'rgba(26, 188, 156, 0.7)',  // Turquesa
            'rgba(243, 156, 18, 0.7)',  // Naranja
            'rgba(52, 73, 94, 0.7)',    // Gris oscuro
            'rgba(149, 165, 166, 0.7)', // Gris claro
            'rgba(192, 57, 43, 0.7)'    // Rojo ladrillo
        ];
        const borderColors = [
            'rgba(52, 152, 219, 1)',
            'rgba(46, 204, 113, 1)',
            'rgba(241, 196, 15, 1)',
            'rgba(231, 76, 60, 1)',
            'rgba(155, 89, 182, 1)',
            'rgba(26, 188, 156, 1)',
            'rgba(243, 156, 18, 1)',
            'rgba(52, 73, 94, 1)',
            'rgba(149, 165, 166, 1)',
            'rgba(192, 57, 43, 1)'
        ];

        // Función para generar colores aleatorios (si necesitas más de 10)
        function getRandomColor() {
            const r = Math.floor(Math.random() * 255);
            const g = Math.floor(Math.random() * 255);
            const b = Math.floor(Math.random() * 255);
            return `rgba(${r}, ${g}, ${b}, 0.7)`;
        }
        function getRandomBorderColor(color) {
            return color.replace('0.7)', '1)');
        }

        // Itera sobre las estadísticas y crea un gráfico para cada pregunta
        for (const questionKey in statistics) {
            if (statistics.hasOwnProperty(questionKey)) {
                const questionData = statistics[questionKey];

                // Crear un div para cada gráfico
                const chartDiv = document.createElement('div');
                chartDiv.className = 'chart-container';
                chartsContainer.appendChild(chartDiv);

                // Título de la pregunta
                const title = document.createElement('h2');
                title.textContent = questionData.label;
                chartDiv.appendChild(title);

                // Canvas donde se dibujará el gráfico
                const canvas = document.createElement('canvas');
                canvas.id = `chart-${questionKey}`;
                chartDiv.appendChild(canvas);

                const ctx = canvas.getContext('2d');

                // Datos para Chart.js
                const labels = Object.keys(questionData.counts);
                const counts = Object.values(questionData.counts);
                const percentages = Object.values(questionData.percentages);

                // Crear array de colores para este gráfico
                const chartBackgroundColors = labels.map((_, i) => backgroundColors[i % backgroundColors.length]);
                const chartBorderColors = labels.map((_, i) => borderColors[i % borderColors.length]);


                new Chart(ctx, {
                    type: 'bar', // Puedes cambiar a 'pie' o 'doughnut' para otras visualizaciones
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Número de Respuestas',
                            data: counts,
                            backgroundColor: chartBackgroundColors,
                            borderColor: chartBorderColors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // Permite que el gráfico no sea cuadrado
                        plugins: {
                            legend: {
                                display: false // No mostrar leyenda para gráficos de barra simples
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        // Muestra el conteo y el porcentaje
                                        const value = context.raw;
                                        const percentage = percentages[context.dataIndex];
                                        return `${label}${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Número de Respuestas'
                                },
                                ticks: {
                                    stepSize: 1 // Asegura que los ticks sean números enteros
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Opciones de Respuesta'
                                }
                            }
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>
