<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CancelamentosExport implements FromArray, WithHeadings, WithStyles
{
    protected $dataInicio;
    protected $dataFim;
    protected $ofensores;
    protected $totalEvasoes;
    protected $prejuizoTotal;
    protected $totalMultasRecuperadas;
    protected $taxaEvasao;

    public function __construct($dataInicio, $dataFim, $ofensores, $totalEvasoes, $prejuizoTotal, $totalMultasRecuperadas, $taxaEvasao)
    {
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
        $this->ofensores = $ofensores;
        $this->totalEvasoes = $totalEvasoes;
        $this->prejuizoTotal = $prejuizoTotal;
        $this->totalMultasRecuperadas = $totalMultasRecuperadas;
        $this->taxaEvasao = $taxaEvasao;
    }

    public function array(): array
    {
        $dados = [];
        
        // Cabeçalho resumo
        $dados[] = ['RESUMO', 'PERÍODO: ' . $this->dataInicio . ' a ' . $this->dataFim];
        $dados[] = [];
        $dados[] = ['Total de Faltas/Cancelamentos', $this->totalEvasoes];
        $dados[] = ['Prejuízo Estimado', 'R$ ' . number_format($this->prejuizoTotal, 2, ',', '.')];
        $dados[] = ['Multas Recuperadas', 'R$ ' . number_format($this->totalMultasRecuperadas, 2, ',', '.')];
        $dados[] = ['Prejuízo Líquido', 'R$ ' . number_format($this->prejuizoTotal - $this->totalMultasRecuperadas, 2, ',', '.')];
        $dados[] = ['Taxa de Evasão', number_format($this->taxaEvasao, 2, ',', '.') . '%'];
        $dados[] = [];
        $dados[] = ['CLIENTES COM MAIS FALTAS'];
        $dados[] = [];
        
        foreach ($this->ofensores as $ofensor) {
            $dados[] = [
                $ofensor->nome,
                $ofensor->telefone,
                $ofensor->total_falhas,
                'R$ ' . number_format($ofensor->prejuizo, 2, ',', '.'),
            ];
        }
        
        return $dados;
    }

    public function headings(): array
    {
        return ['Cliente/Descrição', 'Telefone/Valor', 'Total de Falhas', 'Prejuízo Estimado'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FF0000']]]],
        ];
    }
}
