<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Endroid\QrCode\QrCode;
use Input;

class QrCodeController extends Controller
{


    public function index(){
		$text = Input::get('text');
		$qr = new QrCode();
		$qr->setText($text);
		$qr->setMargin(10);
		$qr->setSize(200);
		header('Content-Type: '.$qr->getContentType());
		return $qr->writeString();
    }
}
