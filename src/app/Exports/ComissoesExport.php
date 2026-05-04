<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ComissoesExport implements FromArray, WithHeadings, WithStyles
{
    protected $dataInicio;
    protected $dataFim;
    protected $comissoes;

    public function __construct($dataInicio, $dataFim, $comissoes)
    {
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
        $this->comissoes = $comissoes;
    }

    public function array(): array
    {
        $dados = [];
        
        foreach ($this->comissoes as $comissao) {
            $dados[] = [
                $comissao->name,
                $comissao->telefone,
                $comissao->total_servicos,
                'R$ ' . number_format($comissao->receita_gerada, 2, ',', '.'),
                'R$ ' . number_format($comissao->comissao_a_pagar, 2, ',', '.'),
            ];
        }
        
        // Rodapé com totais
        $dados[] = [];
        $dados[] = [
            'TOTAIS',
            '',
            $this->comissoes->sum('total_servicos'),
            'R$ ' . number_format($this->comissoes->sum('receita_gerada'), 2, ',', '.'),
            'R$ ' . number_format($this->comissoes->sum('comissao_a_pagar'), 2, ',', '.'),
        ];
        
        return $dados;
    }

    public function headings(): array
    {
        return ['Profissional', 'Telefone', 'Total de Serviços', 'Receita Gerada', 'Comissão a Pagar'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '7B19E5']]]],
            'A' => ['alignment' => ['horizontal' => 'left']],
        ];
    }
}
