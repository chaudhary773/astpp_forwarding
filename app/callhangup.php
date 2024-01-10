<?php
require_once 'iyerfreeswitch.php';
header("Refresh: 5");
$freeswitch = new Freeswitches();
$connect = $freeswitch->connect("127.0.0.1","8021","ClueCon");
if ($connect) {
    $uuid = 'dda5b4c6-9779-11ee-9885-4bb2717d5a1f';
    $freeswitch->execute("hangup","",$uuid);
}
$freeswitch->disconnect();
