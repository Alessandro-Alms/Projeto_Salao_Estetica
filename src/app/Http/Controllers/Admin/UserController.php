<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $consulta = User::query();
        $usuarioLogado = auth()->user();
        $quantidadePorPagina = 10;

        if ($request->filled('cargo')) {
            $consulta->where('cargo', $request->cargo);
        }

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $consulta->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('cpf', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($usuarioLogado->cargo === 'gerente') {
            $consulta->whereIn('cargo', ['gerente', 'recepcionista', 'profissional', 'cliente']);
        } 
        elseif ($usuarioLogado->cargo === 'recepcionista') {
            $consulta->where('cargo', 'cliente');
        } 
        else {
            return redirect()->route('dashboard')->with('error', 'Acesso negado.');
        }

        $usuarios = $consulta->orderBy('name', 'asc')->paginate($quantidadePorPagina);
        return view('admin.usuarios.index', compact('usuarios'));
    }

        public function create()
        {
        return view('admin.usuarios.criar');
    }

    public function store(Request $request)
    {
        $request->merge([
            'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
            'telefone' => preg_replace('/[^0-9]/', '', $request->telefone),
        ]);
        $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
        'cpf' => ['required', 'string', 'size:11', 'regex:/^[0-9]+$/', 'unique:users,cpf'],
        'telefone' => ['required', 'string', 'size:11'],
        'cargo' => ['required'],
        'password' => ['required', 'min:8'],
        'd_nasc' => ['nullable', 'date'],   
        'endereco' => ['nullable', 'string', 'max:255'],
        ], 
        [

        'cpf.size' => 'O CPF deve ter exatamente 11 números (você enviou ' . strlen($request->cpf) . ').',
        'cpf.unique' => 'Este CPF já está cadastrado.',
        'email.unique' => 'Este e-mail já está em uso.',
        'telefone.size' => 'O telefone deve ter exatamente 11 números (você enviou ' . strlen($request->telefone) . ').',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'telefone' => $request->telefone,
            'cargo' => auth()->user()->cargo === 'recepcionista' ? 'cliente' : $request->cargo,
            'password' => Hash::make($request->password),
            'd_nasc' => $request->d_nasc,
            'endereco' => $request->endereco,
        ]);

        return redirect()->route('admin.usuarios.index')->with('status', 'Usuário cadastrado com sucesso!');
    }

    public function edit(User $usuario)
    {
        if (auth()->user()->cargo === 'recepcionista' && $usuario->cargo !== 'cliente') {
        abort(403, 'Você só tem permissão para editar clientes.');
    }
    $servicos = \App\Models\Servico::All();
    return view('admin.usuarios.editar', compact('usuario', 'servicos'));
    }

    public function update(Request $request, $id) 
    {

        $usuario = User::findOrFail($id);
        $request->merge([
            'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
            'telefone' => preg_replace('/[^0-9]/', '', $request->telefone),
        ]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$id],
            'cpf' => ['required', 'string', 'size:11', 'unique:users,cpf,'.$id],
            'telefone' => ['required', 'string', 'size:11   '],
            'cargo' => ['required', 'in:gerente,recepcionista,profissional,cliente'],
            'password' => ['nullable', 'min:8'],
            'd_nasc' => ['nullable', 'date'],
            'endereco' => ['nullable', 'string', 'max:255'],
        ]);

        $usuario->update($request->only('name', 'email', 'cpf', 'telefone', 'cargo', 'd_nasc', 'endereco'));

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
            $usuario->save();
        }
        if ($usuario->cargo === 'profissional' && $request->has('servicos')) {
            $syncData = [];
            foreach ($request->servicos as $servicoId => $dados) {
                if (isset($dados['ativo'])){
                    $syncData[$servicoId] = [
                        'comissao_percentual' => $dados['comissao'] ?? 50.00,
                        'duracao_customizada' => $dados['duracao'] ?? null,
                    ];
                }
            }
            $usuario->servicos()->sync($syncData);
        }
        return redirect()->route('admin.usuarios.index')->with('status', 'Usuário atualizado com sucesso!');
        
    }

    public function destroy(User $usuario)
    {

        if ($usuario->id === auth()->id()) {
            return redirect()->route('admin.usuarios.index')->with('error', 'Você não pode deletar sua própria conta!');
        }

        $usuario->delete();
        return redirect()->route('admin.usuarios.index')->with('status', 'Usuário removido!');
    }
}