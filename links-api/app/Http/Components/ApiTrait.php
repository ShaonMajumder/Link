<?php

namespace App\Http\Components;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

trait ApiTrait{
    protected $status   = false;
    protected $message  = "Error";
    protected $access_token;
    protected $data;

    protected function apiSuccess($message = null){
        $this->status = true;
        // $this->message = empty($message) ? "Successfully" : $message;
    }

    public function apiResponseBuilder(int $status_code = 200,string $message = null): array
    {
        if($status_code == 0 ){
            $status_code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        $response = [
            "status"        => $this->status,
            "message"       => !empty($message) ? $message : $this->message,
        ];
        if(!empty($this->access_token)){
            // if(!is_null($this->data)){
            //     $this->data = array_merge(
            //         [
            //             "access_token" => $this->access_token,
            //             "token_type"  => "Bearer"
            //         ],
            //         $this->data
            //     );
            // }else{
            //     $this->data = [
            //             "access_token" => $this->access_token,
            //             "token_type"  => "Bearer"
            //     ];
            // }
            $response['access_token']=$this->access_token;
            $response['token_type']="Bearer";
        }

        
        $success_codes = range(200, 206);
        if (in_array($status_code, $success_codes)) {
            $response['data'] = $this->data;
        } else {
            $response['errors'] = $this->data;
        }

        if( empty($this->access_token) ){
            unset($response["access_token"]);
            unset($response["token_type"]);
        }
        return array($response,$status_code);
    }

    /**
     * Get Exceptional Error
     */
    protected function getExceptionError($e,$debug=true){
        if(env('APP_ENV') == 'local' and $debug) {
            return $e->getMessage() . ' On File' .$e->getFile() . ':' .$e->getLine();
        }else{
            return $e->getMessage(); 
        }
    }

    public function getRouteSlugs()
    {
        $slugs  = [];
        $routes = Route::getRoutes();

        foreach ($routes as $route)
        {
            $slugs[] = $route->uri();
        }

        return array_unique($slugs);
    }

    public function pathExistedOnRoutes($request){
        foreach($this->getRouteSlugs() as $path_existed){
            if(strpos($path_existed, $request->path()) !== false){
                return true;
            }
        }
        return false;
    }
}