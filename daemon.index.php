<?php

$argc = intval($_SERVER['argc']);
if ($argc < 2) {
    exit;
}

$pno = null;
$argv = $_SERVER['argv'];
for ($i = 1; $i < $argc; $i++) {
    if (empty($argv[$i])) {
        continue;
    }
    $pair = explode('=', $argv[$i]);
    if (count($pair) < 2) {
        continue;
    }
    if (strtolower(trim($pair[0])) == 'pno') {
        $pno = intval($pair[1]);
    }
}
if (empty($pno)) {
    exit;
}
// exit('time=' . date('Y-m-d H:i:s') . " pno=${pno}" . PHP_EOL);

define('BASEPATH', realpath(__DIR__));

$lockDir = BASEPATH . '/locks';
$lockFile = "${lockDir}/mq_process_${pno}.lock";
if (file_exists($lockFile)) {
    exit;
}
if (!is_dir($lockDir)) {
    mkdir($lockDir, 0700, true);
}
touch($lockFile);
if (!file_exists($lockFile)) {
    exit;
}
register_shutdown_function(function ($lkf) {
    if (file_exists($lkf)) {
        unlink($lkf);
    }
}, $lockFile);

// require BASEPATH . '/config/config.php';

try {
    /** @var \Composer\Autoload\ClassLoader $loader */
    $loader = require BASEPATH . '/vendor/autoload.php';
    // $loader->addPsr4('WgxMqDemo\\', __DIR__);

    \WgxMq\MqManager::init(null, new \WgxMq\MqProcessorMysqlBackend(\Wgx\PDOEx::getPdoObj('mqdb')));
    \WgxMq\MqManager::getMqProcessor()->process($pno);
} catch (\Exception $e) {
    // 记日志
}
// echo 'end';