<?php


if(isset($_GET["action"]) && !empty($_GET["location"]) && !empty($_GET["heure"]) && !empty($_GET["arrivee"]) && !empty($_GET["date"]) && !empty($_GET["place"])){
    // echo "ok <br>";
    


// grabbing values----------------------------------------------------------
    $location = $_GET["location"];
    $heure = $_GET["heure"];
    $arrivee = $_GET["arrivee"];
    $date = $_GET["date"];
    $place = $_GET["place"];
    $road = $_GET["road"];
    if(empty($_GET["locationAdd"])){
        $etapes1=null;
    }else{
        $etapes1 = $_GET["locationAdd"];
    }
    if(empty($_GET["locationAdd2"])){
        $etapes2=null;
    }else{
        $etapes2 = $_GET["locationAdd2"];
    }

    $arr = array($etapes1,$etapes2);
    $lesEtapes = implode("&&",$arr);
    echo $lesEtapes;

    //instantiate class----------------------------------------------------
    include "../classes/dbh.class.php";
    include "../classes/create-route.class.php";
    include "../classes/create-routecontrl.class.php";
    $newroute = new CreateRouteContrl($location,$heure,$arrivee,$date,$place,$lesEtapes,$road,$etapes1,$etapes2);
    

    // error handlers and sign up------------------------------------------
    $newroute->newRoute();

    //back to page---------------------------------------------------------
    header("location: ../../confirmation.php?action=trajetCreation");
    echo $location."<br>";
    echo $heure."<br>";
    echo $arrivee."<br>";
    echo $date."<br>";
    echo $place."<br>";
    echo $etapes."<br>";
    echo $road."<br>";
}else{
    

    header("location: ../../confirmation.php?action=emptyInputs");

}