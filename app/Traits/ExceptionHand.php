<?php

namespace App\Traits;

use Throwable;
use Illuminate\Routing\Router;
use Illuminate\Http\Response as HTTP;
use Illuminate\Support\Facades\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;

trait ExceptionHand
{

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function track($content)
    {
        $file = md5(time());
        file_put_contents(public_path("$file.html"), $content);
    }
}
