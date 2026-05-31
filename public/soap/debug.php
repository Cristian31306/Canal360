<?php
header('Content-Type: text/plain');
echo "=== CANAL360 SOAP DIAGNOSTICS ===\n\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "SoapClient Class Exists: " . (class_exists('SoapClient') ? 'YES' : 'NO') . "\n";
echo "SoapServer Class Exists: " . (class_exists('SoapServer') ? 'YES' : 'NO') . "\n";
echo "\nLoaded Extensions:\n";
$extensions = get_loaded_extensions();
sort($extensions);
foreach ($extensions as $ext) {
    echo " - $ext\n";
}
