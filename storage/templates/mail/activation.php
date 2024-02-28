<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activa tu cuenta</title>
    <style>
        body, .parent-container {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex-direction: column;
        }
        h1 {
            color: #333333;
            margin: auto;
            text-align: center;
            font-size: 20px;
        }
        p {
            color: #666666;
            line-height: 1.5;
        }
        .button-container{
            width: 100%;
            text-align: center;
        }
        .button {
            background-color:  #007bff;
            color: #fff !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            width: 120px !important;
            text-align: center;
        }
        h2{
            color:  #007bff;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="parent-container">
        <div class="container">
            <h1>Bienvenido a nuestra plataforma</h1>
            <p>¡Hola {{name}}! Hemos recibido tu solicitud de registro a Trianametria System, 
            para activar tu cuenta, por favor haz clic en el siguiente enlace:</p>
            <div class="button-container">
                <a href="{{link}}" class="button">Activar cuenta</a>
            </div>
            <p>También puedes usar el siguiente código de activación: </p>
            <h2>{{code}}</h2>
            <p>Si no solicitaste esta activación, puedes ignorar este mensaje.</p>
        </div>
    </div>
</body>
</html>
