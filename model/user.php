<?php
    require_once("model/conection.php");
    class User{
        private $id;
        private $nome;
        private $email;
        private $senha;
        private $sexo;
        private $idade;
        private $altura;

        public function __construct($nome, $email, $senha, $sexo, $idade, $altura){
            $this->nome = $nome;
            $this->email = $email;
            $this->senha = $senha;
            $this->sexo = $sexo;
            $this->idade = $idade;
            $this->altura = $altura;
        }

        public function cadastraUsuario(){
            try{
                $con = Conection::conectar();
                $sql = $con->prepare("INSERT 
                                        INTO bearny.users
                                           ( nome,
                                             email,
                                             senha,
                                             sexo,
                                             idade,
                                             altura
                                             ) 
                                      VALUES 
                                           ( :nome,
                                             :email,
                                             :senha,
                                             :sexo,
                                             :idade,
                                             altura
                                             )");
                $sql ->bindParam("nome",$this->nome);
                $sql ->bindParam("email",$this->email);
                $sql ->bindParam("senha",$this->senha);
                $sql ->bindParam("sexo",$this->sexo);
                $sql ->bindParam("idade",$this->idade);
                $sql ->bindParam("altura",$this->altura);

                $sql->execute();
            } catch(PDOException $e){
                echo "conexão falhou".$e->getMessage();
            }
        }

        public function getId(){
            return $this->id;
        }
        public function setId($id){
            $this->id = $id;
        }

        public function getNome(){
            return $this->nome;
        }
        public function setNome($nome){
            $this->nome = $nome;
        }

        public function getEmail(){
            return $this->email;
        }
        public function setEmail($email){
            $this->email = $email;
        }

        public function getPassword(){
            return $this->senha;
        }
        public function setPassword($senha){
            $this->senha = $senha;
        }

        public function getSexo(){
            return $this->sexo;
        }
        public function setSexo($sexo){
            $this->sexo = $sexo;
        }

        public function getIdade(){
            return $this->idade;
        }
        public function setIdade($idade){
            $this->idade = $idade;
        }

        public function getAltura(){
            return $this->altura;
        }
        public function setAltura($altura){
            $this->altura = $altura;
        }
    }
?>