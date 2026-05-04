<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FidelizacaoExport implements FromArray, WithHeadings, WithStyles
{
    protected $dataInicio;
    protected $dataFim;
    protected $clientes;

    public function __construct($dataInicio, $dataFim, $clientes)
    {
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
        $this->clientes = $clientes;
    }

    public function array(): array
    {
        $dados = [];
        
        foreach ($this->clientes as $cliente) {
            $dados[] = [
                $cliente->name,
                $cliente->telefone,
                $cliente->total_visitas,
                'R$ ' . number_format($cliente->valor_gasto_total, 2, ',', '.'),
                $cliente->contador_fidelidade,
                \Carbon\Carbon::parse($cliente->ultima_visita)->format('d/m/Y'),
            ];
        }
        
        // Rodapé com totais
        $dados[] = [];
        $dados[] = [
            'TOTAIS',
            '',
            $this->clientes->sum('total_visitas'),
            'R$ ' . number_format($this->clientes->sum('valor_gasto_total'), 2, ',', '.'),
            '',
            '',
        ];
        
        return $dados;
    }

    public function headings(): array
    {
        return ['Cliente', 'Telefone', 'Total de Visitas', 'Total Gasto', 'Pontos Fidelidade', 'Última Visita'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '00B050']]]],
        ];
    }
}
