<?php
namespace Src\Controller;

use Src\TableGateways\TipoQuantitaGateway;

// use Src\Controller\AnagraficaController;

class TipoQuantitaController {

    private $db;
    private $requestMethod;
    private $userId;

    private $TipoQuantitaGateway;


    public function __construct($db, $requestMethod, $userId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->userId = $userId;

        $this->TipoQuantitaGateway = new TipoQuantitaGateway($db);


    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if(!$this->TipoQuantitaGateway->checkTable()){
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
        $result = $this->TipoQuantitaGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getUser($id)
    {
        $result = $this->TipoQuantitaGateway->find($id);
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
        $this->TipoQuantitaGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = true;
        return $response;
    }

    private function updateUserFromRequest($id)
    {
        $result = $this->TipoQuantitaGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        // $verifica = $this->validatePerson($input);
        // if (is_array($verifica)) {
        //     return $this->unprocessableEntityResponse($verifica);
        // }
        $this->TipoQuantitaGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = true;
        return $response;
    }

    private function deleteUser($id)
    {
        $result = $this->TipoQuantitaGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->TipoQuantitaGateway->delete($id);
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
}