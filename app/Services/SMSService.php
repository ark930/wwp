<?php

namespace App\Services;


use App\Contracts\SMSServiceContract;
use App\Exceptions\BadRequestException;
use Exception;
use GuzzleHttp\Exception\RequestException;

class SMSService implements SMSServiceContract
{
    use HttpClientTrait;

    protected $apiKey = null;
    protected $client = null;

    const BASE_URL = 'https://sms.yunpian.com/v2/';

    public function __construct()
    {
        $this->apiKey = env('YUNPIAN_API_KEY');
        if(empty($this->apiKey)) {
            throw new BadRequestException('YUNPIAN_API_KEY is invalid');
        }

        $this->initHttpClient(self::BASE_URL);
    }

    public function SendSMS($tel, $message)
    {
        $body = $this->requestForm('POST', 'sms/single_send.json', [
            'apikey' => $this->apiKey,
            'mobile' => $tel,
            'text' => $message,
        ]);

        return $body;
    }

    public function SendSMSByTemplate($tel, $temp_id, $code)
    {
        $body = $this->requestForm('POST', 'sms/tpl_single_send.json', [
            'apikey' => $this->apiKey,
            'mobile' => $tel,
            'tpl_id' => $temp_id,
            'tpl_value' => urlencode("#code#") . "=" . urlencode($code),
        ]);

        return $body;
    }

    protected function exceptionHandler(Exception $e)
    {
        if($e instanceof RequestException) {
            $response = $e->getResponse();
            if(is_null($response)) {
                throw new BadRequestException($e->getMessage());
            }

            $code = $response->getStatusCode();
            $body = $response->getBody();
            $message = \GuzzleHttp\json_decode($body, true)['msg'];

            throw new BadRequestException($message, $code);
        }

        throw new BadRequestException($e->getMessage(), $e->getCode());
    }

}
