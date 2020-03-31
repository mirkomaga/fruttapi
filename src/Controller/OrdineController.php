<?php
namespace Src\Controller;

use Src\TableGateways\OrdineGateway;

class OrdineController {
    private $db;
    private $requestMethod;
    private $userId;

    private $OrdineGateway;

    public function __construct($db, $requestMethod, $userId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->userId = $userId;
        $this->OrdineGateway = new OrdineGateway($db);
    }

    public function processRequest()
    {

        switch ($this->requestMethod) {
            case 'GET':
                if(!$this->OrdineGateway->checkTable()){
                    exit(json_encode("Tabella creata"));
                }
                if ($this->userId) {
                    $response = $this->getUser($this->userId);
                }else {
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

    public function apiRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if(!$this->OrdineGateway->checkTable()){
                    exit(json_encode("Tabella creata"));
                }
                if ($this->userId) {
                    return json_decode($this->getUser($this->userId)["body"]);
                } else {
                    return json_decode($this->getAllUsers()["body"]);
                };
                break;
            case 'POST':
                return json_decode($this->createUserFromRequest()->body);
                break;
            case 'PUT':
                return json_decode($this->updateUserFromRequest($this->userId)->body);
                break;
            case 'DELETE':
                return json_decode($this->deleteUser($this->userId)->body);
                break;
            default:
                return json_decode($this->notFoundResponse()->body);
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    public function getalimento($id)
    {
        $result = $this->OrdineGateway->findAlimento($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        echo $response['body'];
    }

    private function getAllUsers()
    {
        $result = $this->OrdineGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getUser($id)
    {
        $result = $this->OrdineGateway->find($id);
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
        // if (is_array($verifica)) {
        //     return $this->unprocessableEntityResponse($verifica);
        // }
        $this->OrdineGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = True;
        return $response;
    }

    private function updateUserFromRequest($id)
    {
        $result = $this->OrdineGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        // $verifica = $this->validatePerson($input);
        // if (is_array($verifica)) {
        //     return $this->unprocessableEntityResponse($verifica);
        // }
        $this->OrdineGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteUser($id)
    {
        $result = $this->OrdineGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->OrdineGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validatePerson($input)
    {
        $error = [];
        if (! isset($input['nome'])) {
            $error[] = 'nome';
        }
        if (! isset($input['cognome'])) {
            $error[] = 'cognome';
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

    public function confermallordine()
    {
        $this->OrdineGateway->confermaordine();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }
}
