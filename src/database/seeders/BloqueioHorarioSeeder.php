<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BloqueioHorarioSeeder extends Seeder
{
    public function run(): void
    {
        $this->criarFeriadosGerais(2026);
        $this->criarFeriadosGerais(2027);
        $this->criarBloqueiosGeraisParaTeste();
        $this->criarFolgasProfissionais();
    }

    private function criarFeriadosGerais(int $ano): void
    {
        $feriados = array_merge(
            $this->feriadosFixos($ano),
            $this->feriadosMoveis($ano)
        );

        foreach ($feriados as $feriado) {
            $data = Carbon::createFromFormat('Y-m-d', $feriado['data']);

            $this->salvarBloqueio(
                null,
                $data->copy()->startOfDay(),
                $data->copy()->endOfDay(),
                $feriado['motivo']
            );
        }
    }

    private function feriadosFixos(int $ano): array
    {
        return [
            ['data' => "{$ano}-01-01", 'motivo' => 'Ano Novo'],
            ['data' => "{$ano}-04-21", 'motivo' => 'Tiradentes'],
            ['data' => "{$ano}-05-01", 'motivo' => 'Dia do Trabalho'],
            ['data' => "{$ano}-09-07", 'motivo' => 'Independencia do Brasil'],
            ['data' => "{$ano}-10-12", 'motivo' => 'Nossa Senhora Aparecida'],
            ['data' => "{$ano}-11-02", 'motivo' => 'Finados'],
            ['data' => "{$ano}-11-15", 'motivo' => 'Proclamacao da Republica'],
            ['data' => "{$ano}-11-20", 'motivo' => 'Consciencia Negra'],
            ['data' => "{$ano}-12-25", 'motivo' => 'Natal'],
        ];
    }

    private function feriadosMoveis(int $ano): array
    {
        $pascoa = $this->calcularPascoa($ano)->startOfDay();

        return [
            ['data' => $pascoa->copy()->subDays(48)->toDateString(), 'motivo' => 'Carnaval'],
            ['data' => $pascoa->copy()->subDays(47)->toDateString(), 'motivo' => 'Carnaval'],
            ['data' => $pascoa->copy()->subDays(2)->toDateString(), 'motivo' => 'Sexta-feira Santa'],
            ['data' => $pascoa->copy()->toDateString(), 'motivo' => 'Pascoa'],
            ['data' => $pascoa->copy()->addDays(60)->toDateString(), 'motivo' => 'Corpus Christi'],
        ];
    }

    private function calcularPascoa(int $ano): Carbon
    {
        $a = $ano % 19;
        $b = intdiv($ano, 100);
        $c = $ano % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $mes = intdiv($h + $l - 7 * $m + 114, 31);
        $dia = (($h + $l - 7 * $m + 114) % 31) + 1;

        return Carbon::create($ano, $mes, $dia, 0, 0, 0);
    }

    private function criarFolgasProfissionais(): void
    {
        $profissionais = DB::table('users')
            ->where('cargo', 'profissional')
            ->orderBy('id')
            ->limit(5)
            ->pluck('id');

        foreach ($profissionais as $index => $profissionalId) {
            $data = now()
                ->addDays(16 + ($index * 7))
                ->next(Carbon::MONDAY)
                ->startOfDay();

            if ($this->temAgendamentoNoDia((int) $profissionalId, $data)) {
                $data = $data->copy()->addWeek();
            }

            $this->salvarBloqueio(
                (int) $profissionalId,
                $data->copy()->startOfDay(),
                $data->copy()->endOfDay(),
                'Folga planejada pelo profissional'
            );
        }
    }

    private function criarBloqueiosGeraisParaTeste(): void
    {
        $bloqueios = [
            [
                'data' => now()->addDays(18)->startOfDay(),
                'motivo' => 'Feriado local - Teste 50%',
            ],
            [
                'data' => now()->addDays(28)->startOfDay(),
                'motivo' => 'Carnaval fora de epoca - Teste 75%',
            ],
        ];

        foreach ($bloqueios as $bloqueio) {
            $this->salvarBloqueio(
                null,
                $bloqueio['data']->copy()->startOfDay(),
                $bloqueio['data']->copy()->endOfDay(),
                $bloqueio['motivo']
            );
        }
    }

    private function salvarBloqueio(?int $profissionalId, Carbon $inicio, Carbon $fim, string $motivo): void
    {
        DB::table('bloqueios_horarios')->updateOrInsert(
            [
                'profissional_id' => $profissionalId,
                'data_hora_inicio' => $inicio,
                'data_hora_fim' => $fim,
                'motivo' => $motivo,
            ],
            [
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    private function temAgendamentoNoDia(int $profissionalId, Carbon $data): bool
    {
        return DB::table('agendamentos')
            ->where('profissional_id', $profissionalId)
            ->where('status', '!=', 'cancelado')
            ->where('data_hora_inicio', '<', $data->copy()->endOfDay())
            ->where('data_hora_fim', '>', $data->copy()->startOfDay())
            ->exists();
    }
}
