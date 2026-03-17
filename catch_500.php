<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);
if ($response->getStatusCode() === 500) {
    if (isset($response->exception) && $response->exception) {
        echo 'EXCEPTION: ' . $response->exception->getMessage() . ' in ' . $response->exception->getFile() . ':' . $response->exception->getLine() . "\n";
        echo $response->exception->getTraceAsString();
    } else {
        echo '500 ERROR NO EXCEPTION';
    }
} else {
    echo 'STATUS: ' . $response->getStatusCode();
}
