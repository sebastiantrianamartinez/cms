<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/core/routing/routing.php';
    require_once ROOT .'/endpoints/services/activation-mail.php';

    $models = [
        "lib" => ["responser", "exception"]
    ];
    
    Routing::model(null, $models);
    Routing::waf('auth/server');
    Routing::vendor();

    $config = Routing::config("auth");
   

    $responser = new Responser();
    $authServer = new AuthServer($entityManager);
    use Core\Database\Entities\Users;
    use Core\Database\Entities\Activation;

    $sid = 2;
    $entityManager = Routing::entityManager();
   
    try{
        if(is_bool($entityManager)){
            $responser->toHttpRequest(500, "Invalid entity manager", null);
            die();
        }
    
        require_once ROOT .'/endpoints/core.php'; // <-- @sid @entityManager <--

    
        if($_SERVER["REQUEST_METHOD"] == 'POST'){

            $username = $data['username'] ?? '';
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            $alias = $data['alias'] ?? '';
            
            if(isset($data['master_key']) && password_verify($data['master_key'], Routing::key('master'))){
                //¡ 00083E00DD3268955A53279C10F7F17A
                $group = (isset($data['group'])) ? $data['group'] : $config['def_group'];
            }
            else{
                $group = $config['def_group'];
            }

            // Validar que los datos necesarios están presentes
            if (empty($username) || empty($email) || empty($password) || empty($group)) {
                $responser->toHttpRequest(400, "Faltan datos requeridos", null);
                exit;
            }
        
            // Crear una nueva instancia de la entidad Users
            $newUser = new Users();
        
            $status = ($config["verification"]) ? $config["status"]["inactive"] : $config["status"]["active"];

            // Establecer los valores del nuevo usuario
            $newUser->setName($username);
            $newUser->setMail($email);
            $newUser->setPassword($password); // La contraseña se encriptará automáticamente en el método setPassword
            $newUser->setGroup($group);
            $newUser->setAlias($alias);
            $newUser->setStatus($status);
        
            // Guardar el nuevo usuario en la base de datos
            $entityManager->persist($newUser);
            $entityManager->flush();

            if(!$config["verification"]){
                $responser->toHttpRequest(200, "Usuario creado exitosamente ", null);
                die();
            }

            $activation = new Activation();

            $token = uniqid(rand(), true);
            $code = '';
            for ($i = 0; $i < 6; $i++) {
                if($i == 0){
                    $code .= rand(1, 9);
                }
                else{
                    $code .= rand(0, 9);
                }
            }
            
            sendActivationMail($email, $alias, $token, $code);

            $activation->setUserId($newUser->getId()); 
            $activation->setToken($token);
            $activation->setCode($code);
            $entityManager->persist($activation);
            $entityManager->flush();
            $responser->toHttpRequest(200, "Usuario creado exitosamente", $token);
        } 
    }
    catch(Exception $e){
        $responser->toHttpRequest($e->getCode(), $e->getMessage(), null);
        if($e->getCode === 401){
            header('location: ' .Routing::config('project')['website'] .'/views/error/unauthorized.php');
        }
        $responser->toHttpRequest(401, $e->getMessage, null);
    }