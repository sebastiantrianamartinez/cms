<?php

    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/core/routing/routing.php';
    require_once ROOT .'/endpoints/services/activation-mail.php';

    routing::vendor();

    use Core\Database\Entities\Activation;
    use Core\Database\Entities\Users;

    $models = [
        "lib" => ["responser", "exception"],
        "mail" => "mail"
    ];
    
    Routing::model(null, $models);
    Routing::waf('auth/server');

    $responser = new Responser();
    $authServer = new AuthServer($entityManager);
    $mail = new Mail();

    $sid = 3;
    $entityManager = Routing::entityManager();
    
    try{
        if(is_bool($entityManager)){
            $responser->toHttpRequest(500, "Invalid entity manager", null);
            die();
        }
    
        require_once ROOT .'/endpoints/core.php'; // <-- @sid @entityManager <--

        if($_SERVER["REQUEST_METHOD"] == 'POST'){
            $code = $data["code"];
            $token = $data["token"];

            $activationRepository = $entityManager->getRepository(Activation::class);
            $activation = $activationRepository->findOneBy(['token' => $token]);

            if($activation->getCode() == $code){
                $activation->setTime(time());
                $entityManager->flush();

                $user = $entityManager->getRepository(Users::class)->findOneBy(['id' => $activation->getUserId()]);
                $user->setStatus(2);
                $entityManager->flush();
                $responser->toHttpRequest(200, "Success activation", null);
                die();
            }
            $responser->toHttpRequest(400, "Token is wrong", null);
            die();
        }
        if($_SERVER["REQUEST_METHOD"] == "GET"){
            $email = $data["mail"];
            $userRepository = $entityManager->getRepository(Users::class);
            $user = $userRepository->findOneBy([
                'mail' => $email
            ]);

            if(!$user){
                $responser->toHttpRequest(400, "Mail not found", null);
                die();
            }
            if($user->getStatus() == $config["status"]["active"]){
                $responser->toHttpRequest(400, "User has been actived", null);
                die();
            }

            $activationRepository = $entityManager->getRepository(Activation::class);
            $activation = $activationRepository->findOneBy([
                'userId' => $user->getId()
            ]);

            $token = $activation->getToken();
            $alias = $user->getAlias();
            $code = $activation->getCode();

            sendActivationMail($email, $alias, $token, $code);
            $responser->toHttpRequest(200, "Activation mail sent", null);
        }
    }
    catch(Exception $e){
        $responser->toHttpRequest($e->getCode(), $e->getMessage(), null);
        if($e->getCode === 401){
            $responser->toHttpRequest(401, $e->getMessage, null);
            //header('location: ' .Routing::config('project')['website'] .'/views/error/unauthorized.php');
        }
    }