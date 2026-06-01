<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AvaliacoesExport implements FromArray, WithHeadings, WithStyles
{
    protected $dataInicio;
    protected $dataFim;
    protected $avaliacoes;

    public function __construct($dataInicio, $dataFim, $avaliacoes)
    {
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
        $this->avaliacoes = $avaliacoes;
    }

    public function array(): array
    {
        $dados = [];
        
        foreach ($this->avaliacoes as $avaliacao) {
            $estrelas = str_repeat('★', $avaliacao->nota) . str_repeat('☆', 5 - $avaliacao->nota);
            $dados[] = [
                $avaliacao->cliente_nome,
                $avaliacao->profissional_nome,
                $avaliacao->nota,
                $estrelas,
                $avaliacao->comentario ?? '-',
                \Carbon\Carbon::parse($avaliacao->created_at)->format('d/m/Y H:i'),
            ];
        }
        
        return $dados;
    }

    public function headings(): array
    {
        return ['Cliente', 'Profissional', 'Nota', 'Estrelas', 'Comentário', 'Data'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFC000']]]],
        ];
    }
}
