<?php
class Conection{
    public function conectar()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";

        try{
            $conn = new PDO("mysql:host=$servername;dbname=bearny", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Conexão foi realizada com Sucesso!";
            return $conn;
        } catch(PDOException $e){
            echo "Conexão Falhou!".$e->getMessage();
            return null;
        }
        
    }
}
?>