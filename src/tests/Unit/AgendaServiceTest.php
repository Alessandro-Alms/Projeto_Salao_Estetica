<?php

namespace Tests\Unit;

use App\Models\BloqueioHorario;
use App\Models\HorarioTrabalho;
use App\Services\AgendaService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class AgendaServiceTest extends TestCase
{
    public function test_calcula_acrescimo_de_almoco_com_cinquenta_por_cento(): void
    {
        $resultado = (new AgendaService())->calcularAtendimentoEspecial(100.00, true, null);

        $this->assertSame(50.00, $resultado['acrescimo_especial']);
        $this->assertSame(150.00, $resultado['valor_total']);
        $this->assertSame('Horario de almoco +50%', $resultado['motivo_acrescimo']);
    }

    public function test_horario_de_almoco_padrao_vai_de_onze_as_treze(): void
    {
        $escala = new HorarioTrabalho([
            'almoco_inicio' => '11:00',
            'almoco_fim' => '13:00',
        ]);

        $agendaService = new AgendaService();

        $this->assertTrue($agendaService->invadeAlmoco($escala, Carbon::parse('2026-05-26 11:00'), Carbon::parse('2026-05-26 12:00')));
        $this->assertTrue($agendaService->invadeAlmoco($escala, Carbon::parse('2026-05-26 12:30'), Carbon::parse('2026-05-26 13:00')));
        $this->assertFalse($agendaService->invadeAlmoco($escala, Carbon::parse('2026-05-26 10:00'), Carbon::parse('2026-05-26 11:00')));
        $this->assertFalse($agendaService->invadeAlmoco($escala, Carbon::parse('2026-05-26 13:00'), Carbon::parse('2026-05-26 14:00')));
    }

    public function test_saida_do_expediente_permite_ate_trinta_minutos_com_acrescimo(): void
    {
        $escala = new HorarioTrabalho([
            'hora_inicio' => '08:00',
            'hora_fim' => '18:00',
        ]);

        $agendaService = new AgendaService();

        $this->assertTrue($agendaService->excedeSaidaExpediente($escala, Carbon::parse('2026-05-26 18:00'), Carbon::parse('2026-05-26 18:30')));
        $this->assertFalse($agendaService->excedeSaidaExpediente($escala, Carbon::parse('2026-05-26 17:00'), Carbon::parse('2026-05-26 18:00')));
        $this->assertFalse($agendaService->excedeSaidaExpediente($escala, Carbon::parse('2026-05-26 18:00'), Carbon::parse('2026-05-26 18:31')));
    }

    public function test_calcula_saida_do_expediente_com_vinte_e_cinco_por_cento(): void
    {
        $resultado = (new AgendaService())->calcularAtendimentoEspecial(100.00, false, null, true);

        $this->assertSame(25.00, $resultado['acrescimo_especial']);
        $this->assertSame(125.00, $resultado['valor_total']);
        $this->assertSame('Saida do expediente +25%', $resultado['motivo_acrescimo']);
    }

    public function test_calcula_fim_de_semana_com_vinte_e_cinco_por_cento(): void
    {
        $resultado = (new AgendaService())->calcularAtendimentoEspecial(
            100.00,
            false,
            null,
            false,
            Carbon::parse('2026-05-30 10:00') // Sabado
        );

        $this->assertSame(25.00, $resultado['acrescimo_especial']);
        $this->assertSame(125.00, $resultado['valor_total']);
        $this->assertSame('Fim de semana +25%', $resultado['motivo_acrescimo']);
    }

    public function test_calcula_feriado_comum_com_cinquenta_por_cento(): void
    {
        $bloqueio = new BloqueioHorario(['motivo' => 'Feriado municipal']);

        $resultado = (new AgendaService())->calcularAtendimentoEspecial(100.00, false, $bloqueio);

        $this->assertSame(50.00, $resultado['acrescimo_especial']);
        $this->assertSame(150.00, $resultado['valor_total']);
        $this->assertSame('Feriado municipal +50%', $resultado['motivo_acrescimo']);
    }

    public function test_calcula_feriado_especial_com_setenta_e_cinco_por_cento(): void
    {
        $bloqueio = new BloqueioHorario(['motivo' => 'Carnaval']);

        $resultado = (new AgendaService())->calcularAtendimentoEspecial(100.00, false, $bloqueio);

        $this->assertSame(75.00, $resultado['acrescimo_especial']);
        $this->assertSame(175.00, $resultado['valor_total']);
        $this->assertSame('Carnaval +75%', $resultado['motivo_acrescimo']);
    }

    public function test_soma_acrescimos_quando_almoco_e_feriado_comum_coincidem(): void
    {
        $bloqueio = new BloqueioHorario(['motivo' => 'Feriado municipal']);

        $resultado = (new AgendaService())->calcularAtendimentoEspecial(100.00, true, $bloqueio);

        $this->assertSame(100.00, $resultado['acrescimo_especial']);
        $this->assertSame(200.00, $resultado['valor_total']);
        $this->assertSame('Horario de almoco +50% + Feriado municipal +50%', $resultado['motivo_acrescimo']);
    }

    public function test_soma_acrescimos_quando_almoco_e_feriado_especial_coincidem(): void
    {
        $bloqueio = new BloqueioHorario(['motivo' => 'Ano Novo']);

        $resultado = (new AgendaService())->calcularAtendimentoEspecial(100.00, true, $bloqueio);

        $this->assertSame(125.00, $resultado['acrescimo_especial']);
        $this->assertSame(225.00, $resultado['valor_total']);
        $this->assertSame('Horario de almoco +50% + Ano Novo +75%', $resultado['motivo_acrescimo']);
    }

    public function test_soma_saida_do_expediente_com_outros_acrescimos(): void
    {
        $bloqueio = new BloqueioHorario(['motivo' => 'Feriado municipal']);

        $resultado = (new AgendaService())->calcularAtendimentoEspecial(100.00, true, $bloqueio, true);

        $this->assertSame(125.00, $resultado['acrescimo_especial']);
        $this->assertSame(225.00, $resultado['valor_total']);
        $this->assertSame('Horario de almoco +50% + Feriado municipal +50% + Saida do expediente +25%', $resultado['motivo_acrescimo']);
    }

    public function test_combo_de_cinco_servicos_desconta_total_sem_reduzir_base_de_comissao(): void
    {
        $resultado = (new AgendaService())->calcularAtendimentoEspecial(200.00, false, null, false, null, 5);

        $this->assertSame(20.00, $resultado['desconto_servicos']);
        $this->assertSame(200.00, $resultado['base_comissao']);
        $this->assertSame(180.00, $resultado['valor_total']);
        $this->assertSame('Combo de 5 serviços -10%', $resultado['motivo_desconto']);
    }

    public function test_combo_de_cinco_servicos_desconta_depois_do_acrescimo(): void
    {
        $resultado = (new AgendaService())->calcularAtendimentoEspecial(
            200.00,
            false,
            new BloqueioHorario(['motivo' => 'Feriado municipal']),
            false,
            null,
            5
        );

        $this->assertSame(100.00, $resultado['acrescimo_especial']);
        $this->assertSame(300.00, $resultado['base_comissao']);
        $this->assertSame(30.00, $resultado['desconto_servicos']);
        $this->assertSame(270.00, $resultado['valor_total']);
    }
}
