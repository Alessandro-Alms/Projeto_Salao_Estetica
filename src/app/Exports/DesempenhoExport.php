<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DesempenhoExport implements FromArray, WithHeadings, WithStyles
{
    protected $dataInicio;
    protected $dataFim;
    protected $profissionais;

    public function __construct($dataInicio, $dataFim, $profissionais)
    {
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
        $this->profissionais = $profissionais;
    }

    public function array(): array
    {
        $dados = [];
        
        foreach ($this->profissionais as $prof) {
            $dados[] = [
                $prof->name,
                $prof->total_servicos,
                'R$ ' . number_format($prof->receita_gerada, 2, ',', '.'),
                'R$ ' . number_format($prof->comissao_total, 2, ',', '.'),
                $prof->media_nota ? $prof->media_nota . ' ★ (' . $prof->total_avaliacoes . ')' : 'Sem avaliações',
            ];
        }
        
        // Rodapé com totais
        $dados[] = [];
        $dados[] = [
            'TOTAIS',
            $this->profissionais->sum('total_servicos'),
            'R$ ' . number_format($this->profissionais->sum('receita_gerada'), 2, ',', '.'),
            'R$ ' . number_format($this->profissionais->sum('comissao_total'), 2, ',', '.'),
            '',
        ];
        
        return $dados;
    }

    public function headings(): array
    {
        return ['Profissional', 'Serviços Realizados', 'Receita Gerada', 'Comissão Total', 'Avaliação Média'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '7B19E5']]]],
        ];
    }
}
