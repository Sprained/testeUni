<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use stdClass;

class UsuarioController extends Controller
{
    public function insert(Request $request)
    {
        $nome = $request->input('nome');
        $senha = Hash::make($request->input('senha'));
        $email = $request->input('email');

        $matricula = 2020 . rand(11111, 99999);

        $sql = "SELECT
                    id 
                FROM
                    usuarios 
                WHERE
                    email = '$email'";
        $usuario = DB::selectOne($sql);

        if ($usuario) {
            return response()->json(['message' => 'Usuário já cadastrado!'], 409);
        }

        $sql = "INSERT INTO 
                    usuarios ( nome, senha, matricula, email, cargo_id )
                VALUES
                    ( '$nome', '$senha', $matricula, '$email', 2 )";
        DB::insert($sql);

        return response()->json(['message' => 'Usuário cadastrado com sucesso!']);
    }

    public function delete($id)
    {
        $sql = "DELETE 
                FROM
                    usuarios 
                WHERE
                    id = $id";
        DB::delete($sql);

        return response()->json(['message' => 'Usuário deletado com sucesso!']);
    }

    public function update(Request $request, $id)
    {
        $nome = $request->input('nome');
        $senha = Hash::make($request->input('senha'));
        $email = $request->input('email');

        if ($request->session()->get('cargo') == 'Administrador') {
            $sql = "SELECT
                        id,
                        nome,
                        email
                    FROM
                        usuarios 
                    WHERE
                        id = '$id'";
            $usuario = DB::selectOne($sql);
        } else {
            $userId = $request->session()->get('user_id');
            $sql = "SELECT
                        id,
                        nome,
                        email
                    FROM
                        usuarios 
                    WHERE
                        id = '$userId'";
            $usuario = DB::selectOne($sql);
        }


        if(!$nome) {
            $nome = $usuario->nome;
        }

        if(!$email) {
            $email = $usuario->email;
        }

        if($senha) {
            $sql = "UPDATE 
                        usuarios 
                    SET 
                        nome = '$nome',
                        senha = '$senha',
                        email = '$email' 
                    WHERE
                        id = $id";
            DB::update($sql);
        } else {
            $sql = "UPDATE 
                        usuarios 
                    SET 
                        nome = '$nome',
                        email = '$email' 
                    WHERE
                        id = $id";
            DB::update($sql);
        }


        return response()->json(['message' => 'Usuário atualizado com sucesso!']);
    }

    public function avatarUpload(Request $request)
    {
        $image = $request->file('image');

        $userId = $request->session()->get('user_id');
        $sql = "SELECT
                    id 
                FROM
                    imagens 
                WHERE
                    usuario_id = $userId";
        $imagem = DB::selectOne($sql);

        $filename = time() . '-' . $image->getClientOriginalName();
        $image->storeAs('imagens', $filename);
        $userId = $request->session()->get('user_id');

        if($imagem) {
            $sql = "UPDATE 
                        imagens 
                    SET 
                        path = '$filename'
                    WHERE
                        id = $imagem->id";
            DB::update($sql);
        } else {
            $sql = "INSERT INTO 
                        imagens ( path, usuario_id )
                    VALUES
                        ( '$filename', $userId )";
            DB::insert($sql);
        }

        return response()->json(['message' => 'Upload do avatar realizado com sucesso']);
    }

    public function select(Request $request)
    {
        $userId = $request->session()->get('user_id');
        $sql = "SELECT
                    u.id,
                    u.nome,
                    u.matricula,
                    u.email,
                    (SELECT i.path FROM imagens i WHERE u.id = i.usuario_id) AS path
                FROM
                    usuarios AS u
                WHERE
                    u.id=$userId";
        $user = DB::selectOne($sql);

        if($user->path){
            return response()->json(['id' => $user->id, 'nome' => $user->nome, 'matricula' => $user->matricula, 'url' => "https://tdev3.grupounibra.com/storage/imgs/$user->path"]);
        }
        
        return response()->json(['id' => $user->id, 'nome' => $user->nome, 'matricula' => $user->matricula]);
    }

    public function resetPassword(Request $request)
    {
        $email = $request->input('email');

        $sql = "SELECT
                    id,
                    nome,
                    email
                FROM
                    usuarios 
                WHERE
                    email = '$email'";
        $usuario = DB::selectOne($sql);

        if(!$usuario){
            return response()->json(['message' => 'Usuário não cadastrado'], 400);
        }

        $senha = rand(111111, 999999);
        $hasSenha = Hash::make($senha);

        $sql = "UPDATE 
                    usuarios 
                SET 
                    senha = '$hasSenha'
                WHERE
                    id = $usuario->id";
        DB::update($sql);

        $user = new stdClass();
        $user->nome = $usuario->nome;
        $user->email = $usuario->email;
        $user->senha = $senha;

        Mail::send(new ResetPassord($user));

        return response()->json(['message' => 'Email enviado']);
    }
}
