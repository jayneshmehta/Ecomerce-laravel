<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MailController extends Controller
{
    public function basic_email() {
        $data = array('name'=>"jaynesh mehta");
     
        Mail::send(['text'=>'mail'], "hello mail recived", function($message) {
           $message->to('jayneshmehta21@gmail.com', 'laravel mail sending')->subject
              ('Laravel Basic Testing Mail');
           $message->from('testphp@mailtest.radixweb.net','CubeX');
        });
        echo "Basic Email Sent. Check your inbox.";
     }

     public function html_email() {
      $data = array('name'=>"jaynesh mehta");
      Mail::send('mail', $data, function($message) {
         $message->to('jayneshmehta21@gmail.com', 'laravel mail sending')->subject
            ('Laravel HTML Testing Mail');
         $message->from('testphp@mailtest.radixweb.net','CubeX');
      });
      echo "HTML Email Sent. Check your inbox.";
   }
    //  public function attachment_email() {
    //     $data = array('name'=>"Virat Gandhi");
    //     Mail::send('mail', $data, function($message) {
    //        $message->to('abc@gmail.com', 'Tutorials Point')->subject
    //           ('Laravel Testing Mail with Attachment');
    //        $message->attach('C:\laravel-master\laravel\public\uploads\image.png');
    //        $message->attach('C:\laravel-master\laravel\public\uploads\test.txt');
    //        $message->from('xyz@gmail.com','Virat Gandhi');
    //     });
    //     echo "Email Sent with attachment. Check your inbox.";
    //  }
}