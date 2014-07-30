<?php
namespace Esnuab\Libro\Controller;

use Functional as F;
use Silex\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class LogController implements ControllerProviderInterface
{
    protected $logPath;
    const ASSERT_DATE = '[2][0-9]{3}-[0-1][0-9]-[0-3][0-9]';
    const DATE_REGEX = '/[2][0-9]{3}-[0-1][0-9]-[0-3][0-9]/';
    const LOG_EXTENSION = '.log';
    const LOG_PREFIX = 'app_';

    public function __construct($logPath)
    {
        $this->logPath = $logPath;
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->get('/', array($this,"getLogs"));
        $controllers->get('/{date}', array($this,"getLog"))
        ->assert('date',self::ASSERT_DATE);
        $controllers->get('/{date1}/{date2}/', array($this,"getRange"))
        ->assert('date1',self::ASSERT_DATE)
        ->assert('date2',self::ASSERT_DATE);

        return $controllers;
    }

    public function getLogs(Application $app)
    {
        $logs = $this->loadLogs();
        $logs = F\map($logs,function ($log) {return $this->setSize($log);});

        return $app->json(array('logs' => $logs));
    }

    public function getLog($date)
    {
        $log = $this->openLog($date);

        return new JsonResponse(array('log' => $log));
    }

    public function getRange(Application $app, $date1,$date2)
    {
        $logs = $this->loadLogs();
        if (strtotime($date1)>strtotime($date2)) {
            $date0 = $date2;
        } else {
            $date0 = $date1;
            $date1 = $date2;
        }
        $logsInRange = F\select($logs,function ($log) use ($date0,$date1) {return $this->isInRange($log,$date0,$date1);});
        $logsInRange = F\reduce_left($logsInRange,function ($log, $index, $collection, $reduction) {return $this->joinLogs($log, $index, $collection, $reduction);});
        $log = $this->log2array($logsInRange);

        return $app->json($log);
    }
    protected function loadLogs($order = SCANDIR_SORT_DESCENDING)
    {
        $logs = scandir($this->logPath,$order);
        $logs = F\select($logs, function ($log) {return $this->hasLogExtension($log);});

        return $logs;
    }

    protected function log2array($log)
    {
        $log = explode(PHP_EOL, $log);
        $log = F\map($log,function ($entry) {return json_decode($entry);});

        return $log;
    }

    protected function openLog($logName)
    {
        $log = $this->openLogFile($logName);

        return $this->log2array($log);
    }

    protected function openLogFile($logName)
    {
        if (!preg_match('/^app_/', $logName)) {
            $logName = self::LOG_PREFIX . $logName . self::LOG_EXTENSION;
        }
        $logs = scandir($this->logPath);

        if (!F\contains($logs,$logName)) {
            throw new \Exception();
        }
        $log = file_get_contents($this->logPath.'/'.$logName);
        while ( substr($log,strlen($log)-1) == PHP_EOL ) {
            $log = substr($log, 0, strlen($log)-1);
        }

        return $log;

    }

    private function isInRange($log, $date0, $date1)
    {
        preg_match(self::DATE_REGEX, $log, $date);
        $date = strtotime($date[0]);

        return ($date >= strtotime($date0)) && ($date <= strtotime($date1));
    }

    private function joinLogs($log, $index, $collection, $reduction)
    {
        $log = $this->openLogFile($log);

        return $reduction . $log;
    }

    private function hasLogExtension($log)
    {
        $regex = '/' . self::LOG_EXTENSION . '$/';
        if (preg_match($regex, $log)) {
            return true;
        }

        return false;
    }

    private function setSize($log)
    {
        $array = array();
        $array['filename']=$log;
        $array['size'] = filesize($this->logPath . '/' . $log)/1000 ."kB";
        preg_match(self::DATE_REGEX, $log, $array['date']);
        $array['date']=$array['date'][0];

        return $array;
    }

    /*********** TO FILE
    /****
    /**
    /** $log = file_get_contents($this->logPath.'/'.$logName);
    /**
    /** return new Response($log,200,array('Content-Type' => 'text/plain'));
    /**
    /****
    /*******************/
}
