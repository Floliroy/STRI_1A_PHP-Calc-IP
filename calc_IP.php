<?php 
$GLOBALS["Netmask"] = array(
    0 => "0.0.0.0", 1 => "128.0.0.0", 2 => "192.0.0.0", 3 => "224.0.0.0", 4 => "240.0.0.0", 5 => "248.0.0.0", 6 => "252.0.0.0", 7 => "254.0.0.0",
    8 => "255.0.0.0", 9 => "255.128.0.0", 10 => "255.192.0.0", 11 => "255.224.0.0", 12 => "255.240.0.0", 13 => "255.248.0.0", 14 => "255.252.0.0", 15 => "255.254.0.0",
    16 => "255.255.0.0", 17 => "255.255.128.0", 18 => "255.255.192.0", 19 => "255.255.224.0", 20 => "255.255.240.0", 21 => "255.255.248.0", 22 => "255.255.252.0", 23 => "255.255.254.0",
    24 => "255.255.255.0", 25 => "255.255.255.128", 26 => "255.255.255.192", 27 => "255.255.255.224", 28 => "255.255.255.240", 29 => "255.255.255.248", 30 => "255.255.255.252", 31 => "255.255.255.254", 32 => "255.255.255.255"       
);

function inRange($nombre, $min, $max){
    if($nombre > $min && $nombre < $max){
        return true;
    }
    return false;
}
function getClasseAdresseIp($ip){
    $part = explode(".", $ip);
    if(inRange($part[0], 0, 128)){
        return "A";
    }elseif(inRange($part[0], 127, 192)){
        return "B";
    }elseif(inRange($part[0], 191, 224)){
        return "C";
    }elseif(inRange($part[0], 223, 240)){
        return "D";
    }else{
        return "E";
    }
}
function isAdresseIpValide($ip){
    $retour = true;
    foreach(explode(".", $ip) as $value){
        if(!inRange($value, -1, 256)){
            $retour = false;
        }
    }
    return $retour;
}
function adresseDecToBin($ip){
    $retour = "";
    foreach(explode(".", $ip) as $value){
        $retour .= str_pad(decbin($value), 8, "0", STR_PAD_LEFT) . ".";
    }
    return rtrim($retour, ".");
}
function formatMasque($mask){
    $retour = $mask;
    if(inRange($mask, -1, 33)){
        $retour = $GLOBALS["Netmask"][$mask];
    }
    return $retour;
}
function adresseToMasque($mask){
    foreach($GLOBALS["Netmask"] as $key => $value){
        if(strcmp($value, $mask) == 0){
            return $key;
        }
    }
    return "";
}
function getWildcard($mask){
    $part = explode(".", $mask);
    return (255-$part[0]) . "." . (255-$part[1]) . "." . (255-$part[2]) . "." . (255-$part[3]);
}
function getNetworkAdress($ip, $mask){
    $partIp = explode(".", adresseDecToBin($ip));
    $partMask = explode(".", adresseDecToBin($mask));
    $partNetwork = [
        0 => bindec($partIp[0] & $partMask[0]),
        1 => bindec($partIp[1] & $partMask[1]),
        2 => bindec($partIp[2] & $partMask[2]),
        3 => bindec($partIp[3] & $partMask[3]),
    ];
    return implode(".", $partNetwork);
}
function getBroadcastAdress($ip, $wild){
    $partIp = explode(".", adresseDecToBin($ip));
    $partWild = explode(".", adresseDecToBin($wild));
    $partBroadcast = [
        0 => bindec($partIp[0] | $partWild[0]),
        1 => bindec($partIp[1] | $partWild[1]),
        2 => bindec($partIp[2] | $partWild[2]),
        3 => bindec($partIp[3] | $partWild[3]),
    ];
    return implode(".", $partBroadcast);
}
function getIpBefore($ip){
    $partIp = explode(".", $ip);
    if($partIp[3] > 0){
        $partIp[3]--;
    }elseif($partIp[2] > 0){
        $partIp[2]--;
    }elseif($partIp[1] > 0){
        $partIp[1]--;
    }elseif($partIp[0] > 0){
        $partIp[0]--;
    }
    return implode(".", $partIp);
}
function getIpAfter($ip){
    $partIp = explode(".", $ip);
    if($partIp[3] < 255){
        $partIp[3]++;
    }elseif($partIp[2] < 255){
        $partIp[2]++;
    }elseif($partIp[1] < 255){
        $partIp[1]++;
    }elseif($partIp[0] < 255){
        $partIp[0]++;
    }
    return implode(".", $partIp);
}
function getHostByNet($mask){
    return floor((pow(2, 32) - pow(2, $mask) - 2) / (pow(2, $mask)));
}
function formatBinaire($ip, $mask){
    $partIp = explode(".", $ip);
    $index = 32 - $mask;
    if($index < 8){
        $partIp[3] = substr($partIp[3], 0, 8-$index%8) . " " . substr($partIp[3], 8-$index%8);
    }elseif($index < 16){
        $partIp[2] = substr($partIp[2], 0, 8-$index%8) . " " . substr($partIp[2], 8-$index%8);
    }elseif($index < 24){
        $partIp[1] = substr($partIp[1], 0, 8-$index%8) . " " . substr($partIp[1], 8-$index%8);
    }elseif($index < 32){
        $partIp[0] = substr($partIp[0], 0, 8-$index%8) . " " . substr($partIp[0], 8-$index%8);
    }
    return implode(".", $partIp);
}
?>