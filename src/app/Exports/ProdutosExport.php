<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProdutosExport implements FromArray, WithHeadings, WithStyles
{
    protected $dataInicio;
    protected $dataFim;
    protected $produtos;

    public function __construct($dataInicio, $dataFim, $produtos)
    {
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
        $this->produtos = $produtos;
    }

    public function array(): array
    {
        $dados = [];
        
        foreach ($this->produtos as $produto) {
            $dados[] = [
                $produto->nome,
                $produto->total_vendido,
                'R$ ' . number_format($produto->receita_gerada, 2, ',', '.'),
                $produto->quantidade_estoque,
            ];
        }
        
        // Rodapé com totais
        $dados[] = [];
        $dados[] = [
            'TOTAIS',
            $this->produtos->sum('total_vendido'),
            'R$ ' . number_format($this->produtos->sum('receita_gerada'), 2, ',', '.'),
            '',
        ];
        
        return $dados;
    }

    public function headings(): array
    {
        return ['Produto', 'Quantidade Vendida', 'Receita Gerada', 'Estoque Atual'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FF2EB6']]]],
        ];
    }
}
