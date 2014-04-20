<?php
namespace Esnuab\Cron\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CronController implements ControllerProviderInterface
{
    protected $confirmationManager;
    protected $mandrill;
    protected $mailchimp;

    public function __construct($confirmationManager,$mandrill,$mailchimp)
    {
        $this->confirmationManager = $confirmationManager;
        $this->mandrill=$mandrill;
        $this->mailchimp=$mailchimp;
    }
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->get('/confirmar',array($this,"confirmSocios"));
        $controllers->get('/suscribir',array($this,"subscribeSocios"));
        $controllers->get('/limpiar',array($this,"cleanConfirmed"));
        $controllers->get('/facturar',array($this,"facturar"));

        return $controllers;
    }

    public function cleanConfirmed(Application $app)
    {
        $this->confirmationManager->deleteConfirmed($app);

        return new Response('',201);
    }
    public function confirmSocios(Application $app)
    {
        $this->confirmationManager->loadUnconfirmed($app);
        $results = $this->sendConfirmation($this->confirmationManager->getUnconfirmed(),$this->confirmationManager->getMergeVars());
        $this->confirmationManager->processResult($app,$results);
        $subRequest = Request::create('/suscribir', 'GET');

        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
    public function sendConfirmation($subjects,$mergeVars)
    {
        $message = array(
            'html' => '<b>hola *|NAME|*,</b><br>tu ESNCard, n&uacute;mero *|ESNCARD|*, es v&aacute;lida hasta el *|EXPIREDATE|* <br><br> Un saludo,<br>el equipo deErasmus Student Network Universitat Autonoma de Barcelona',
            'text' => 'Example text content',
            'subject' => 'Bienvenido a ESN UAB!',
            'from_email' => 'no-reply@esnuab.org',
            'from_name' => 'Erasmus Student Network UAB',
            'to' => $subjects,
            'merge_vars' => $mergeVars
        );
        $async = false;
        $ip_pool = 'Main Pool';
        $send_at = strtotime ('YYYY-MM-DD HH:MM:SS');

        return $this->mandrill->messages->send($message, $async, $ip_pool, $send_at);
    }
    public function subscribeSocios(Application $app)
    {
        $batch=$this->confirmationManager->prepareSubscriptions();
        $result = $this->mailchimp->call('lists/batch-subscribe', array(
                'id'                => 'eb59cf58e5',
                'batch'             => $batch,
                'double_optin'      => false,
                'update_existing'   => true,
                'replace_interests' => false,
            )
        );

        return new Response('',201);
    }
    public function facturar(Application $app)
    {
        $confirmados=$this->confirmationManager->getConfirmed($app);
        if (!$confirmados) {
            return new Response('',201);
        }
        $items= array(
            'concepto' => 'Cuota de socio + ESNCard',
            'cantidad' => 1,
            'precio' => 5,
            'total' => 5,
            'iva' => 0

         );
        foreach ($confirmados as $value) {
            $app['mpdf']->setData('Cuota de socio', $value['name']);
            $app['mpdf']->setTemplate('factura.twig')
                ->addConcepto($items);
            $app['mpdf']->generarPDF();
        }
        $this->confirmationManager->trackPdfGenerated($app, $confirmados);
        $subRequest = Request::create('/limpiar', 'GET');

        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
}
