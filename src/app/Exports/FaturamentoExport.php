<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FaturamentoExport implements FromArray, WithHeadings, WithStyles
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
        
        // Cabeçalho de resumo
        $dados[] = ['RESUMO DO PERÍODO', $this->dataInicio . ' a ' . $this->dataFim];
        $dados[] = [];
        
        $dados[] = ['Receita de Serviços', 'R$ ' . number_format($this->dados['receitaServicos'], 2, ',', '.')];
        $dados[] = ['Receita de Produtos/Avulsos', 'R$ ' . number_format($this->dados['receitaVendas'], 2, ',', '.')];
        $dados[] = ['Faturamento Total', 'R$ ' . number_format($this->dados['faturamentoTotal'], 2, ',', '.')];
        $dados[] = [];
        
        $dados[] = ['Quantidade de Transações', $this->dados['qtdTransacoes']];
        $dados[] = ['Ticket Médio', 'R$ ' . number_format($this->dados['ticketMedio'], 2, ',', '.')];
        $dados[] = [];
        
        $dados[] = ['Faturamento Anterior', 'R$ ' . number_format($this->dados['faturamentoAnterior'], 2, ',', '.')];
        $dados[] = ['Crescimento (%)', number_format($this->dados['crescimento'], 2, ',', '.')];
        
        return $dados;
    }

    public function headings(): array
    {
        return ['Descrição', 'Valor'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true, 'color' => ['rgb' => '00B050']]],
            8 => ['font' => ['bold' => true]],
            9 => ['font' => ['bold' => true]],
        ];
    }
}
