<?php
    echo 'Error de autorización: ' .$_GET["reason"];
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized</title>
</head>
<body>
    <main>
        <h1>Unauthorized</h1>
        <h2>¡STOP!</h2>
        <p>For security reasons you were suspended from the system, please do not insist or you will 
            be permanently blocked.</p>
        <p>Blocking in force until: <?php echo $_GET["until"]?></p>
    </main>
</body>
</html>