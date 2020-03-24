<?php
    /* J'ai trouvé un moyen de set les cookies dans une fonction interne au body pour qu'il soit incrémenté seulement si on utilise le formulaire avec l'output buffering */
    //$nbUtil = isset($_COOKIE["nbUtil"]) ? $_COOKIE["nbUtil"]+1 : 1;
    //setcookie("nbUtil", strval($nbUtil));
?>
<!DOCTYPE html>
<head>
	<meta charset="UTF-8"/>
    <title>Calculateur d'IP</title>
    
    <link rel="stylesheet" href="format.css">
<?php
    include("calc_IP.php");
    include("mysql.php");

    $doitAfficherTableau = false;
    $ip = "";
    $maskIp = "";
    $mask;
    $wild;
    $network;
    $broadcast;
    $hostMin;
    $hostMax;
    $authUtil = true;

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        //on incrémente le cookie d'utilisation de la page
        /*ob_start();
        $nbUtil = isset($_COOKIE["nbUtil"]) ? $_COOKIE["nbUtil"]+1 : 1;
        setcookie("nbUtil", strval($nbUtil));
        ob_end_flush();*/
        $ip = $_POST["adresseIp"];
        $maskIp = $_POST["masque"];
        //pour avoir le masque sur 4 octets
        $maskIp = formatMasque($maskIp);
        //pour avoir le masque sur 2 decimals
        $mask = adresseToMasque($maskIp);
        //pour avoir l'inverse du masque
        $wild = getWildcard($maskIp);
        
        if(isAdresseIpValide($ip) && isAdresseIpValide($maskIp)){
            //on affcihe le tableau seulement si les adresses sont valides
            $doitAfficherTableau = true;
            $network = getNetworkAdress($ip, $maskIp);
            $broadcast = getBroadcastAdress($ip, $wild);
            //host min est l'adresse apres celle de réseau
            $hostMin = getIpAfter($network);
            //host max est l'adresse avant celle de diffusion
            $hostMax = getIpBefore($broadcast);
        }

        $con = getCon();
        $res = select("SELECT * FROM License WHERE etat = 0", $con);
        if(mysqli_num_rows($res) == 0){
            $authUtil = false;
        }else{
            $row = mysqli_fetch_array($res);
            update("License", "etat", "1", "id = " . $row["id"], $con);
            commit($con);
?>
<script>
            window.onbeforeunload = function () {
                document.createElement("img").src = "end.php";
            }
</script>
<?php
        }
        finish($con);
    }
?>    

</head>
<body>
<?php
    if($authUtil){
?>
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
                        <!-- Si on a une requete POST alors on récupère ce qui avait été envoyé pour le masque dans cette requete -->
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
            <!-- Tableau complet des résultats -->
            <!-- On affichera d'abord le nom de la ligne -->
            <!-- Puis l'adresse en decimal -->
            <!-- Et enfin l'adresse en binaire -->
            <table class="result">
            <tbody>
                <!-- 3 lignes correspondant aux valeurs d'entrées -->
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

                <!-- 4 lignes correspondant aux calculs fait sur les valeurs d'entrées -->
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
                //Si une des valeurs d'entrées n'est pas bonne on affiche un message
                echo "L'adresse $ip et/ou $maskIp ne sont pas des adresses valide";
            }
        }
    }else{
?>
    Pas de License disponible actuellement, veuillez réessayer ultérieurement...
<?php
    }
?>
</body>
</html>