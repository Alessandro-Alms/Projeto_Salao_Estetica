<?php

namespace App\Services;

use App\Models\Agendamento;
use App\Models\BloqueioHorario;
use App\Models\HorarioTrabalho;
use App\Models\User;
use Carbon\Carbon;

class AgendaService
{
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
            return 'O profissional não trabalha neste dia da semana.';
        }

        $horaInicio = $inicio->format('H:i:s');
        $horaFim = $fim->format('H:i:s');

        if ($horaInicio < $escala->hora_inicio || $horaFim > $escala->hora_fim) {
            return 'O horário escolhido está fora do expediente do profissional.';
        }

        if ($escala->almoco_inicio && $escala->almoco_fim && $horaInicio < $escala->almoco_fim && $horaFim > $escala->almoco_inicio) {
            return 'Este horário coincide ou invade o intervalo de almoço do profissional.';
        }

        return null;
    }

    public function buscarBloqueioConflitante(int $profissionalId, Carbon $inicio, Carbon $fim): ?BloqueioHorario
    {
        return BloqueioHorario::where(function ($query) use ($profissionalId) {
            $query->whereNull('profissional_id')
                ->orWhere('profissional_id', $profissionalId);
        })
            ->where('data_hora_inicio', '<', $fim)
            ->where('data_hora_fim', '>', $inicio)
            ->first();
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
