<?php 
    include "global/config.php";
    include "global/conexion.php";
    include "carrito.php";
    include "templates/cabecera.php";
?>

<?php
if($_POST){
    $total=0;
    $sid=session_id();
    $correo=$_POST['email'];
    foreach ($_SESSION['carrito'] as $indice => $producto) {

        $total=$total+($producto['precio']*$producto['cantidad']);

    }
        $sentencia=$pdo->prepare("INSERT INTO `tblventas` 
                                (`id`, `clavetransaccion`, `paypaldatos`, `fecha`, `correo`, `total`, `status`) 
        VALUES (NULL,:clavetransaccion, '', now(),:correo,:total, 'pendiente');");
        
        $sentencia->bindParam(":clavetransaccion",$sid);
        $sentencia->bindParam(":correo",$correo);
        $sentencia->bindParam(":total",$total);
        $sentencia->execute();
        $idventa=$pdo->lastInsertId();

        foreach ($_SESSION['carrito'] as $indice => $producto) {

            $sentencia=$pdo->prepare("INSERT INTO 
            `tbldetalleventa` (`id`, `idventa`, `idproducto`, `preciounitario`, `cantidad`, `descargado`)
             VALUES (NULL,:idventa,:idproducto,:preciounitario,:cantidad,'0');");
             $sentencia->bindParam(":idventa",$idventa);
             $sentencia->bindParam(":idproducto",$producto['id']);
             $sentencia->bindParam(":preciounitario",$producto['precio']);
             $sentencia->bindParam(":cantidad",$producto['cantidad']);
             $sentencia->execute();

        }
    echo "<h3>".$total."</h3>";

}

?>
<!-- Include the PayPal JavaScript SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=sb&currency=USD"></script>

<div class="jumbotron text-center">
    <h1 class="display-4">¡Paso Final</h1>
    <hr class="my-4">
    <p class="lead">Estas apunto a pagar con paypal la cantidad de:
        <h4>$<?php echo number_format($total,2); ?></h4>
        <!-- Set up a container element for the button -->
        <div id="paypal-button-container"></div>

    </p>
        <p>Los productos podran ser descargados una vez que se procese el pago <br/>
         <strong>(Para aclaraciones :jonathanf4.32@hotmail com)</strong>   
        </p>

</div>
<!DOCTYPE html>

<head>
    <!-- Add meta tags for mobile and IE -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
</head>

<body>
    <script>
        // Render the PayPal button into #paypal-button-container
        paypal.Buttons({

            // Set up the transaction
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo $total; ?>',
                            description:"Compras de producto a Artesanos Web:$<?php echo number_format($total,2);?>",
                            custom:"<?php echo $sid;?>#<?php echo openssl_encrypt($idventa,COD,KEY);?> "
                        }
                    }]
                });
            },
            client: {
            sandbox:    'AUr644M4T4dtZsonKMjVkuib4UIeudmJAbD6N_PLO_9OCsF5DHLpUlf2jllLMa_G1tUJcybOErrEfWuT',
            production: 'Ac1x_f9Ffzjhs0hZGa6a11QaikJedJKuFEMtOvshiB8U-OSxzjwk5evUXVIGxwhuamDx_jGCRKte2wNh'
         
        },
            // Finalize the transaction
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Show a success message to the buyer
                    console.log(data);
                    window.location="verificador.php?paymentTokent="+data.paymentToken
                });
            }


        }).render('#paypal-button-container');
    </script>
</body>
    

<?php include 'templates/pie.php'; ?>