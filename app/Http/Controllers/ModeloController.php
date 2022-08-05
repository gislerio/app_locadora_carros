<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{
    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json($this->modelo->all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');
        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $imagem_urn,
            'numero_prtas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'ari_bag' => $request->ari_bag,
            'abs' => $request->abs
        ]);

        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $modelo = $this->modelo->find($id);
        if ($modelo === null) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }

        return response()->json($modelo, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $modelo = $this->modelo->find($id);
        if ($modelo === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização.'], 404);
        }

        if ($request->method() == 'PATCH') {
            $regrasDinamicas = array();
            //percorredno todas as regras definidas no Model
            foreach ($modelo as $input => $regra) {
                //coletar apenas as regras aplicáveis
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);

        } else {
            $request->validate($modelo->rules());
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        //remove o arquivo antigo caso um novo arquivo tenha sido enviado no request
        if ($request->file('imagem')) {
            Storage::disk('public')->delete($modelo->imagem);
        }

        $modelo->update([
            'marca_id' => $request->marca_id,
            'nome' => $imagem_urn,
            'numero_prtas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'ari_bag' => $request->ari_bag,
            'abs' => $request->abs
        ]);
        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);

        if ($modelo === null) {
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe'], 404);
        }

        //remove o arquivo antigo
        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();
        return response()->json(['msg' => 'O modelo foi removida com sucesso'], 200);
    }
}
