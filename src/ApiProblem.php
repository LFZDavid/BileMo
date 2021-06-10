<?php

namespace App;

use Symfony\Component\HttpFoundation\Response;

class ApiProblem
{
    const TYPE_VALIDATION_ERROR = 'validation_error';
    const TYPE_INVALID_REQUEST_BODY_FORMAT = 'invalid_body_format';

    private int $statusCode;
    private string $type;
    private string $title;
    private array $extraData = [];
    private static $titles = array(
        self::TYPE_VALIDATION_ERROR => 'There was a validation error',
        self::TYPE_INVALID_REQUEST_BODY_FORMAT => 'Invalid JSON format sent',
    );

    public function __construct(int $statusCode, string $type = null)
    {
        $this->statusCode = $statusCode;
        if ($type === null) {
            $type = 'about:blank';
            $title = isset(Response::$statusTexts[$statusCode])
                ? Response::$statusTexts[$statusCode]
                : 'Unknown status code :(';
        }else{
            if (!isset(self::$titles[$type])) {
                throw new \InvalidArgumentException('No title for type '.$type);
            }
            $title = $title ?? self::$titles[$type];
        }

        $this->type = $type;
        $this->title = $title;
    }

    public function getStatusCode():int
    {
        return $this->statusCode;
    }

    public function getType():string
    {
        return $this->type;
    }

    public function getTitle():string
    {
        return $this->title;
    }
   
    public function set($name, $value): void
    {
        $this->extraData[$name] = $value;
    }

    public function get($name, $value): ?mixed
    {
        if(!array_key_exists($name, $this->extraData)){
            return null;
        }
        return $this->extraData[$name] = $value;
    }

    public function toArray():array
    {
        return array_merge(
            $this->extraData,
            [
                'status' => $this->statusCode,
                'type' => $this->type,
                'title' => $this->title,
            ]
        );
    }

}
