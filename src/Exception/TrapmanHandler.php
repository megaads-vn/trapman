<?php
namespace Megaads\Trapman\Exception;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler as HandlerInterface;

class TrapmanHandler implements HandlerInterface
{
    protected $dontReport = [];


    public function report(Exception $e)
    {
        if ( $this->shouldntReport($e) ) {
            return;
        }

        try{
            var_dump($e);
        } catch (Exception $ex) {
            throw $e;
        }
    }


    public function render($request, Exception $e)
    {
        // TODO: Implement render() method.
    }

    public function shouldReport(Exception $e)
    {
        return !$this->shouldntReport($e);
    }

    public function shouldntReport(Exception $e)
    {
        return !is_null(collect($this->dontReport)->first(function($type) use ($e) {
            return $e instanceof $type;
        }));
    }
}