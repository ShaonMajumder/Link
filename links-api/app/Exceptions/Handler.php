<?php

namespace App\Exceptions;

use App\Http\Components\ApiTrait;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        // dd( $request->path(), $request->path(), $request->method(), Route::getRoutes(),$exception);
        if($this->pathExistedOnRoutes($request)){
            $this->data = [
                'not_found'=>'Your Route URI is incomplete!'
            ];
            // dd($exception->message);
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_NOT_FOUND,
                    $message = 'Your Route URI is incomplete!'
                )
            ); 
        }
        else if ( ($exception instanceof ModelNotFoundException || $exception instanceof RouteNotFoundException || $exception instanceof NotFoundHttpException) && $request->wantsJson()) {
            // dd( $request->header('Content-Type'));
            // return isset($acceptable[0]) && Str::contains(strtolower($acceptable[0]), ['/json', '+json']);
            $this->data = [
                'not_found'=>'Your items was not found!'
            ];
            // dd($exception->message);
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_NOT_FOUND,
                    $message = 'Your Api Resource was not found!'
                )
            );
        }else if ( ( $exception instanceof AuthenticationException) && $request->wantsJson()) {
            $this->data = [
                'not_authenticated'=>'You need to be authenticated to access this route !'
            ];
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_UNAUTHORIZED,
                    $message = 'You are not authenticated to access API Resource!'
                )
            );
        }else if ( ( $exception instanceof MethodNotAllowedHttpException) && $request->wantsJson()) {
            $this->data = [
                'not_allowed'=>'Your request method is not allowed for the route, or route not found or not authenticated!'
            ];
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_METHOD_NOT_ALLOWED,
                    $message = 'Your Request method is not allowed !'
                )
            );
        }

        return parent::render($request, $exception);
    }
}
