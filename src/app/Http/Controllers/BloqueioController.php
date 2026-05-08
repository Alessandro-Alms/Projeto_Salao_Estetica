<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BloqueioHorario;
use App\Models\User;
use App\Services\AgendaService;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class BloqueioController extends Controller
{
    public function index()
    {
        // Traz os bloqueios e o nome do profissional (se tiver)
        $bloqueios = BloqueioHorario::with('profissional')->orderBy('data_hora_inicio', 'desc')->get();
        return view('admin.bloqueios.index', compact('bloqueios'));
    }

    public function create()
    {
        // Traz apenas os profissionais para popular o campo "Select" no formulário
        $profissionais = User::where('cargo', 'profissional')->get();
        return view('admin.bloqueios.criar', compact('profissionais'));
    }

    public function store(Request $request, AgendaService $agendaService)
    {
        $validado = $request->validate([
            'profissional_id' => ['nullable', Rule::exists('users', 'id')->where('cargo', 'profissional')],
            'data_hora_inicio' => ['required', 'date'],
            'data_hora_fim' => ['required', 'date', 'after:data_hora_inicio'],
            'motivo' => ['nullable', 'string', 'max:255'],
        ], [
            'data_hora_fim.after' => 'A data final deve ser depois da data de início.',
        ]);

        $inicio = Carbon::parse($validado['data_hora_inicio']);
        $fim = Carbon::parse($validado['data_hora_fim']);

        if (
            !empty($validado['profissional_id'])
            && $agendaService->existeConflitoAgendamento((int) $validado['profissional_id'], $inicio, $fim)
        ) {
            return back()->withErrors([
                'data_hora_inicio' => 'Existe agendamento ativo nesse período. Reagende ou cancele os horários antes de criar o bloqueio.'
            ])->withInput();
        }

        BloqueioHorario::create($validado);

        return redirect()->route('admin.bloqueios.index')->with('success', 'Bloqueio de agenda registrado com sucesso!');
    }

    public function destroy(BloqueioHorario $bloqueio)
    {
        $bloqueio->delete();
        return redirect()->route('admin.bloqueios.index')->with('success', 'Bloqueio removido com sucesso!');
    }
}
