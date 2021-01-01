<?php
namespace App\Http\Controllers\Api;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\Resource;

trait ApiResponseTrait{

    public function apiResponse($data = null, $error = null, $code = 200){
        $array = [
          "data" => $data,
          "status" => in_array($code, $this->successCode()) ? true : false,
            "error" => $error,
        ];

        return response($array, $code);

    }

    public function successCode(){
        return [
            200,201,202
        ];

    }




}
