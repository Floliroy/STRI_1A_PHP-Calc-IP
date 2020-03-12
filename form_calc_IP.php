<!DOCTYPE html>
<head>
	<meta charset="UTF-8"/>
    <title>Calculateur d'IP</title>
    
    <link rel="stylesheet" href="format.css">
<?php
    include("calc_IP.php");

    $doitAfficherTableau = false;
    $ip = "";
    $maskIp = "";
    $mask;
    $wild;
    $network;
    $broadcast;
    $hostMin;
    $hostMax;

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $ip = $_POST["adresseIp"];
        $maskIp = $_POST["masque"];
        $maskIp = formatMasque($maskIp);
        $mask = adresseToMasque($maskIp);
        $wild = getWildcard($maskIp);
        
        if(isAdresseIpValide($ip) && isAdresseIpValide($maskIp)){
            $doitAfficherTableau = true;
            $network = getNetworkAdress($ip, $maskIp);
            $broadcast = getBroadcastAdress($ip, $wild);
            $hostMin = getIpAfter($network);
            $hostMax = getIpBefore($broadcast);
        }
    }
?>    

</head>
<body>
    <h1>Calculateur d'IP</h1>
    <form method="post" action="form_calc_IP.php">
        <table>
        <tbody>
            <tr>
                <td class="form"><b>Adresse IP</b></td>
                <td class="form"><b>Masque</b></td>
            </tr>
            <tr>
                <td class="form"><input type="text" name="adresseIp" value="<?=$ip ?>"/> /</td>
                <td class="form">
                    <input type="text" name="masque" value="<?=$_SERVER["REQUEST_METHOD"] == "POST" ? $_POST["masque"] : "" ?>"/>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="form"><input type="submit" value="Calculer"/></td>
            </tr>
        </tbody>
        </table>
    </form>
    <br/><br/>
    
<?php
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if($doitAfficherTableau){
?>
        <table class="result">
        <tbody>
            <tr>
                <td>Adresse :</td>
                <td class="ip"><?=$ip ?></td>
                <td colspan="2"><?=formatBinaire(adresseDecToBin($ip), $mask) ?></td>
            </tr>
            <tr>
                <td>Masque :</td>
                <td class="ip"><?="$maskIp => $mask" ?></td>
                <td colspan="2" class="mask"><?=formatBinaire(adresseDecToBin($maskIp), $mask) ?></td>
            </tr>
            <tr>
                <td>Inverse :</td>
                <td class="ip"><?=$wild ?></td>
                <td colspan="2"><?=formatBinaire(adresseDecToBin($wild), $mask) ?></td>
            </tr>
            
            <tr>
                <td colspan="3">=></td>
            </tr>

            <tr>
                <td>Réseau :</td>
                <td class="ip"><?="$network / $mask" ?></td>
                <td><?=formatBinaire(adresseDecToBin($network), $mask) ?></td>
                <td class="classe">(Classe <?=getClasseAdresseIp($network) ?>)</td>
            </tr>
            <tr>
                <td>Diffusion :</td>
                <td class="ip"><?=$broadcast ?></td>
                <td colspan="2"><?=formatBinaire(adresseDecToBin($broadcast), $mask) ?></td>
            </tr>
            <tr>
                <td>HostMin :</td>
                <td class="ip"><?=$hostMin ?></td>
                <td colspan="2"><?=formatBinaire(adresseDecToBin($hostMin), $mask) ?></td>
            </tr>
            <tr>
                <td>HostMax :</td>
                <td class="ip"><?=$hostMax ?></td>
                <td colspan="2"><?=formatBinaire(adresseDecToBin($hostMax), $mask) ?></td>
            </tr>
            <tr>
                <td>Host/Net :</td>
                <td colspan="3" class="ip"><?=getHostByNet($mask) ?></td>
            </tr>
        </tbody>
        </table>
<?php
        }else{
            echo "L'adresse $ip et/ou $maskIp ne sont pas des adresses valide";
        }
    }
?>

</body>
</html>