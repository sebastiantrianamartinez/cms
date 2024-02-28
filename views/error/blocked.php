<?php

    if(!isset($_GET["until"])){
        header('location: https://');
    }
    $outTime = "";
    $permanent = false;
    if($_GET["until"] == "NULL") {
        $outTime = "User blocked permanently";
        $permanent = true;
    } 
    else {
        $dateFormatted = json_decode($_GET["until"], true);

        // Verificar si la decodificación fue exitosa
        if ($dateFormatted !== null && array_key_exists('date', $dateFormatted)) {
            $datetime = date_create_from_format('Y-m-d H:i:s.u', $dateFormatted['date']);

            // Si la decodificación fue exitosa, procedemos con el formateo de la fecha y hora
            if ($datetime !== false) {
                date_default_timezone_set('America/Bogota');

                $day = $datetime->format('d');
                $month = $datetime->format('m');
                $year = $datetime->format('Y');
                $hour = $datetime->format('H');
                $minute = $datetime->format('i');

                $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

                // Verificar si el mes está dentro del rango
                if ($month >= 1 && $month <= 12) {
                    $outTime = $day .' of ' .$months[$month - 1] .' of ' .$year .' at ' .$hour .':' .$minute;
                } else {
                    $outTime = "Invalid month";
                }
            } else {
                $outTime = "Invalid date format";
            }
        } else {
            $outTime = "Invalid JSON format";
        }
    }
    
    $message = ($permanent) ? "you will be added to security blacklist." : "you will be permanently blocked.";
?>


<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bloqueado</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
  }
  .container {
    text-align: center;
  }
  .hexagono {
    position: relative;
    width: 150px;
    height: 150px;
    background-color: #ff4d4d;
    margin: 0 auto 20px;
    clip-path: polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%);
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
    font-size: 30px;
    font-weight: 900;
  }
  .mensaje {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }
  button{
    padding: 10px 20px;
    background-color: #ff4d4d;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 18px;
  }
  .no-action-button{
    background-color: #4d82bc;
  }
</style>
</head>
<body>

<div class="container">
  <div class="hexagono">
    STOP
  </div>
  <div class="mensaje">
    <h1>Blocked</h1>
    <h2>¡STOP!</h2>
    <p>For security reasons you were suspended from the system, please do <b>not insist</b> or <b><?php echo $message?></b>.</p>
    <p>Blocking in force until:<b> <?php echo $outTime?></b></p>

    <p>If you believe that we made a mistake or that your account was blocked by third parties, you can open a request to review your case.</p>
    <a href=""><button>Send Request</button></a>  
    <a href=""><button class="no-action-button">Site guidelines</button></a>  
</div>
</div>

</body>
</html>
