<?php

function getCon(){
    //A changer par un utilisateur non admin pour plus de sécurité
    return mysqli_connect("127.0.0.1", "root", "root", "flobase");
}

function finish($con){
    mysqli_close($con);
}

function insert($table, $columns, $values, $con){
    $req = "INSERT INTO $table(";
    foreach($columns as $var){
        $req .= "$var,";
    }
    $req = rtrim($req, ",") . ") VALUES (";
    foreach($values as $var){
        $req .= "$var,";
    }
    $req = rtrim($req, ",") . ")";
    mysqli_query($con, $req); 
}

function update($table, $column, $value, $condition, $con){
    $req = "UPDATE $table SET $column = $value ";
    if($condition != null){
        $req.= "WHERE $condition ";
    }
    mysqli_query($con, $req); 
}

function select($req, $con){
    return mysqli_query($con, $req);
}

function commit($con){
    mysqli_commit($con);
}

?>