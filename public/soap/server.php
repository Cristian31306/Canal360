<?php

class PolicyValidationService {
    /**
     * Valida una póliza a través de un documento y valor asegurado.
     * Si el documento empieza con 999, simula un siniestro/rechazo.
     */
    public function validatePolicy($documento, $valorAsegurado) {
        $res = new stdClass();
        if (str_starts_with($documento, '999')) {
            $res->status = 'denied';
            $res->message = 'Siniestro Activo Detectado: El cliente posee siniestros vigentes reportados en centrales de riesgo.';
        } else {
            $res->status = 'approved';
            $res->message = 'Cliente sin siniestros vigentes. Emisión aprobada por el MVPS.';
        }
        return $res;
    }
}

// Servir el WSDL dinámicamente si se solicita ?wsdl
if (isset($_GET['wsdl'])) {
    header("Content-Type: text/xml");
    $wsdlPath = __DIR__ . '/service.wsdl';
    if (!file_exists($wsdlPath)) {
        header("HTTP/1.0 404 Not Found");
        echo "<error>WSDL file not found</error>";
        exit;
    }
    $wsdl = file_get_contents($wsdlPath);
    // Hacer que la dirección del servicio sea dinámica según el host actual
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https" : "http";
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    $location = "$protocol://$host/soap/server.php";
    echo str_replace('http://localhost/soap/server.php', $location, $wsdl);
    exit;
}

// Iniciar el servidor SOAP
ini_set("soap.wsdl_cache_enabled", "0");
try {
    $server = new SoapServer(__DIR__ . '/service.wsdl', [
        'cache_wsdl' => WSDL_CACHE_NONE
    ]);
    $server->setClass('PolicyValidationService');
    $server->handle();
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo $e->getMessage();
}
