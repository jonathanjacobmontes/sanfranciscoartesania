<?php

print_r($_GET);

$clienteid="AUr644M4T4dtZsonKMjVkuib4UIeudmJAbD6N_PLO_9OCsF5DHLpUlf2jllLMa_G1tUJcybOErrEfWuT";
$secret="EN6kcr81-FEnWnCsZYwzHss5sKL-oYFrh9BbsnZIjmmqswCS_urWLSEPCfkAoo0lu67n4iyfoO3oV7jF";

    $login=curl_init("https://api.sandbox.paypal.com/v1/oauth2/token");

    curl_setopt($login,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($login,CURLOPT_USERPWD,$clienteid.":".$secret);
    curl_setopt($login,CURLOPT_POSTFIELDS,"grant_type=client_credentials");
    $respuesta=curl_exec($login);

    print_r($respuesta);

    $objRespuesta=json_decode($respuesta);

    $accessToken=$objRespuesta->access_token;

    print_r($accessToken);

 $venta= curl_init("https://api.sandbox.paypal.com/v1/payment-experience/web-profiles/".$_GET['paymentid']);

 curl_setopt($venta,CURLOPT_HTTPHEADER,array("Content-Type: application/json","Authorization: Bearer".$accessToken));

 $respuestaventa=curl_exec($venta);

 print_r($respuestaventa);


?>