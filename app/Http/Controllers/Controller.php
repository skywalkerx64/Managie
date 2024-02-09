<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function handleResponse($result, string $message, int $code = Response::HTTP_OK, bool $success = true)
    {
        // Format response
        $response = [
            "success" => $success,
            "data"    => $result,
            "message" => $message
        ];
        // return response
        return response()->json($response, $code);
    }

     public function json_abort_if(bool $condition, string $message, int $code)
    {
        if($condition) {
            return $this->json_abort($message, $code);
        }
    }
    public function json_abort(string $message, int $code)
    {
        return response()->json(["message" => $message], $code);
    }
}
