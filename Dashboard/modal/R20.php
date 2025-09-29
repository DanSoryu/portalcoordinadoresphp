<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../controller/ClassOps.php");
    require_once("../../vendor/autoload.php"); // mPDF

    session_start();

    if (!isset($_GET['Folio_Pisa'])) { 
        http_response_code(400); 
        exit('Falta ?Folio_Pisa'); 
    }
    $Folio_Pisa = $_GET['Folio_Pisa'];

    $modal = new Ops();
    $filas = $modal->getOrden($Folio_Pisa);
    if (!$filas || !is_array($filas) || count($filas) === 0) {
        http_response_code(404);
        exit('No se encontró el folio solicitado');
    }
    $d = $filas[0];

    // Helpers
    function v($arr, $k, $def=''){ 
        return isset($arr[$k]) ? $arr[$k] : $def; 
    }
    $cliente = trim(v($d,'Cliente_Titular').' '.v($d,'Apellido_Paterno_Titular').' '.v($d,'Apellido_Materno_Titular'));

    // Logo
    $logo_path = realpath(__DIR__ . "/../img/enlace.png");
    $logo_exists = $logo_path && file_exists($logo_path);

    // Crear mPDF con márgenes optimizados
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 10,
        'margin_right' => 10,
        'margin_top' => 10,
        'margin_bottom' => 10
    ]);

    // Formatear fecha
    $fecha_formateada = date('d/m/Y');
    if (!empty($d['Fecha_Coordiapp'])) {
        $fecha_formateada = date('d/m/Y', strtotime($d['Fecha_Coordiapp']));
    }

    // Determinar estado y color dinámico
    $estatus = strtoupper(trim(v($d,'Estatus_Orden')));
    $estatus_color = '#28a745'; // Verde por defecto
    $estatus_bg = '#d4edda';
    $estatus_symbol = '✓';
    
    if (strpos($estatus, 'PENDIENTE') !== false || strpos($estatus, 'PROCESO') !== false) {
        $estatus_color = '#ffc107';
        $estatus_bg = '#fff3cd';
        $estatus_symbol = '⚠';
    } elseif (strpos($estatus, 'CANCELAD') !== false || strpos($estatus, 'ERROR') !== false) {
        $estatus_color = '#dc3545';
        $estatus_bg = '#f8d7da';
        $estatus_symbol = '✗';
    }

    // Tecnología con color dinámico
    $tecnologia = strtoupper(trim(v($d,'Tecnologia')));
    $tech_color = '#0066cc';
    $tech_symbol = '🌐';
    if (strpos($tecnologia, 'FIBRA') !== false) {
        $tech_color = '#17a2b8';
        $tech_symbol = '💡';
    } elseif (strpos($tecnologia, 'COBRE') !== false) {
        $tech_color = '#fd7e14';
        $tech_symbol = '🔗';
    }

    // Generar checkboxes usando símbolos Unicode
    $fibra_checked = (stripos(strtolower(v($d,'Tecnologia')), 'fibra') !== false) ? '☑' : '☐';
    $cobre_checked = (stripos(strtolower(v($d,'Tecnologia')), 'cobre') !== false) ? '☑' : '☐';
    $adsl_checked = (stripos(strtolower(v($d,'Tecnologia')), 'adsl') !== false) ? '☑' : '☐';

    // Logo
    $logo_html = '';
    if ($logo_exists) {
        $logo_data = base64_encode(file_get_contents($logo_path));
        $logo_extension = pathinfo($logo_path, PATHINFO_EXTENSION);
        $logo_mime = ($logo_extension === 'png') ? 'image/png' : 'image/jpeg';
        $logo_base64 = 'data:' . $logo_mime . ';base64,' . $logo_data;
        
        $logo_html = '<img src="' . $logo_base64 . '" style="height: 60px; width: auto; margin-bottom: 8px;" alt="Logo Enlace Digital">';
    } else {
        $logo_html = '<div class="logo-text">ENLACE DIGITAL</div>';
    }

    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { 
                font-family: Arial, sans-serif; 
                font-size: 10pt; 
                margin: 0; 
                padding: 0;
                line-height: 1.3;
                color: #2c3e50;
            }
            
            .header {
                text-align: center;
                border-bottom: 3px solid #0066cc;
                padding: 15px 20px 12px 20px;
                margin-bottom: 18px;
                position: relative;
                background: white;
            }
            
            .logo-text {
                font-size: 18pt;
                font-weight: bold;
                color: #0066cc;
                margin-bottom: 8px;
                letter-spacing: 2px;
            }
            
            .subtitle {
                font-size: 11pt;
                color: #333;
                font-weight: bold;
                margin: 3px 0;
            }
            
            .folio-box {
                position: absolute;
                top: 15px;
                right: 15px;
                border: 3px solid #0066cc;
                padding: 10px 12px;
                text-align: center;
                background-color: #f0f8ff;
                border-radius: 8px;
                font-size: 8pt;
            }
            
            .content {
                padding: 0 10px;
            }
            
            .section {
                margin-bottom: 12px;
            }
            
            .section-header {
                background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);
                padding: 8px 12px;
                border-left: 4px solid #0066cc;
                font-weight: 600;
                font-size: 9pt;
                color: #495057;
                margin-bottom: 6px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .section-icon {
                display: inline-block;
                width: 18px;
                height: 18px;
                background: #0066cc;
                color: white;
                text-align: center;
                border-radius: 50%;
                font-size: 8pt;
                line-height: 18px;
                margin-right: 8px;
                vertical-align: middle;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 8px;
            }
            
            .info-table td {
                padding: 6px 8px;
                border: 1px solid #dee2e6;
                vertical-align: top;
                font-size: 9pt;
            }
            
            .label {
                background: #f8f9fa;
                font-weight: 600;
                width: 90px;
                font-size: 8pt;
                color: #6c757d;
                text-transform: uppercase;
                letter-spacing: 0.3px;
            }
            
            .status-badge {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 12px;
                font-weight: 600;
                font-size: 8pt;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .tech-badge {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 12px;
                font-weight: 600;
                font-size: 8pt;
                color: white;
            }
            
            .checkbox-section {
                background: #f8f9fa;
                border: 2px solid #dee2e6;
                border-radius: 8px;
                padding: 10px;
                margin: 10px 0;
            }
            
            .checkbox-title {
                font-weight: 600;
                margin-bottom: 6px;
                font-size: 8pt;
                color: #495057;
                text-transform: uppercase;
            }
            
            .checkbox-line {
                margin: 4px 0;
                font-size: 9pt;
                font-weight: 500;
            }
            
            .checkbox-line span {
                margin-right: 6px;
                font-size: 11pt;
                color: #0066cc;
                font-weight: bold;
            }
            
            .signature-section {
                margin-top: 15px;
                text-align: center;
                background: #f8f9fa;
                padding: 15px;
                border-radius: 8px;
                border: 2px dashed #0066cc;
            }
            
            .signature-line {
                border-bottom: 2px solid #0066cc;
                width: 220px;
                margin: 15px auto 8px auto;
                height: 35px;
            }
            
            .footer-info {
                background: #0066cc;
                color: white;
                padding: 6px 10px;
                font-size: 7pt;
                text-align: center;
                margin-top: 12px;
                border-radius: 4px;
            }
            
            .highlight {
                font-weight: 600;
                color: #0066cc;
            }
            
            .value-important {
                font-weight: 600;
                font-size: 9pt;
                color: #2c3e50;
            }
            
            .icon-text {
                font-size: 10pt;
                margin-right: 5px;
                color: #0066cc;
            }
        </style>
    </head>
    <body>
        <!-- Folio en esquina -->
        <div class="folio-box">
            <div style="font-weight: bold; font-size: 7pt;">FOLIO PISA</div>
            <div style="font-size: 12pt; font-weight: bold; color: #0066cc; margin: 3px 0;">' . htmlspecialchars(v($d,'Folio_Pisa')) . '</div>
            <div style="font-size: 7pt; color: #666;">' . $fecha_formateada . '</div>
        </div>

        <!-- Header original -->
        <div class="header">
            ' . $logo_html . '
            <div class="subtitle">REPORTE DE INSTALACIÓN R-20</div>
        </div>

        <div class="content">
            <!-- Información del Cliente -->
            <div class="section">
                <div class="section-header">
                    INFORMACIÓN DEL CLIENTE
                </div>
                <table class="info-table">
                    <tr>
                        <td class="label">Cliente:</td>
                        <td class="value-important">' . htmlspecialchars($cliente) . '</td>
                        <td class="label">Teléfono:</td>
                        <td class="highlight">' . htmlspecialchars(v($d,'Telefono')) . '</td>
                    </tr>
                    <tr>
                        <td class="label">Dirección:</td>
                        <td colspan="3">' . htmlspecialchars(v($d,'Direccion_Cliente')) . '</td>
                    </tr>
                    <tr>
                        <td class="label">Fecha Instalación:</td>
                        <td class="highlight">' . $fecha_formateada . '</td>
                        <td class="label"></td>
                        <td></td>
                    </tr>
                </table>
            </div>

            <!-- Configuración Técnica -->
            <div class="section">
                <div class="section-header">
                    CONFIGURACIÓN TÉCNICA
                </div>
                <table class="info-table">
                    <tr>
                        <td class="label">Tecnología:</td>
                        <td><span class="tech-badge" style="background: ' . $tech_color . ';">' . $tech_symbol . ' ' . htmlspecialchars(v($d,'Tecnologia')) . '</span></td>
                        <td class="label">Tipo Tarea:</td>
                        <td>' . htmlspecialchars(v($d,'Tipo_Tarea')) . '</td>
                    </tr>
                    <tr>
                        <td class="label">Tipo Instalación:</td>
                        <td>' . htmlspecialchars(v($d,'Tipo_Instalacion')) . '</td>
                        <td class="label">Estatus:</td>
                        <td><span class="status-badge" style="background: ' . $estatus_bg . '; color: ' . $estatus_color . ';">' . $estatus_symbol . ' ' . $estatus . '</span></td>
                    </tr>
                </table>
            </div>

            <!-- Tipo de Conexión -->
            <div class="checkbox-section">
                <div class="checkbox-title">TIPO DE CONEXIÓN IMPLEMENTADA:</div>
                <div class="checkbox-line"><span>' . $fibra_checked . '</span> FIBRA ÓPTICA</div>
                <div class="checkbox-line"><span>' . $cobre_checked . '</span> COBRE</div>
                <div class="checkbox-line"><span>' . $adsl_checked . '</span> ADSL</div>
            </div>

            <!-- Equipos y Configuración -->
            <div class="section">
                <div class="section-header">
                    EQUIPOS Y CONFIGURACIÓN
                </div>
                <table class="info-table">
                    <tr>
                        <td class="label">Distrito:</td>
                        <td><span class="tech-badge" style="background: ' . $tech_color . ';">' . htmlspecialchars(v($d,'Distrito')) . '</span></td>
                        <td class="label">No. Serie ONT:</td>
                        <td class="value-important">' . htmlspecialchars(v($d,'Ont')) . '</td>
                    </tr>
                    <tr>
                        <td class="label">Terminal:</td>
                        <td>' . htmlspecialchars(v($d,'Terminal')) . '</td>
                        <td class="label">Puerto:</td>
                        <td class="highlight">' . htmlspecialchars(v($d,'Puerto')) . '</td>
                    </tr>
                    <tr>
                        <td class="label">Metraje Fibra:</td>
                        <td class="value-important">' . htmlspecialchars(v($d,'Metraje')) . ' metros</td>
                        <td class="label">COPE:</td>
                        <td class="highlight">' . htmlspecialchars(v($d,'COPE')) . '</td>
                    </tr>
                </table>
            </div>

            <!-- Técnico -->
            <div class="section">
                <div class="section-header">
                    TÉCNICO RESPONSABLE
                </div>
                <table class="info-table">
                    <tr>
                        <td class="label">Técnico Instalador:</td>
                        <td class="value-important">' . htmlspecialchars(v($d,'Nombre_T') . ' ' . v($d,'Apellidos_T')) . '</td>
                        <td class="label">Expediente:</td>
                        <td class="highlight">' . htmlspecialchars(v($d,'NExpediente')) . '</td>
                    </tr>
                </table>
            </div>';

    // Observaciones solo si existen
    if (!empty($d['Observaciones'])) {
        $html .= '
            <div class="section">
                <div class="section-header">
                    OBSERVACIONES
                </div>
                <div style="padding: 8px; background: #fff3cd; border-radius: 6px; font-size: 8pt; border: 1px solid #ffeaa7;">
                    ' . nl2br(htmlspecialchars($d['Observaciones'])) . '
                </div>
            </div>';
    }

    $html .= '
            <!-- Firma del Cliente -->
            <div class="signature-section">
                <div style="font-weight: 600; margin-bottom: 10px; font-size: 9pt; color: #495057;">
                    CONFORMIDAD DEL CLIENTE
                </div>
                <div class="signature-line"></div>
                <div style="font-weight: 600; font-size: 8pt; margin-top: 6px;">FIRMA Y NOMBRE DEL CLIENTE</div>
                <div style="font-size: 7pt; color: #6c757d; margin-top: 3px;">
                    ✓ Acepto que el servicio fue instalado correctamente y funciona según lo esperado
                </div>
            </div>
            
            <!-- Footer -->
            <div class="footer-info">
                <strong>REPORTE GENERADO:</strong> Folio: ' . htmlspecialchars(v($d,'Folio_Pisa')) . ' | 
                ' . date('d/m/Y H:i') . ' | 
                Sistema COORDIAPP ERP | 
                Enlace Digital
            </div>
        </div>
    </body>
    </html>';

    $mpdf->WriteHTML($html);
    $mpdf->SetTitle("R20_" . $d['Folio_Pisa']);

    // Generar PDF
    $mpdf->Output("R20_" . $d['Folio_Pisa'] . ".pdf", \Mpdf\Output\Destination::INLINE);
?>