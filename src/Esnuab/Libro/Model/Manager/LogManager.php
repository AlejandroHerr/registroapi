<?php

namespace Esnuab\Libro\Model\Manager;

use Functional as F;

class LogManager
{
    const DATE_REGEX = '/[2][0-9]{3}-[0-1][0-9]-[0-3][0-9]/';
    const TYPE_REGEX = '/[A-Za-z]+/';

    protected $extension;
    protected $path;

    public function __construct($path, $extension = 'log')
    {
        $this->extension = $extension;
        $this->path = $path;
    }

    public function getLogsJoined($date = null, $type = null, $asArray = true)
    {
        $logs = $this->getCollection($data, $type, false);
        $logs = F\reduce_left($logs, function ($log, $index, $collection, $reduction) {
                return $this->joinLogs($log, $index, $collection, $reduction);
            }
        );
        $log = $this->json2array($logs);
        $log = $this->orderByDate($log);

        return $log;
    }

    public function getCollection($date = null, $type = null, $withMetadata = true)
    {
        $logs = scandir($this->path);

        /** Remove non files **/
        $logs = F\select($logs, function ($log) {
                return !($log == '.' || $log == '..');
            }
        );

        if (null !== $date) {
            $fn = function ($log) use ($date) {
                return $this->isOnDate($log, $date);
            };
            $logs = F\select($logs, $fn);
        }

        if (null !== $type) {
            $fn = function ($log) use ($type) {
                return $this->isOnDate($log, $type);
            };
            $logs = F\select($logs, $fn);
        }
        if ($withMetadata) {
            $fn = function ($log) {
                return $this->setMetadata($log);
            };
            $logs = F\map($logs, $fn);
        }

        return $logs;
    }

    /*********
     Protected methods
     *********/

    protected function json2array($log)
    {
        $log = explode(PHP_EOL, $log);
        $log = F\map($log,function ($entry) {
                return json_decode($entry, true);
            }
        );

        return $log;
    }

    protected function openResource($resourceName, $asArray = true)
    {
        $resource = file_get_contents($resourceName);
        while ( substr($resource,strlen($resource)-1) == PHP_EOL ) {
            $resource = substr($resource, 0, strlen($resource)-1);
        }

        if (!$asArray) {
            return $resource;
        }

        return $this->json2array($resource);
    }

    protected function orderByDate($log)
    {
        $dates = F\pluck(F\pluck($log,'datetime'),'date');
        array_multisort($dates, SORT_DESC, SORT_STRING, $log);

        return $log;
    }

    /*********
     Private methods, used mostly for the functional calls
     *********/

    private function isOfType($log, $type)
    {
        preg_match(self::TYPE_REGEX, $log, $logType);
        if (count($logType) > 0) {
           return ($type == $logType[0]);
        }

        return false;
    }

    private function isOnDate($log, $date)
    {
        preg_match(self::DATE_REGEX, $log, $logDate);
        if (count($logDate) > 0) {
           return ($date == $logDate[0]);
        }

        return false;
    }

    private function joinLogs($log, $index, $collection, $reduction)
    {
        $log = $this->openResource($this->path . '/' . $log, false);

        return $reduction . $log;
    }

    private function setMetadata($log, $path = null)
    {
        $array = array();

        $array['filename']= $log;
        $array['size'] = filesize($this->path . '/' . $log)/1000 ."kB";

        preg_match(self::DATE_REGEX, $log, $array['date']);
        $array['date'] = $array['date'][0];

        preg_match(self::TYPE_REGEX, $log, $array['type']);
        $array['type'] = $array['type'][0];

        return $array;
    }

    public function getResource($logName, $asArray = true)
    {
        $logName .= '.' . $this->extension;
        $logs = scandir($this->path);

        if (!F\contains($logs,$logName)) {
            throw new \Exception();
        }

        return $this->openResource($this->path.'/'.$logName, $asArray);
    }

}
