<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Response;

class ApiController extends Controller
{

    protected $statusCode = 200;

    public function getStatusCode(){

        return $this->statusCode;

    }

    public function setStatusCode($statusCode){

        $this->statusCode = $statusCode;
        return $this;

    }

    public function respondNotFound($message = "Not Found"){

        return $this->setStatusCode(200)->respondWithError($message);

    }

    public function respond($data, $headers = []){

        return response()->json($data, $this->getStatusCode(), $headers);

    }

    public function respondWithError($message){

        return $this->respond([
            'error' => [
                'message' => $message,
                'status_code' => $this->getStatusCode()
            ]
        ]);

    }

    public function respondSuccess($message){

        return $this->respond($message);

    }

}