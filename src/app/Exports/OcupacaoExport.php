<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OcupacaoExport implements FromArray, WithHeadings, WithStyles
{
    protected $dataInicio;
    protected $dataFim;
    protected $ocupacaoPorHora;
    protected $ocupacaoPorDia;

    public function __construct($dataInicio, $dataFim, $ocupacaoPorHora, $ocupacaoPorDia)
    {
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
        $this->ocupacaoPorHora = $ocupacaoPorHora;
        $this->ocupacaoPorDia = $ocupacaoPorDia;
    }

    public function array(): array
    {
        $dados = [];
        
        $dados[] = ['OCUPAÇÃO POR HORA DO DIA'];
        $dados[] = [];
        
        foreach ($this->ocupacaoPorHora as $hora) {
            $dados[] = [$hora->hora . ':00', $hora->total];
        }
        
        $dados[] = [];
        $dados[] = ['OCUPAÇÃO POR DIA DA SEMANA'];
        $dados[] = [];
        
        $diasNomes = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
        foreach ($this->ocupacaoPorDia as $dia) {
            $nomeDia = $diasNomes[$dia->dia_semana] ?? 'Desconhecido';
            $dados[] = [$nomeDia, $dia->total];
        }
        
        return $dados;
    }

    public function headings(): array
    {
        return ['Período', 'Total de Agendamentos'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '70AD47']]]],
            7 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '70AD47']]]],
        ];
    }
}
