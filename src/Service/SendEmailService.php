<?php
namespace Megaads\Trapman\Service;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SendEmailService
{
    private $message = [];
    private $exceptionData = [];
    private $decorated = "";

    public function __construct()
    {

    }

    public function sendEmailRequest()
    {
        $sentData = [
            "url" => config('app.url'),
            "data" => $this->exceptionData
        ];

        $result = $this->curlRequest('http://email.megaads.net/api/send-mail', $sentData, 'POST');
    }

    /**
     * @param $exception
     * @return SymfonyResponse
     */
    public function buildException($exception, $is_html = false)
    {
        $exception = FlattenException::create($exception);
        $statusCode = $exception->getStatusCode();
        if ( !$is_html ) {
            $exceptionItem = [];
            $exceptionItem["message"] = $exception->getMessage();
            $exceptionItem["file"] = $exception->getFile();
            $exceptionItem["line"] = $exception->getLine();
            $exceptionItem["previous"] =  $exception->getPrevious();
            if ( isset($this->exceptionData[$statusCode]) ) {
                $allError = $this->exceptionData[$statusCode]["items"];
                foreach( $allError as $error ) {
                    if ( strcmp($error["message"], $exceptionItem["message"]) !== 0) {
                        array_push( $this->exceptionData[$statusCode]["items"], $exceptionItem);
                    }
                }
            } else {
                $this->exceptionData[$statusCode]["items"][] = $exceptionItem;
            }
        } else {
            $this->exceptionData = $this->convertExceptionToResponse($exception);
        }
    }

    /**
     * @param $decorated
     * @return mixed
     */
    public function setDecoreated($decorated)
    {
        $this->decorated = $decorated;
    }

    public function getExceptionData()
    {
        return $this->exceptionData;
    }

    /**
     * @param $exception
     * @return SymfonyResponse
     */
    private function convertExceptionToResponse($exception)
    {

        $handler = new SymfonyExceptionHandler(true); //alway generate with mode debug = true.
        
        $decorated = $this->generateDecorated($handler->getContent($exception), $handler->getStylesheet($exception));

        return SymfonyResponse::create($decorated, $exception->getStatusCode(), $exception->getHeaders());
    }

    /**
     * @param $contents
     * @param $stylesheet
     * @return mixed|string
     */
    private function generateDecorated($contents, $stylesheet)
    {
        $this->decorated = str_replace('#contents', $contents, $this->decorated);
        $this->decorated = str_replace('#stylesheets', $stylesheet, $this->decorated);
        return $this->decorated;
    }

    /**
     * @param $exception
     * @return bool
     */
    private function isHttpException($exception)
    {
        return $exception instanceof \HttpException;
    }

    /**
     * @param $url
     * @param array $data
     * @param string $method
     * @param bool $isAsync
     * @return mixed
     */
    private function curlRequest($url, $data = [], $method = "GET" , $isAsync = false) {
        $channel = curl_init();
        curl_setopt($channel, CURLOPT_URL, $url);
        curl_setopt($channel, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($channel, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($channel, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($channel, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($channel, CURLOPT_MAXREDIRS, 3);
        curl_setopt($channel, CURLOPT_POSTREDIR, 1);
        curl_setopt($channel, CURLOPT_TIMEOUT, 10);
        curl_setopt($channel, CURLOPT_CONNECTTIMEOUT, 10);
        if($isAsync){
            curl_setopt($channel, CURLOPT_NOSIGNAL, 1);
            curl_setopt($channel, CURLOPT_TIMEOUT_MS, 400);
        }
        $response = curl_exec($channel);
        return $response;
    }

}