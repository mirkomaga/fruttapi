<?php
namespace Src\TableGateways;

class ProdottiGateway {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "
            SELECT 
                id, tipo, prezzo_ingrosso, prezzo_vendita
            FROM
                prodotti;
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
                id, tipo, prezzo_ingrosso, prezzo_vendita
            FROM
                prodotti
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
            INSERT INTO prodotti 
                (tipo, prezzo_ingrosso, prezzo_vendita)
            VALUES
                (:tipo, :prezzo_ingrosso, :prezzo_vendita);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'tipo' => $input['tipo'],
                'prezzo_ingrosso'  => $input['prezzo_ingrosso'],
                'prezzo_vendita' => $input['prezzo_vendita']
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function update($id, Array $input)
    {
        $statement = "
            UPDATE prodotti
            SET 
                tipo= :tipo,
                prezzo_ingrosso= :prezzo_ingrosso,
                prezzo_vendita= :prezzo_vendita
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'tipo' => $input['tipo'],
                'prezzo_ingrosso'  => $input['prezzo_ingrosso'],
                'prezzo_vendita'  => $input['prezzo_vendita']
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM prodotti
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

    // public function AzioniGiornaliere($input)
    // {
    //     $statement = "
    //         SELECT 
    //             id,tipo,id_Anagrafica,data_Azione 
    //         FROM 
    //             prodotti 
    //         WHERE 
    //             data_Azione > CURDATE() AND data_Azione < CURDATE() + INTERVAL 1 DAY AND id_Anagrafica = :id AND tipo = :tipo;
    //     ";

    //     try {
    //         $statement = $this->db->prepare($statement);
    //         $statement->execute(array(
    //             'id' => $input['id_Anagrafica'],
    //             'tipo' => $input['tipo'],
    //         ));
    //         $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
    //         return $result;
    //     } catch (\PDOException $e) {
    //         exit($e->getMessage());
    //     }    
    // }

    // public function getUltimaAzione($input)
    // {
    //     $statement = "
    //         SELECT 
    //             * 
    //         FROM 
    //             orari 
    //         WHERE 
    //             id_Anagrafica = :id_Anagrafica ORDER BY data_Azione DESC LIMIT 1;
    //         ";

    //     try {
    //         $statement = $this->db->prepare($statement);
    //         $statement->execute(array(
    //             'id_Anagrafica' => $input
    //         ));
    //         $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
    //         return $result;
    //     } catch (\PDOException $e) {
    //         exit($e->getMessage());
    //     }
    // }

    // public function datiCalcoli($input)
    // {
    //     $statement = "
    //         SELECT
    //             * 
    //         FROM 
    //             orari 
    //         WHERE 
    //             data_Azione between :dataI and :dataF and id_Anagrafica = :id_Anagrafica;
    //         ";

    //     try {
    //         $statement = $this->db->prepare($statement);
    //         $statement->execute(array(
    //             'dataI' => $input["data_start"],
    //             'dataF' => $input["data_stop"],
    //             'id_Anagrafica' => $input["id_Anagrafica"]
    //         ));
    //         $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
    //         return $result;
    //     } catch (\PDOException $e) {
    //         exit($e->getMessage());
    //     }
    // }

    public function checkTable(){
        $statement = "
            SELECT 
                id 
            FROM 
                prodotti;
        ";

        $statement = $this->db->prepare($statement);
        $statement->execute();
        $result = $statement->rowCount();

        if(empty($result)) {
            $statement = "
                CREATE TABLE prodotti (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    tipo VARCHAR(160) NOT NULL,
                    prezzo_ingrosso VARCHAR(160),
                    prezzo_vendita VARCHAR(160)
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
//     id_Anagrafica INT(6) NOT NULL,
//     data_Azione TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// );

// CREATE TABLE anagrafica (
//     id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     nome VARCHAR(30) NOT NULL,
//     cognome VARCHAR(30) NOT NULL
// );

// SELECT id,tipo,id_Anagrafica,data_Azione FROM orari WHERE id = ?

// SELECT id,tipo,id_Anagrafica,data_Azione FROM orari WHERE data_Azione > CURDATE() AND data_Azione < CURDATE() + INTERVAL 1 DAY;

// UPDATE orari SET tipo= 1, id_Anagrafica= 456, WHERE id = 18;

// php -S 127.0.0.1:8000 -t public

// {
// 	"id_Anagrafica": "3",
// 	"data_start": "2020-01-10 00:00:00",
// 	"data_stop": "2020-01-20 00:00:00"
// }