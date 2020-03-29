<?php
namespace Src\TableGateways;

class TipoquantitaGateway {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "
            SELECT 
                id, tipo, datetime
            FROM
                tipoquantita;
        ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {
        $statement = "
            SELECT 
                id, 
                tipo,
                datetime
            FROM
                tipoquantita
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function insert(Array $input)
    {
        $statement = "
            INSERT INTO tipoquantita 
                (tipo)
            VALUES
                (:tipo);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'tipo' => $input['tipo']
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit(json_encode($e->getMessage()));
        }    
    }

    public function update($id, Array $input)
    {
        $statement = "
            UPDATE tipoquantita
            SET 
                tipo = :tipo
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'tipo' => $input['tipo']
            ));
            return $statement;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM tipoquantita
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function checkTable(){
        $statement = "
            SELECT 
                id 
            FROM 
                tipoquantita;
        ";

        $statement = $this->db->prepare($statement);
        $statement->execute();
        $result = $statement->rowCount();

        if(empty($result)) {
            $statement = "
                CREATE TABLE tipoquantita (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    tipo VARCHAR(30) NOT NULL,
                    datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                );
            ";
            try {
                $statement = $this->db->prepare($statement);
                $statement->execute();
                return false;
            } catch (PDOException $e) {
                exit($e->getMessage());
            } 
        }
        
        return true;
    }
}


// CREATE TABLE orari (
//     id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     tipo BOOLEAN NOT NULL,
//     id_tipoquantita INT(6) NOT NULL,
//     data_Azione TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// );

// CREATE TABLE tipoquantita (
//     id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     nome VARCHAR(30) NOT NULL,
//     cognome VARCHAR(30) NOT NULL
// );
