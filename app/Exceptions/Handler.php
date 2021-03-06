<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Sms;
use App\Console\Commands\sendMeLaravelLog;
use App\Console\Commands\dbBackup;
use Log;
use Input;
use Mail;
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
		\Illuminate\Auth\AuthenticationException::class,
		\Illuminate\Auth\Access\AuthorizationException::class,
		\Illuminate\Validation\ValidationException::class,
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
     * @param  \Exception  $exception
     * @return void
     */
	public function report(Exception $exception)
	{
		 if (!empty( trim( $exception->getMessage() ) )) {
			 Log::info('URL YANG error : ' . Input::fullUrl());
			 Log::info('Input : ' . json_encode(Input::all()));
			 Log::info('ERRRROORRRR');
			 Log::info('Method yang error : ' . Input::method());
			 Log::info('Memory Usage ' . memory_get_usage());
			 Log::info('Pada Jam : ' . date('Y-m-d H:i:s'));
			 Log::info($exception->getMessage());
			 Log::info('=====================================================================================================');
			 Log::info($exception->getTraceAsString());
			 Log::info('=====================================================================================================');
			 Sms::send(env("NO_HP_OWNER"), $exception->getMessage() . ' ' . $exception->getTraceAsString() . ' pada jam ' . date('Y-m-d H:i:s') );
		 }
		return parent::report($exception);
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
        return parent::render($request, $exception);
    }
}
