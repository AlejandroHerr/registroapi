<?php

namespace Esnuab\Libro\Model\Manager;

use Esnuab\Libro\Model\Entity\Historia;

class HistoriaManager {
    function createHistoria($historia,$app){
        $app['db']->insert('historia',$historia->toArray());
        return;
    }
}