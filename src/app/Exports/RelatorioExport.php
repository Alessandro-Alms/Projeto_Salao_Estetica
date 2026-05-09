<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RelatorioExport implements FromCollection, WithHeadings
{
    protected $dataInicio;
    protected $dataFim;

    public function __construct($dataInicio, $dataFim)
    {
        $this->dataInicio = $dataInicio . ' 00:00:00';
        $this->dataFim = $dataFim . ' 23:59:59';
    }

    public function collection()
    {
        // Exporta o ranking de profissionais para o Excel
        return DB::table('agendamentos')
            ->join('users', 'agendamentos.profissional_id', '=', 'users.id')
            ->select(
                'users.name as Profissional',
                DB::raw('COUNT(agendamentos.id_agendamento) as Total_Atendimentos'),
                DB::raw('SUM(agendamentos.valor_comissao) as Total_Gerado')
            )
            ->where('agendamentos.status', 'executado')
            ->whereBetween('agendamentos.updated_at', [$this->dataInicio, $this->dataFim])
            ->groupBy('users.id', 'users.name')
            ->orderByRaw('COUNT(agendamentos.id_agendamento) DESC')
            ->get();
    }

    public function headings(): array
    {
        return ['Profissional', 'Total de Atendimentos', 'Total Gerado (R$)'];
    }
}