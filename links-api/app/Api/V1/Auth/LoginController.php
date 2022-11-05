<?php
namespace App\Api\V1\Auth;

use App\Http\Components\ApiTrait;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

/**
 * Login Controller For API V1
 */
Class LoginController extends Controller{
    use ApiTrait;

    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => ['required', 'exists:users,email'],
            'password' => ['required']
        ]);

        if($validator->fails()){
            $this->data = $validator->errors(); //->first();
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_UNPROCESSABLE_ENTITY,
                    $message = ''
                )
            );
        }

        $user = User::where('email',$request->email)
                    // ->where('is_active',true)
                    ->first();
        if(!$user){
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_FORBIDDEN,
                    $message     = 'User was not found or active !!!',
                )
            );
        }
        
        $this->access_token = $user->createToken( $request->device_name ?? ($request->ip() ?? "Unknown") )->plainTextToken;
        $this->apiSuccess();
        $this->data = [
            'profile' => $user//new UserResource($user)
        ];

        return response()->json(
            ...$this->apiResponseBuilder(
                $status_code = Response::HTTP_OK,
                $message     = 'Login Successfully',
            )
        );
    }

    /**
     * Logout current user
     */
    public function logout(Request $request){
        $user = $request->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        $this->apiSuccess();
        return response()->json(
            ...$this->apiResponseBuilder(
                $status_code = Response::HTTP_OK,
                $message     = 'Logout Successfully',
            )
        );
    }
}