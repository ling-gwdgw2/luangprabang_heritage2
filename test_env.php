<?php
echo "getenv('MYSQLPORT'): " . getenv('MYSQLPORT') . "\n";
echo "_SERVER['MYSQLPORT']: " . ($_SERVER['MYSQLPORT'] ?? 'not set') . "\n";
echo "_ENV['MYSQLPORT']: " . ($_ENV['MYSQLPORT'] ?? 'not set') . "\n";
?>
