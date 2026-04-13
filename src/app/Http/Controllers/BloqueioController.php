<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BloqueioHorario;
use App\Models\User;

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

    public function store(Request $request)
    {
        $validado = $request->validate([
            'profissional_id' => ['nullable', 'exists:users,id'],
            'data_hora_inicio' => ['required', 'date'],
            'data_hora_fim' => ['required', 'date', 'after:data_hora_inicio'],
            'motivo' => ['nullable', 'string', 'max:255'],
        ], [
            'data_hora_fim.after' => 'A data final deve ser depois da data de início.',
        ]);

        BloqueioHorario::create($validado);

        return redirect()->route('admin.bloqueios.index')->with('success', 'Bloqueio de agenda registrado com sucesso!');
    }

    public function destroy(BloqueioHorario $bloqueio)
    {
        $bloqueio->delete();
        return redirect()->route('admin.bloqueios.index')->with('success', 'Bloqueio removido com sucesso!');
    }
}