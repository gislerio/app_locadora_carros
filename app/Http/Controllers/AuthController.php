<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credenciais = $request->all(['email', 'password']);
        //autenticação (email e senha)
        $token = auth('api')->attempt($credenciais);
        if ($token) {
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['erro' => 'Usuário ou senha inválido!'], 403);

            //401 = Unauthorized -> não autorizado
            //403 = forbidden -> proibido (login inválido)
        }

        //retorno de um Json Web Token
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['msg' => 'Logout foi realizado com sucesso!']);;
    }
    
    
    public function refresh()
    {
        $token = auth('api')->refresh();
        return response()->json(['token' => $token]);
        
    }

    public function me()
    {
        return response()->json(auth()->user());
    }
}
