<?php

namespace App\Services;
use App\Services\Freeswitches;

header("Refresh: 5");
class CallHangup
{
    public static function hangup($uuid)
    {
        $freeswitch = new Freeswitches();
        $connect = $freeswitch->connect("127.0.0.1","8021","ClueCon");
        if ($connect) {
            $freeswitch->execute("hangup","",$uuid);
        }
        $freeswitch->disconnect();
    }

}
