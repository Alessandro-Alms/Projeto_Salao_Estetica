<?php

namespace App\Services;

use App\Models\Agendamento;
use App\Models\BloqueioHorario;
use App\Models\HorarioTrabalho;
use App\Models\User;
use Carbon\Carbon;

class AgendaService
{
    public const ACRESCIMO_ATENDIMENTO_ESPECIAL_PERCENTUAL = 50.00;

    public function buscarEscala(int $profissionalId, int $diaSemana): ?HorarioTrabalho
    {
        return HorarioTrabalho::where('profissional_id', $profissionalId)
            ->where('dia_semana', $diaSemana)
            ->first();
    }

    public function validarExpediente(User $profissional, Carbon $inicio, Carbon $fim): ?string
    {
        $escala = $this->buscarEscala($profissional->id, $inicio->dayOfWeek);

        if (!$escala || !$escala->trabalha) {
            return 'O profissional nao trabalha neste dia da semana.';
        }

        $horaInicio = $inicio->format('H:i:s');
        $horaFim = $fim->format('H:i:s');

        if ($horaInicio < $escala->hora_inicio || $horaFim > $escala->hora_fim) {
            return 'O horario escolhido esta fora do expediente do profissional.';
        }

        return null;
    }

    public function buscarBloqueioConflitante(int $profissionalId, Carbon $inicio, Carbon $fim): ?BloqueioHorario
    {
        return $this->buscarBloqueioProfissionalConflitante($profissionalId, $inicio, $fim)
            ?? $this->buscarBloqueioGeralConflitante($inicio, $fim);
    }

    public function buscarBloqueioProfissionalConflitante(int $profissionalId, Carbon $inicio, Carbon $fim): ?BloqueioHorario
    {
        return BloqueioHorario::where('profissional_id', $profissionalId)
            ->where('data_hora_inicio', '<', $fim)
            ->where('data_hora_fim', '>', $inicio)
            ->first();
    }

    public function buscarBloqueioGeralConflitante(Carbon $inicio, Carbon $fim): ?BloqueioHorario
    {
        return BloqueioHorario::whereNull('profissional_id')
            ->where('data_hora_inicio', '<', $fim)
            ->where('data_hora_fim', '>', $inicio)
            ->first();
    }

    public function invadeAlmoco(?HorarioTrabalho $escala, Carbon $inicio, Carbon $fim): bool
    {
        if (!$escala || !$escala->almoco_inicio || !$escala->almoco_fim) {
            return false;
        }

        $horaInicio = $inicio->format('H:i:s');
        $horaFim = $fim->format('H:i:s');

        return $horaInicio < $escala->almoco_fim && $horaFim > $escala->almoco_inicio;
    }

    public function calcularAtendimentoEspecial(float $valorBase, bool $invadeAlmoco, ?BloqueioHorario $bloqueioGeral): array
    {
        $motivos = [];

        if ($invadeAlmoco) {
            $motivos[] = 'Horario de almoco';
        }

        if ($bloqueioGeral) {
            $motivos[] = $bloqueioGeral->motivo ?: 'Feriado/Bloqueio geral';
        }

        if (empty($motivos)) {
            return [
                'valor_base' => $valorBase,
                'acrescimo_especial' => 0.00,
                'motivo_acrescimo' => null,
                'valor_total' => $valorBase,
            ];
        }

        $acrescimo = round($valorBase * (self::ACRESCIMO_ATENDIMENTO_ESPECIAL_PERCENTUAL / 100), 2);

        return [
            'valor_base' => $valorBase,
            'acrescimo_especial' => $acrescimo,
            'motivo_acrescimo' => implode(' + ', $motivos),
            'valor_total' => $valorBase + $acrescimo,
        ];
    }

    public function existeConflitoAgendamento(int $profissionalId, Carbon $inicio, Carbon $fim): bool
    {
        return Agendamento::where('profissional_id', $profissionalId)
            ->where('status', '!=', 'cancelado')
            ->where('data_hora_inicio', '<', $fim)
            ->where('data_hora_fim', '>', $inicio)
            ->exists();
    }
}
