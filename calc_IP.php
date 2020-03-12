<?php 
$GLOBALS["Netmask"] = array(
    0 => "0.0.0.0", 1 => "128.0.0.0", 2 => "192.0.0.0", 3 => "224.0.0.0", 4 => "240.0.0.0", 5 => "248.0.0.0", 6 => "252.0.0.0", 7 => "254.0.0.0",
    8 => "255.0.0.0", 9 => "255.128.0.0", 10 => "255.192.0.0", 11 => "255.224.0.0", 12 => "255.240.0.0", 13 => "255.248.0.0", 14 => "255.252.0.0", 15 => "255.254.0.0",
    16 => "255.255.0.0", 17 => "255.255.128.0", 18 => "255.255.192.0", 19 => "255.255.224.0", 20 => "255.255.240.0", 21 => "255.255.248.0", 22 => "255.255.252.0", 23 => "255.255.254.0",
    24 => "255.255.255.0", 25 => "255.255.255.128", 26 => "255.255.255.192", 27 => "255.255.255.224", 28 => "255.255.255.240", 29 => "255.255.255.248", 30 => "255.255.255.252", 31 => "255.255.255.254", 32 => "255.255.255.255"       
);

/** Vérifie qu'un nombre donnée est bien etre deux autres nombres (non inclus) */
function inRange($nombre, $min, $max){
    if($nombre > $min && $nombre < $max){
        return true;
    }
    return false;
}

/** Renvoit la classe d'une adresse ip */
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

/** Vérifie qu'une adresse ip decimal est valide */
function isAdresseIpValide($ip){
    $retour = true;
    //On parcours chaque octets
    foreach(explode(".", $ip) as $value){
        //Si un octet n'est pas entre 0 et 255 (compris)
        if(!inRange($value, -1, 256)){
            $retour = false;
        }
    }
    return $retour;
}

/** Transforme une adresse ip decimal en une adresse ip binaire */
function adresseDecToBin($ip){
    $retour = "";
    //On parcours chaque octets
    foreach(explode(".", $ip) as $value){
        //On transforme chaque octet en binaire et on force l'affichage a afficher 8 bits
        $retour .= str_pad(decbin($value), 8, "0", STR_PAD_LEFT) . ".";
    }
    //On supprime le dernier point
    return rtrim($retour, ".");
}

/** Formate le masque en un masque sur 4 octets */
function formatMasque($mask){
    $retour = $mask;
    //Si le masque est entre 0 et 32 (compris)
    if(inRange($mask, -1, 33)){
        //$GLOBALS contient la liste des Netmask
        $retour = $GLOBALS["Netmask"][$mask];
    }
    return $retour;
}

/** Renvoit le masque sur deux decimals en fonction du masque sur 4 octets */
function adresseToMasque($mask){
    foreach($GLOBALS["Netmask"] as $key => $value){
        //On cherhce si on connait l'adresse sur 4 octets
        if(strcmp($value, $mask) == 0){
            return $key;
        }
    }
    return "";
}

/** Retourne l'inverse du masque donnée */
function getWildcard($mask){
    $part = explode(".", $mask);
    //On inverse chaque octet du masque
    return (255-$part[0]) . "." . (255-$part[1]) . "." . (255-$part[2]) . "." . (255-$part[3]);
}

/** Récupère l'adresse de réseau d'une ip donnée grace a son masque */
function getNetworkAdress($ip, $mask){
    $partIp = explode(".", adresseDecToBin($ip));
    $partMask = explode(".", adresseDecToBin($mask));
    //On fait un ET logique entre les octets de l'ip et du masque
    $partNetwork = [
        0 => bindec($partIp[0] & $partMask[0]),
        1 => bindec($partIp[1] & $partMask[1]),
        2 => bindec($partIp[2] & $partMask[2]),
        3 => bindec($partIp[3] & $partMask[3]),
    ];
    //On renvoit avec des points entre chaque octet
    return implode(".", $partNetwork);
}

/** Récupère l'adresse de diffusion d'une ip donnée grace a l'inverse du masque */
function getBroadcastAdress($ip, $wild){
    $partIp = explode(".", adresseDecToBin($ip));
    $partWild = explode(".", adresseDecToBin($wild));
    //On fait un OU logique entre les octets de l'ip et de l'inverse du masque
    $partBroadcast = [
        0 => bindec($partIp[0] | $partWild[0]),
        1 => bindec($partIp[1] | $partWild[1]),
        2 => bindec($partIp[2] | $partWild[2]),
        3 => bindec($partIp[3] | $partWild[3]),
    ];
    //On renvoit avec des points entre chaque octet
    return implode(".", $partBroadcast);
}

/** Récupère l'adresse ip juste avant celle d'entrée */
function getIpBefore($ip){
    $partIp = explode(".", $ip);
    //On fait bien attention a ne pas passer un octet a -1
    if($partIp[3] > 0){
        $partIp[3]--;
    }elseif($partIp[2] > 0){
        $partIp[3] = 255;
        $partIp[2]--;
    }elseif($partIp[1] > 0){
        $partIp[3] = 255;
        $partIp[2] = 255;
        $partIp[1]--;
    }elseif($partIp[0] > 0){
        $partIp[3] = 255;
        $partIp[2] = 255;
        $partIp[1] = 255;
        $partIp[0]--;
    }
    return implode(".", $partIp);
}

/** Récupère l'adresse ip juste après celle d'entrée */
function getIpAfter($ip){
    $partIp = explode(".", $ip);
    //On fait bien attention a ne pas passer un octet a 256
    if($partIp[3] < 255){
        $partIp[3]++;
    }elseif($partIp[2] < 255){
        $partIp[3] = 0;
        $partIp[2]++;
    }elseif($partIp[1] < 255){
        $partIp[3] = 0;
        $partIp[2] = 0;
        $partIp[1]++;
    }elseif($partIp[0] < 0){
        $partIp[3] = 0;
        $partIp[2] = 0;
        $partIp[1] = 0;
        $partIp[0]++;
    }
    return implode(".", $partIp);
}

/** Calcul le nombre d'host par net qu'on peut avoir en fonction du masque */
function getHostByNet($mask){
    return floor((pow(2, 32) - pow(2, $mask) - 2) / (pow(2, $mask)));
}

/** Formate une adresse ip en binaire suivant le masque en ajoutant un espace au bon endroit */
function formatBinaire($ip, $mask){
    $partIp = explode(".", $ip);
    $index = 32 - $mask;
    //On ajoute l'espace au bon endroit suivant l'index trouvé à l'aide du masque
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