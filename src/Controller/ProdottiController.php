<?php
namespace Src\Controller;

use Src\TableGateways\ProdottiGateway;

// use Src\Controller\AnagraficaController;

class ProdottiController {

    private $db;
    private $requestMethod;
    private $userId;

    private $ProdottiGateway;


    public function __construct($db, $requestMethod, $userId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->userId = $userId;

        $this->ProdottiGateway = new ProdottiGateway($db);


    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if(!$this->ProdottiGateway->checkTable()){
                    exit(json_encode("Tabella creata"));
                }
                if ($this->userId) {
                    $response = $this->getUser($this->userId);
                } else {
                    $response = $this->getAllUsers();
                };
                break;
            case 'POST':
                $response = $this->createUserFromRequest();
                break;
            case 'PUT':
                $response = $this->updateUserFromRequest($this->userId);
                break;
            case 'DELETE':
                $response = $this->deleteUser($this->userId);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function isPossible(){

    }

    private function getAllUsers()
    {
        $result = $this->ProdottiGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getUser($id)
    {
        $result = $this->ProdottiGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createUserFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        // $verifica = $this->validatePerson($input);
        // if ($verifica) {
        //     return $this->unprocessableEntityResponse($verifica);
        // }
        $this->ProdottiGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = true;
        return $response;
    }

    private function updateUserFromRequest($id)
    {
        $result = $this->ProdottiGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        // $verifica = $this->validatePerson($input);
        // if (is_array($verifica)) {
        //     return $this->unprocessableEntityResponse($verifica);
        // }
        $this->ProdottiGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = true;
        return $response;
    }

    private function deleteUser($id)
    {
        $result = $this->ProdottiGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->ProdottiGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = true;
        return $response;
    }

    private function validatePerson($input)
    {
        return True;
        $error = [];
        if (! isset($input['tipo'])) {
            $error[] = 'tipo';
        }
        if (! isset($input['id_Anagrafica'])) {
            $error[] = 'id_Anagrafica';
        }

        if (!$this->controlloAnagrafica($input["id_Anagrafica"])) {
            $error[] = 'Nessuna anagrafica collegata';
            return $error;
        }

        if (!$this->controlloEntrate_Uscite($input)) {
            $error[] = 'Troppi giorni';
            return $error;
        }

        if (count($error) >> 0){
            return $error;
        }else{
            return true;
        }
    }

    private function unprocessableEntityResponse($error)
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => $error
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }

    // private function controlloAnagrafica($idAnagrafica){
    //     // $result = $this->AnagraficaGateway->find($idAnagrafica);
    //     $controller = new AnagraficaController($this->db, "GET", $idAnagrafica);
        
    //     $result = $controller->apiRequest();

    //     if(! is_null($result)){
    //         return true;
    //     }
    //     return false;
    // }

    // private function controlloEntrate_Uscite($input){
    //     $result = $this->uscite_entrateUltimoGiorno($input);
    //     return json_encode($result);
    // }

    // public function controlloUltimoEvento($userId){
    //     $result = $this->ProdottiGateway->getUltimaAzione($userId);
    //     exit(json_encode($result));
    //     return json_encode($result);
    // }

    // private function uscite_entrateUltimoGiorno($dati){
    //     $result = $this->ProdottiGateway->AzioniGiornaliere($dati);
    //     return $result;
    // }

    // public function calcoli(){
    //     $input = (array) json_decode(file_get_contents('php://input'), TRUE);
    //     $result = $this->ProdottiGateway->datiCalcoli($input);
    //     $obj = [];
    //     $obj["in"] = "";
    //     $obj["out"] = "";

    //     $totali = [];
        
    //     foreach($result as $singolo){
    //         if ($singolo["tipo"] == 1){
    //             $obj["in"] = $singolo["data_Azione"];
    //         }elseif($singolo["tipo"] == 0){
    //             $obj["out"] = $singolo["data_Azione"];
    //         }
            
    //         if(!empty($obj["in"]) && !empty($obj["out"])){
    //             $data = explode(" ", $obj["in"])[0];
    //             if(empty($totali[$data])){
    //                 $totali[$data] = ["mattina" => [], "pomeriggio" => []];
    //             }

                
    //             if (empty($totali[$data]["mattina"])){
    //                 array_push($totali[$data]["mattina"], date("H:m:s", strtotime($obj["out"]) - strtotime($obj["in"]))); 
    //             }else{
    //                 array_push($totali[$data]["pomeriggio"], date("H:m:s", strtotime($obj["out"]) - strtotime($obj["in"]))); 
    //             }
                
    //             $obj["in"] = "";
    //             $obj["out"] = "";

    //             if (!empty($totali[$data]["mattina"]) && !empty($totali[$data]["pomeriggio"])){
    //                 $totali[$data] = date("H:m:s", strtotime($totali[$data]["mattina"][0]) + strtotime($totali[$data]["pomeriggio"][0]));
    //             }
    //         }
            
    //     }


    //     exit(json_encode($totali));
    //     return $totali;
    // }
}