<?php
include("../SQL/mysql.php");
$con = getCon();
/*$res = select("SELECT * FROM License WHERE etat = 1", $con);
if(mysqli_num_rows($res) != 0){
    $row = mysqli_fetch_array($res);*/

update("License", "etat", "0", "id = " . $_GET["id"], $con);
commit($con);

/*}*/
finish($con);
?>