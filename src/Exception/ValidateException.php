<?php
namespace Kumaomao\Validate\Exception;


use Hyperf\Server\Exception\ServerException;

class ValidateException extends ServerException
{

    public function __construct(int $code = 0, string $message = null, Throwable $previous = null)
    {
        if (is_null($message)) {
             $message = 'Server Error！';
        }

        parent::__construct($message, $code, $previous);
    }


}
