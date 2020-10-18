<?php

namespace App\Exceptions;

use Exception;
use hisorange\BrowserDetect\Stages\BrowserDetect;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
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
    public function render($request, Exception $exception)
    {
//        if ($request->isJson())
//        if ($exception instanceof ModelNotFoundException)
//        if (strpos($request->getUri(), '/api/'))
        {
//            $request->wantsJson();
//            Content-Type application/json

//            Accept      application/json
//            $request->isJson();

//            $request->ajax()
            if ($request->wantsJson())
            {
                if (config('app.debug'))
                {
                    $meta = [

                    ];
                    return response()->json([
                        'error' => [
                            'code' => $exception->getCode(),
                            'message' => $exception->getMessage(),
                            'file' => $exception->getFile(),
                            'line' => $exception->getLine(),
                            'exception' => get_class($exception),
                        ]
                    ]);
                }
                else
                {
                    return response()->json([
                        'error' => [
                            'code' => $exception->getCode(),
                            'message' => $exception->getMessage(),
                        ]
                    ]);
                }
            }
            else
            {
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($exception->guards() === 'admin')
            $url = route('admin.login');
        else
            $url = route('wap.login');

        return $request->expectsJson()
            ? response()->json(['message' => $exception->getMessage()], 401)
            : redirect()->guest($url);
    }
}
