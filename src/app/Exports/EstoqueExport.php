<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EstoqueExport implements FromArray, WithHeadings, WithStyles
{
    protected $produtos;

    public function __construct($produtos)
    {
        $this->produtos = $produtos;
    }

    public function array(): array
    {
        $dados = [];
        
        foreach ($this->produtos as $produto) {
            $capitalPreso = $produto->quantidade_estoque * $produto->valor_unitario;
            $status = $produto->quantidade_estoque <= 5 ? 'ALERTA' : 'OK';
            
            $dados[] = [
                $produto->nome,
                $produto->quantidade_estoque,
                'R$ ' . number_format($produto->valor_unitario, 2, ',', '.'),
                'R$ ' . number_format($capitalPreso, 2, ',', '.'),
                $status,
            ];
        }
        
        // Rodapé com totais
        $dados[] = [];
        $totalCapital = $this->produtos->sum(function($p) { return $p->quantidade_estoque * $p->valor_unitario; });
        $dados[] = [
            'TOTAIS',
            $this->produtos->sum('quantidade_estoque'),
            '',
            'R$ ' . number_format($totalCapital, 2, ',', '.'),
            '',
        ];
        
        return $dados;
    }

    public function headings(): array
    {
        return ['Produto', 'Quantidade', 'Valor Unitário', 'Capital Preso', 'Status'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FF6600']]]],
        ];
    }
}
