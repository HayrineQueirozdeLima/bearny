<?php
require_once("model/user.php");

class UserController {
    public function processa($acao){
        if($acao == "C"){
            $novoUsuario = new User($_POST['nome'],
                                    $_POST['email'],
                                    $_POST['senha'],
                                    $_POST['sexo'],
                                    $_POST['idade'],
                                    $_POST['altura']);
            $novoUsuario->cadastraUsuario();
        }
    }
}