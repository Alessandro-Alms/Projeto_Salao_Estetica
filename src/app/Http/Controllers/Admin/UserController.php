<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {


        $usuarios = User::orderBy('name', 'asc')->paginate(10);
    
        $usuarioLogado = auth()->user();

        $quantidadePorPagina = 10;

    if ($usuarioLogado->cargo === 'gerente') {
        $usuarios = User::paginate($quantidadePorPagina);
    } 
    elseif ($usuarioLogado->cargo === 'recepcionista') {
        $usuarios = User::where('cargo', 'cliente')->paginate($quantidadePorPagina);
    } 
    else {
        return redirect()->route('dashboard')->with('error', 'Acesso negado.');
    }

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
        ], 
        [

        'cpf.size' => 'O CPF deve ter exatamente 11 números (você enviou ' . strlen($request->cpf) . ').',
        'cpf.unique' => 'Este CPF já está cadastrado.',
        'email.unique' => 'Este e-mail já está em uso.',
        'telefone.size' => 'O telefone deve ter exatamente 11 números (você enviou ' . strlen($request->telefone) . ').',
        ]);

        $dados = $request->all();

        if (auth()->user()->cargo !== 'gerente') {
            $dados['cargo'] = 'cliente';
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'telefone' => $request->telefone,
            'cargo' => auth()->user()->cargo === 'recepcionista' ? 'cliente' : $request->cargo,
            'password' => Hash::make($dados['password']),
        ]);

        return redirect()->route('admin.usuarios.index')->with('status', 'Usuário cadastrado com sucesso!');
    }

    public function edit(User $user)
    {
        if (auth()->user()->cargo === 'recepcionista' && $user->cargo !== 'cliente') {
        abort(403, 'Você só tem permissão para editar clientes.');
    }
        $usuario = $user;
        return view('admin.usuarios.editar', compact('usuario'));
    }

    public function update(Request $request, $id) 
    {

        $user = User::findOrFail($id);
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
        ]);

        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'telefone' => $request->telefone,
            'cargo' => $request->cargo,
        ]);


        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.usuarios.index')->with('status', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.usuarios.index')->with('error', 'Você não pode deletar sua própria conta!');
        }

        $user->delete();
        return redirect()->route('admin.usuarios.index')->with('status', 'Usuário removido!');
    }
    public function clientes(Request $request)
    {
        $query = User::where('cargo', 'cliente');

            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
                });
            }

        $clientes = $query->orderBy('name', 'asc')->paginate(10);
        return view('admin.usuarios.clientes', compact('clientes'));
    }
    public function profissionais(Request $request)
    {
        $query = User::where('cargo', 'profissional');

            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
                });
            }

        $profissionais = $query->orderBy('name', 'asc')->paginate(10);
        return view('admin.usuarios.profissionais', compact('profissionais'));
    }
}