<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {

		 if (!empty( trim( $e->getMessage() ) )) {
			// emails.exception is the template of your email
			// it will have access to the $error that we are passing below
			 Log::info('URL YANG error : ' . Input::fullUrl());
			 Log::info('Method yang error : ' . Input::method());
			 Log::info('Pada Jam : ' . date('Y-m-d H:i:s'));
			 if (gethostname() != 'yoga') {
				 //Mail::send('email.error', [
					 //'url'    => Input::url(),
					 //'method' => Input::method(),
					 //'error'  => $e->getMessage() . ' pada jam ' . date('Y-m-d H:i:s')
				 //], function($m){
					  //$m->from('admin@mailgun.org', 'Yoga Hadi Nugroho');
					  //$m->to('yoga_email@yahoo.com', 'Yoga Hadi Nugroho');
					  //$m->subject('Error from KJE');
				 //});
				 //try {
					 
				 //} catch (\Exception $e) {
					 //Log::info($e->getMessage();
				 //}
				 //$sv  = new dbBackup;
				 //$sv->handle();
				 //$lg = new sendMeLaravelLog;
				 //$lg->sendLog();
				 Sms::send(env("NO_HP_OWNER"),$e->getMessage() . ' pada jam ' . date('Y-m-d H:i:s') );
			 }
		}
        parent::report($e);
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
