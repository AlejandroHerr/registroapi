<?php
namespace Esnuab\Facturador;

use Silex\Application;

class Facturador extends \mPDF
{
    protected $conceptos=array();
    protected $context;
    protected $data=array();
    protected $factura;
    protected $facturaId;
    protected $total;
    protected $variables;

    public function __construct(Application $app,$template=null)
    {
        $this->newMPDF();
        $this->context=$app;
        $this->setTemplate($template);
    }
    public function addConcepto($concepto)
    {
        $this->conceptos[]=$concepto;

        return $this;
    }
    public function generarPDF($dest='F',$filename=null)
    {
        $this->render();
        parent::WriteHTML($this->factura);
        if ($filename == null) {
            $filename = '/var/facturas/factura_' .  $this->data['id'] . '.pdf';
        }
        $this->data['archivo']=$filename;
        $output = parent::Output(ROOT . $filename,$dest);
        $this->recordFactura();

        return $output;
    }
    public function setData($concepto,$receptor)
    {
        $this->data['concepto']=$concepto;
        $this->data['receptor']=$receptor;
        $this->data['fecha']=date("Y-m-d");
        $this->getNumeroFactura();
        $this->data['id']=$this->facturaId['facturaId'];

        return $this;
    }
    public function setTemplate($template)
    {
        $this->template=$template;

        return $this;
    }
    public function resetConceptos()
    {
        unset($this->conceptos);

        return $this;
    }
    public function resetTemplate()
    {
        $this->template='';

        return $this;
    }
    protected function calcularTotal()
    {
        $this->total=array(
            'iva' => 0,
            'subtotal' => 0,
            'total' => 0
            );
        foreach ($this->conceptos as  $value) {
            $this->total['subtotal'] += $value['total'];
            $this->total['iva'] += ($value['total']*$value['iva'])/100;
        }
        $this->total['total'] = $this->total['subtotal'] + $this->total['iva'];
    }
    protected function getNumeroFactura()
    {
        $query="SELECT MAX( id ) AS facturaId FROM facturas";
        $this->facturaId=$this->context['db']->fetchAssoc($query);
        $this->facturaId['facturaId']++;
    }

    protected function newMPDF()
    {
        parent::mPDF('utf-8','A4','','',20,15,48,25,10,10);
    }
    protected function recordFactura()
    {
        $this->context['db']->insert('facturas', $this->data);
    }
    protected function render()
    {
        $this->calcularTotal();
        $this->variables=array_merge(
            array(
                'items' => $this->conceptos
            ),
            array(
                'total' => $this->total
            ),
            array(
                "fecha" => date("d-m-Y")
            ),
            $this->facturaId
        );
        $this->factura=$this->context['twig']->render(
            $this->template,
            $this->variables
        );

        return $this;
    }
}
