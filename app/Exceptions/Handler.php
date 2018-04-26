<?php

namespace App\Exceptions;

use App\Traits\APIToolTrait;
use Exception;
use HttpResponseException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    use APIToolTrait;
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if (explode('/',$request->path())[0] == 'api-merchant' || explode('/',$request->path())[0] == 'api-web' || $request->ajax()) {
            $apiFormatter = $this->getAPIFormatter();
            $apiModel = $this->getAPIModel($e);
            if($e instanceof AuthenticationException){
                return response()->json($apiFormatter->error($apiModel), 401);
            }
            if ($e instanceof Exception) {
                return $apiFormatter->error($apiModel);
            }

            if ($e instanceof HttpResponseException) {
                return $e->getResponse();
            }


            $class = get_class($e);

            switch ($class) {
                case 'Illuminate\\Http\\Exception\\HttpResponseException':
                    return parent::render($request, $e);
                    break;
                case 'Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException':
                    $code = 'NotFound';
                    $msg = 'Not Found.';
                    $statusCode = 404;
                    break;
                case 'Illuminate\Database\Eloquent\ModelNotFoundException':
                    $code = 'ModelNotFound';
                    $model = str_replace('App\\Models\\', '', $e->getModel());
                    $msg = $model . ' not found.';
                    $statusCode = 404;
                    break;
                case 'Illuminate\Auth\Access\AuthorizationException':
                    $code = 'InvalidCredentials';
                    $msg = 'Credentials are invalid.';
                    $statusCode = 400;
                    break;
                default:
                    $code = 'SystemError';
                    $msg = $e->getMessage();
                    $file = $e->getFile();
                    $line = $e->getLine();
                    $statusCode = 500;
            }

            $data = [
                'status' => 'error',
                'exception' => $class,
                'code' => $code,
                'msg' =>  $msg
            ];

            if (isset($file)) {
                $data['file'] = $file;
            }

            if (isset($line)) {
                $data['line'] = $line;
            }

            return response($data, $statusCode)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        } else{
            if ($request->format() == 'html') {
                // custom error message
                if ($e instanceof \ErrorException) {
                    return response()->view('errors.500', [], 500);
                }
            }
            return parent::render($request, $e);
        }
        //Log::useDailyFiles(storage_path().'/logs/laravel.log');
        //Log::error($e);

    }
    /*
    public function render($request, Exception $e)
    {
        if(env('APP_DEBUG') == false) {
            // 404 page when a model is not found
            if (!$request->ajax()) {
                if ($e instanceof ModelNotFoundException) {
                    if ($request::format() == 'text/html') {
                        return response()->view('errors.404', [], 404);
                    }
                }

                // custom error message
                if ($e instanceof \ErrorException) {
                    return response()->view('errors.500', [], 500);
                } else {
                    return parent::render($request, $e);
                }
            }
        }
        return parent::render($request, $e);

    }
    */

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }
}
