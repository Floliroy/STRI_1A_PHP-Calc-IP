<!DOCTYPE html>
<head>
	<meta charset="UTF-8"/>
    <title>Calculateur d'IP</title>
    
    <link rel="stylesheet" href="format.css">
<?php
    $ip = "";
    $maskIp = "";
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $ip = $_POST["adresseIp"];
        $maskIp = $_POST["masque"];
    }
?>    

</head>
<body>
    <h1>Calculateur d'IP</h1>
    <form method="post" action="form_calc_IP.php">
        <table>
            <tr>
                <td class="form"><b>Adresse IP</b></td>
                <td class="form"><b>Masque</b></td>
            </tr>
            <tr>
<?php
            echo "<td class=\"form\"><input type=\"text\" name=\"adresseIp\" value=\"$ip\"/> /</td>";
            echo "<td class=\"form\"><input type=\"text\" name=\"masque\" value=\"$maskIp\"/></td>";
?>
            </tr>
            <tr>
                <td colspan="2" class="form"><input type="submit" value="Calculer"/></td>
            </tr>
        </table>
    </form>
    <br/><br/>
    
<?php
    include("calc_IP.php");

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $maskIp = formatMasque($maskIp);
        $mask = adresseToMasque($maskIp);
        $wild = getWildcard($maskIp);

        if(isAdresseIpValide($ip) && isAdresseIpValide($maskIp)){
            echo "<table class=\"result\">";

            echo "<tr><td>Adresse :</td><td class=\"ip\">$ip</td><td colspan=\"2\">" . formatBinaire(adresseDecToBin($ip), $mask) . "</td></tr>";
            echo "<tr><td>Masque :</td><td class=\"ip\">$maskIp => $mask</td><td colspan=\"2\" class=\"mask\">" . formatBinaire(adresseDecToBin($maskIp), $mask) . "</td></tr>";
            echo "<tr><td>Inverse :</td><td class=\"ip\">$wild</td><td colspan=\"2\">" . formatBinaire(adresseDecToBin($wild), $mask) . "</td></tr>";
            
            echo "<tr><td colspan=\"3\">=></td></tr>";

            $network = getNetworkAdress($ip, $maskIp);
            $broadcast = getBroadcastAdress($ip, $wild);
            $hostMin = getIpAfter($network);
            $hostMax = getIpBefore($broadcast);
            echo "<tr><td>Réseau :</td><td class=\"ip\">$network / $mask</td><td>" . formatBinaire(adresseDecToBin($network), $mask) . "</td><td class=\"classe\">(Classe " . getClasseAdresseIp($network) . ")</td></tr>";
            echo "<tr><td>Diffusion :</td><td class=\"ip\">$broadcast</td><td colspan=\"2\">" . formatBinaire(adresseDecToBin($broadcast), $mask) . "</td></tr>";
            echo "<tr><td>HostMin :</td><td class=\"ip\">$hostMin</td><td colspan=\"2\">" . formatBinaire(adresseDecToBin($hostMin), $mask) . "</td></tr>";
            echo "<tr><td>HostMax :</td><td class=\"ip\">$hostMax</td><td colspan=\"2\">" . formatBinaire(adresseDecToBin($hostMax), $mask) . "</td></tr>";
            echo "<tr><td>Host/Net :</td><td colspan=\"3\" class=\"ip\">" . getHostByNet($mask) . "</td></tr>";

            echo "</table>";
        }else{
            echo "L'adresse $ip et/ou $maskIp ne sont pas des adresses valide";
        }
    }
?>

</body>
</html>