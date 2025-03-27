<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Encryption\DecryptException;
use Exception;
use Crypt;
use GENERAL;


class SMSController extends Controller
{
    protected $username;
    protected $password;
    protected $url;
    public function __construct()
    {
      $this->username = 'noreply@southernleytestateu.edu.ph';
      $this->password = 'N3stn13!';
      $this->url = 'https://messagingsuite.smart.com.ph/cgpapi/messages/sms';
    }

    public function send($contactnumber = '', $sms = '')
    {
      if (substr($contactnumber, 0, 1) == '0') {
        $contactnumber = '63' . substr($contactnumber, 1, 10);
      }
  
      $response = Http::withHeaders([
        'Content-Type' => 'application/json;charset=UTF-8',
      ])
        ->withOptions(['verify' => false])
        ->post($this->url, [
          'username' => $this->username,
          'password' => $this->password,
          'messageType' => 'sms',
          'destination' => $contactnumber,
          'text' => $sms,
        ])
        ->json();
  
      if (isset($response['status']) and $response['status'] == 'ENROUTE') {
        return ['Error' => 0, 'Message' => ''];
      }
  
      return ['Error' => 1, 'Message' => $response['errorDescription']];
    }
}
