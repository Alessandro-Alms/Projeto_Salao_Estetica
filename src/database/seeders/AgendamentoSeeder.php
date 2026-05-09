<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgendamentoSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = DB::table('users')->where('cargo', 'cliente')->orderBy('id')->pluck('id')->values();
        $profissionais = DB::table('users')->where('cargo', 'profissional')->orderBy('id')->pluck('id')->values();
        $servicos = DB::table('servicos')->orderBy('id_servico')->get()->values();

        if ($clientes->isEmpty() || $profissionais->isEmpty() || $servicos->isEmpty()) {
            return;
        }

        $statusPorAgenda = [
            'executado',
            'executado',
            'executado',
            'confirmado',
            'confirmado',
            'presente',
            'cancelado',
            'falta',
            'confirmado',
            'executado',
        ];

        foreach ($statusPorAgenda as $index => $status) {
            $servico = $servicos[$index % $servicos->count()];
            $inicio = $this->dataParaStatus($status, $index);
            $fim = $inicio->copy()->addMinutes((int) $servico->duracao);
            $valorComissao = $status === 'executado' ? round(((float) $servico->preco) * 0.50, 2) : null;

            $agendamentoId = DB::table('agendamentos')->insertGetId([
                'cliente_id' => $clientes[$index % $clientes->count()],
                'profissional_id' => $profissionais[$index % $profissionais->count()],
                'servico_id' => $servico->id_servico,
                'data_hora_inicio' => $inicio,
                'data_hora_fim' => $fim,
                'valor_total' => $status === 'cancelado' ? (float) $servico->preco : (float) $servico->preco,
                'valor_base' => (float) $servico->preco,
                'acrescimo_especial' => 0,
                'motivo_acrescimo' => null,
                'valor_comissao' => $valorComissao,
                'comissao_paga_percentual' => $valorComissao ? 50.00 : null,
                'multa_valor' => $status === 'cancelado' ? round(((float) $servico->preco) * 0.05, 2) : 0,
                'status' => $status,
                'obs' => $this->observacaoParaStatus($status),
                'created_at' => $inicio,
                'updated_at' => $inicio,
            ], 'id_agendamento');

            DB::table('agendamento_servico')->insert([
                'agendamento_id' => $agendamentoId,
                'servico_id' => $servico->id_servico,
                'duracao' => $servico->duracao,
                'preco' => $servico->preco,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function dataParaStatus(string $status, int $index): Carbon
    {
        if (in_array($status, ['executado', 'cancelado', 'falta'], true)) {
            return Carbon::now()
                ->subDays(10 - $index)
                ->setTime(9 + ($index % 5), 0, 0);
        }

        return Carbon::now()
            ->addDays($index + 1)
            ->setTime(9 + ($index % 5), 0, 0);
    }

    private function observacaoParaStatus(string $status): ?string
    {
        return match ($status) {
            'executado' => 'Atendimento realizado para testes.',
            'cancelado' => 'Cancelamento de exemplo com multa.',
            'falta' => 'Falta registrada para teste.',
            'presente' => 'Cliente presente aguardando finalizacao.',
            default => null,
        };
    }
}
