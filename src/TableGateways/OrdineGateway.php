<?php
namespace Src\TableGateways;

class OrdineGateway {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "
            SELECT 
                *
            FROM
                ordine;
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
                quantita,
                cliente,
                id_alimento,
                stato
            FROM
                ordine
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

    public function findAlimento($id_alimento)
    {
        $statement = "
            SELECT 
                *
            FROM
                ordine
            WHERE id_alimento = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id_alimento));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function insert(Array $input)
    {
        $statement = "
            INSERT INTO ordine 
                (
                    quantita,
                    cliente,
                    id_alimento,
                    stato,
                    id_tipo
                )
            VALUES
                (:quantita,:cliente,:id_alimento,:stato, :id_tipo);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'quantita' => $input['quantita'],
                'cliente' => $input['cliente'],
                'id_alimento' => $input['id_alimento'],
                'stato' => $input['stato'],
                'id_tipo' => $input['id_tipo']
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit(json_encode($e->getMessage()));
        }    
    }

    public function update($id, Array $input)
    {
        if(!isset($input['quantita']) && !isset($input['cliente']) && !isset($input['id_alimento'])){
            $statement = "
                UPDATE ordine
                SET 
                    stato = :stato
                WHERE id = :id;
            ";
            try {
                $statement = $this->db->prepare($statement);
                $statement->execute(array(
                    'id' => (int) $id,
                    'stato' => $input['stato']
                ));
            } catch (\PDOException $e) {
                exit($e->getMessage());
            }
        }elseif(!isset($input['id_alimento'])){
            $statement = "
            UPDATE ordine
            SET 
                quantita = :quantita,
                cliente = :cliente
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'quantita' => $input['quantita'],
                'cliente' => $input['cliente']
            ));
            return $statement;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
        }else{
            $statement = "
                UPDATE ordine
                SET 
                    quantita = :quantita,
                    cliente = :cliente,
                    id_alimento = :id_alimento,
                    stato = :stato
                WHERE id = :id;
            ";
    
            try {
                $statement = $this->db->prepare($statement);
                $statement->execute(array(
                    'id' => (int) $id,
                    'quantita' => $input['quantita'],
                    'cliente' => $input['cliente'],
                    'id_alimento' => $input['id_alimento'],
                    'stato' => $input['stato']
                ));
                return $statement;
            } catch (\PDOException $e) {
                exit($e->getMessage());
            }
        }
    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM ordine
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
                ordine;
        ";

        $statement = $this->db->prepare($statement);
        $statement->execute();
        $result = $statement->rowCount();

        if(empty($result)) {
            $statement = "
                CREATE TABLE ordine (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    quantita VARCHAR(30) NOT NULL,
                    id_tipo BIGINT(8) NOT NULL,
                    cliente VARCHAR(30),
                    id_alimento BIGINT(8),
                    stato BIGINT(8),
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
//     id_ordine INT(6) NOT NULL,
//     data_Azione TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// );

// CREATE TABLE ordine (
//     id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     nome VARCHAR(30) NOT NULL,
//     cognome VARCHAR(30) NOT NULL
// );
