<?php

use Symfony\Component\HttpFoundation\Response;

$status   = false;
$message  = "Error";
$access_token;
$data;

if (!function_exists('apiResponse')) {
    //int|string $status_code 
    function apiResponse(int $status_code = 0, string $message, $responseDataOrError = []): array
    {
        if($status_code == 0 ){
            $status_code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        
        $response['status'] = $status;
        $response['message'] = $message;
                        
        $success_codes = range(200, 206);
        if(!empty($this->access_token)){
            if(!is_null($this->data)){
                $this->data = array_merge(
                    [
                        "access_token" => $this->access_token,
                        "token_type"  => "Bearer"
                    ],
                    $this->data
                );
            }else{
                $this->data = [
                        "access_token" => $this->access_token,
                        "token_type"  => "Bearer"
                    ];
            }
            
        }

        if (in_array($status_code, $success_codes)) {
            $response['data'] = $this->data;
        } else {
            $response['errors'] = $responseDataOrError;
        }
        return $response;
    }
}
