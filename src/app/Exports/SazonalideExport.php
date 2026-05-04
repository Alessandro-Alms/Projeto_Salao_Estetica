<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SazonalideExport implements FromArray, WithHeadings, WithStyles
{
    protected $dataInicio;
    protected $dataFim;
    protected $sazonalidade;

    public function __construct($dataInicio, $dataFim, $sazonalidade)
    {
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
        $this->sazonalidade = $sazonalidade;
    }

    public function array(): array
    {
        $dados = [];
        
        foreach ($this->sazonalidade as $dia) {
            $dados[] = [
                $dia->dia_nome,
                $dia->total_servicos,
                'R$ ' . number_format($dia->receita_gerada, 2, ',', '.'),
            ];
        }
        
        // Rodapé com totais
        $dados[] = [];
        $dados[] = [
            'TOTAIS',
            $this->sazonalidade->sum('total_servicos'),
            'R$ ' . number_format($this->sazonalidade->sum('receita_gerada'), 2, ',', '.'),
        ];
        
        return $dados;
    }

    public function headings(): array
    {
        return ['Dia da Semana', 'Total de Serviços', 'Receita Gerada'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]]],
        ];
    }
}
