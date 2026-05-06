<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinanceiroExport implements FromArray, WithHeadings, WithStyles
{
    protected $dataInicio;
    protected $dataFim;
    protected $dados;

    public function __construct($dataInicio, $dataFim, $dados)
    {
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
        $this->dados = $dados;
    }

    public function array(): array
    {
        $dados = [];
        
        // ENTRADAS
        $dados[] = ['ENTRADAS (RECEITAS)'];
        $dados[] = ['Receita de Serviços', 'R$ ' . number_format($this->dados['receitaServicos'], 2, ',', '.')];
        $dados[] = ['Receita de Produtos', 'R$ ' . number_format($this->dados['receitaProdutos'], 2, ',', '.')];
        $dados[] = ['Receita de Pacotes', 'R$ ' . number_format($this->dados['receitaPacotes'], 2, ',', '.')];
        $dados[] = ['Receita de Multas', 'R$ ' . number_format($this->dados['receitaMultas'], 2, ',', '.')];
        $dados[] = ['Total de Entradas', 'R$ ' . number_format($this->dados['totalEntradas'], 2, ',', '.')];
        $dados[] = [];
        
        // SAÍDAS
        $dados[] = ['SAÍDAS (DESPESAS)'];
        $dados[] = ['Despesa com Comissões', 'R$ ' . number_format($this->dados['despesaComissoes'], 2, ',', '.')];
        $dados[] = ['Total de Saídas', 'R$ ' . number_format($this->dados['totalSaidas'], 2, ',', '.')];
        $dados[] = [];
        
        // SALDO
        $dados[] = ['RESULTADO LÍQUIDO', 'R$ ' . number_format($this->dados['saldoLiquido'], 2, ',', '.')];
        
        return $dados;
    }

    public function headings(): array
    {
        return ['Descrição', 'Valor'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF'], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1F4E78']]]],
            6 => ['font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '00B050']]]],
            10 => ['font' => ['bold' => true, 'size' => 11]],
            12 => ['font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF'], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '92D050']]]],
        ];
    }
}
