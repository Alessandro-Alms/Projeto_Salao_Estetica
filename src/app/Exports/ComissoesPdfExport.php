<?php

namespace App\Exports;

use Barryvdh\DomPDF\Facade\Pdf;

class ComissoesPdfExport
{
    protected $dataInicio;
    protected $dataFim;
    protected $comissoes;
    protected $totalGeralComissoes;
    protected $totalServicosRealizados;

    public function __construct($dataInicio, $dataFim, $comissoes, $totalGeralComissoes, $totalServicosRealizados)
    {
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
        $this->comissoes = $comissoes;
        $this->totalGeralComissoes = $totalGeralComissoes;
        $this->totalServicosRealizados = $totalServicosRealizados;
    }

    public function download()
    {
        $pdf = Pdf::loadView('admin.relatorios.comissoes-pdf', [
            'dataInicio' => $this->dataInicio,
            'dataFim' => $this->dataFim,
            'comissoes' => $this->comissoes,
            'totalGeralComissoes' => $this->totalGeralComissoes,
            'totalServicosRealizados' => $this->totalServicosRealizados,
        ]);

        return $pdf->download('comissoes_' . $this->dataInicio . '.pdf');
    }
}
