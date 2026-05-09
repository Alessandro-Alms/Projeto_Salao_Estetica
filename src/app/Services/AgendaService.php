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
    public const ACRESCIMO_FERIADO_ESPECIAL_PERCENTUAL = 75.00;
    public const ACRESCIMO_SAIDA_EXPEDIENTE_PERCENTUAL = 25.00;
    public const TOLERANCIA_SAIDA_EXPEDIENTE_MINUTOS = 30;

    private const TERMOS_FERIADO_ESPECIAL = [
        'ano novo',
        'reveillon',
        'carnaval',
        'natal',
        'pascoa',
        'feriado especial',
    ];

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

        if ($inicio->lt($this->inicioExpediente($escala, $inicio)) || $fim->gt($this->limiteSaidaExpediente($escala, $inicio))) {
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
        $almocoInicio = Carbon::parse($escala->almoco_inicio)->format('H:i:s');
        $almocoFim = Carbon::parse($escala->almoco_fim)->format('H:i:s');

        return $horaInicio < $almocoFim && $horaFim > $almocoInicio;
    }

    public function excedeSaidaExpediente(?HorarioTrabalho $escala, Carbon $inicio, Carbon $fim): bool
    {
        if (!$escala || !$escala->hora_fim) {
            return false;
        }

        $fimExpediente = $this->fimExpediente($escala, $inicio);

        return $fim->gt($fimExpediente) && $fim->lte($fimExpediente->copy()->addMinutes(self::TOLERANCIA_SAIDA_EXPEDIENTE_MINUTOS));
    }

    public function inicioExpediente(HorarioTrabalho $escala, Carbon $data): Carbon
    {
        return Carbon::parse($data->toDateString() . ' ' . $escala->hora_inicio);
    }

    public function fimExpediente(HorarioTrabalho $escala, Carbon $data): Carbon
    {
        return Carbon::parse($data->toDateString() . ' ' . $escala->hora_fim);
    }

    public function limiteSaidaExpediente(HorarioTrabalho $escala, Carbon $data): Carbon
    {
        return $this->fimExpediente($escala, $data)->addMinutes(self::TOLERANCIA_SAIDA_EXPEDIENTE_MINUTOS);
    }

    public function calcularAtendimentoEspecial(float $valorBase, bool $invadeAlmoco, ?BloqueioHorario $bloqueioGeral, bool $excedeSaidaExpediente = false): array
    {
        $motivos = [];
        $percentual = 0.00;

        if ($invadeAlmoco) {
            $percentualAlmoco = self::ACRESCIMO_ATENDIMENTO_ESPECIAL_PERCENTUAL;
            $motivos[] = 'Horario de almoco +' . $percentualAlmoco . '%';
            $percentual += $percentualAlmoco;
        }

        if ($bloqueioGeral) {
            $motivoBloqueio = $bloqueioGeral->motivo ?: 'Feriado/Bloqueio geral';
            $percentualFeriado = $this->ehFeriadoEspecial($motivoBloqueio)
                ? self::ACRESCIMO_FERIADO_ESPECIAL_PERCENTUAL
                : self::ACRESCIMO_ATENDIMENTO_ESPECIAL_PERCENTUAL;

            $motivos[] = $motivoBloqueio . ' +' . $percentualFeriado . '%';
            $percentual += $percentualFeriado;
        }

        if ($excedeSaidaExpediente) {
            $percentualSaida = self::ACRESCIMO_SAIDA_EXPEDIENTE_PERCENTUAL;
            $motivos[] = 'Saida do expediente +' . $percentualSaida . '%';
            $percentual += $percentualSaida;
        }

        if (empty($motivos)) {
            return [
                'valor_base' => $valorBase,
                'acrescimo_especial' => 0.00,
                'motivo_acrescimo' => null,
                'valor_total' => $valorBase,
            ];
        }

        $acrescimo = round($valorBase * ($percentual / 100), 2);

        return [
            'valor_base' => $valorBase,
            'acrescimo_especial' => $acrescimo,
            'motivo_acrescimo' => implode(' + ', $motivos),
            'valor_total' => $valorBase + $acrescimo,
        ];
    }

    public function percentualAtendimentoEspecial(bool $invadeAlmoco, ?BloqueioHorario $bloqueioGeral, bool $excedeSaidaExpediente = false): float
    {
        $dados = $this->calcularAtendimentoEspecial(100.00, $invadeAlmoco, $bloqueioGeral, $excedeSaidaExpediente);

        return (float) $dados['acrescimo_especial'];
    }

    private function ehFeriadoEspecial(string $motivo): bool
    {
        $motivoNormalizado = function_exists('mb_strtolower') ? mb_strtolower($motivo) : strtolower($motivo);
        $motivoNormalizado = strtr($motivoNormalizado, [
            'á' => 'a',
            'à' => 'a',
            'ã' => 'a',
            'â' => 'a',
            'é' => 'e',
            'ê' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ú' => 'u',
            'ç' => 'c',
        ]);

        foreach (self::TERMOS_FERIADO_ESPECIAL as $termo) {
            if (str_contains($motivoNormalizado, $termo)) {
                return true;
            }
        }

        return false;
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
