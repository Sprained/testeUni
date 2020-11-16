<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SessionController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->input('email');
        $senha = $request->input('senha');

        $sql = "SELECT
                    u.id,
                    u.senha,
                    c.cargo
                FROM
                    usuarios as u
                    INNER JOIN cargos as c ON c.id = u.cargo_id 
                WHERE
                    email = '$email'";
        $usuario = DB::selectOne($sql);

        if(!$usuario) {
            return response()->json(['message' => 'Usuário não encontrado'], 400);
        }

        if(!(Hash::check($senha, $usuario->senha))){
            return response()->json(['message' => 'Usuário ou senha informado com erro'], 400);
        }

        $request->session()->put('user_id', $usuario->id);
        $request->session()->put('cargo', $usuario->cargo);
        return response()->json(['email' => $email, 'cargo' => $usuario->cargo]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->flush();
        
        return response()->json(['message' => 'Usuário deslogado com sucesso']);
    }
}
