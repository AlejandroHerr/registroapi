<?php

namespace AlejandroHerr\ApiApplication\Debug;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Debug\Exception\FlattenException;


if (!defined('ENT_SUBSTITUTE')) {
    define('ENT_SUBSTITUTE', 8);
}

class JsonExceptionHandler extends ExceptionHandler
{
    private $debug;
    private $charset;

    public function __construct($debug = true, $charset = 'UTF-8')
    {
        $this->debug = $debug;
        $this->charset = $charset;
    }

    public function createResponse($exception,$request)
    {
        if (!$exception instanceof FlattenException) {
            $exception = FlattenException::create($exception);
        }
        $corsHeaders = array(
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers'=>'X-WSSE, content-type',
            'Access-Control-Allow-Methods' => 'POST, GET, PUT, DELETE, OPTIONS');
        $headers = array_merge($exception->getHeaders(),$corsHeaders);

        return new JsonResponse(
            $this->getContent($exception),
            $exception->getStatusCode(),
            $headers
        );
    }

    public function getContent(FlattenException $exception)
    {
        switch ($exception->getStatusCode()) {
            case 404:
                $title = 'Sorry, the page you are looking for could not be found.';
                break;
            default:
                $title = 'Whoops, looks like something went wrong.';
        }

        $content=array();
        $total=null;
        if ($this->debug) {
            try {
                $count = count($exception->getAllPrevious());
                $total = $count + 1;
                foreach ($exception->toArray() as $position => $e) {
                    $ind = $count - $position + 1;
                    $class = $e['class'];
                    $message = nl2br($e['message']);
                    $subcontent=array();
                    $subind=1;
                    foreach ($e['trace'] as $trace) {
                        if ($trace['function']) {
                            $subcontent[$subind++]= sprintf('at %s%s%s(%s)', $trace['class'], $trace['type'], $trace['function'], $this->formatArgs($trace['args']));
                        }
                        if (isset($trace['file']) && isset($trace['line'])) {
                            if ($linkFormat = ini_get('xdebug.file_link_format')) {
                                $link = str_replace(array('%f', '%l'), array($trace['file'], $trace['line']), $linkFormat);
                                $subcontent[$subind++]= sprintf(' in <a href="%s" title="Go to source">%s line %s</a>', $link, $trace['file'], $trace['line']);
                            } else {
                                $subcontent[$subind++]= sprintf(' in %s line %s', $trace['file'], $trace['line']);
                            }
                        }
                    }
                    $content[$ind]=array(
                        'message' => $class.': '.$message ,
                        'subcontent' => $subcontent
                    );
                }
            } catch (\Exception $e) {
                if ($this->debug) {
                    $title = sprintf('Exception thrown when handling an exception (%s: %s)', get_class($exception), $exception->getMessage());

                } else {
                    $title = 'Whoops, looks like something went wrong.';
                }
            }
        }

        return array(
            'title' => $title,
            'total' => $total,
            'content' => $content
        );
    }

    private function formatArgs(array $args)
    {
        $result = array();
        foreach ($args as $key => $item) {
            if ('object' === $item[0]) {
                $formattedValue = sprintf("object(%s)", $item[1]);
            } elseif ('array' === $item[0]) {
                $formattedValue = sprintf("array(%s)", is_array($item[1]) ? $this->formatArgs($item[1]) : $item[1]);
            } elseif ('string'  === $item[0]) {
                $formattedValue = sprintf("'%s'", htmlspecialchars($item[1], ENT_QUOTES | ENT_SUBSTITUTE, $this->charset));
            } elseif ('null' === $item[0]) {
                $formattedValue = 'null';
            } elseif ('boolean' === $item[0]) {
                $formattedValue = ''.strtolower(var_export($item[1], true)).'';
            } elseif ('resource' === $item[0]) {
                $formattedValue = 'resource';
            } else {
                $formattedValue = str_replace("\n", '', var_export(htmlspecialchars((string) $item[1], ENT_QUOTES | ENT_SUBSTITUTE, $this->charset), true));
            }

            $result[] = is_int($key) ? $formattedValue : sprintf("'%s' => %s", $key, $formattedValue);
        }

        return implode(', ', $result);
    }
}
