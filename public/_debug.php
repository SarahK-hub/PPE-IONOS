<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

$logDir = __DIR__ . '/../ppe_logs';

$files = [
  $logDir . '/ping.log',
  $logDir . '/raw.log',
  $logDir . '/trace.log',
  $logDir . '/auth.log',
  $logDir . '/dashboard.log',
  $logDir . '/php-auth-exception.log',
  $logDir . '/php-dashboard-exception.log',
  $logDir . '/php-fatal.log',
  $logDir . '/php-exception.log',
  $logDir . '/post-ok.log',
  $logDir . '/php-fraisforfait-exception.log',

];


foreach ($files as $f) {
    echo "===== " . basename($f) . " =====\n";
    if (is_file($f)) {
        $c = file_get_contents($f);
        echo substr($c ?: '', max(0, strlen($c ?: '') - 20000));
        echo "\n\n";
    } else {
        echo "absent\n\n";
    }
}