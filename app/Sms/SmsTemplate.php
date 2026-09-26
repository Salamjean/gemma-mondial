<?php

namespace App\Sms;

use GuzzleHttp\Client;

class SmsTemplate
{

        public function __construct($phone, $message){
            $this->phone = $phone;
            $this->message = $message;
        }

        protected $message;
        protected $phone;
        protected $headers = [
            'Cookie' => 'PHPSESSID=hodjov6cacnt3akre24fkhbj62',
        ];

        public function send(){

            $client = new Client();

            $params = [
                'sendsms' => 'null',
                'apikey' => config('sms.apiKey'),
                'apitoken' => config('sms.token'),
                'type' => 'sms',
                'from' => config('sms.sender_id'),
                'to' => "+225$this->phone",
                'text' => $this->message,
            ];

            $response = $client->request('GET', config('sms.url'), [
                'query' => $params,
                'headers' => $this->headers,
            ]);

            $responseBody = $response->getBody()->getContents();

            return $responseBody;

        }

}
