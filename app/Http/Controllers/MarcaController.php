<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Marca;
use App\Http\Requests\{StoreMarcaRequest, UpdateMarcaRequest};
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    protected $marca;

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $marcaRepository = new MarcaRepository($this->marca);

        if($request->has('atributos_modelos')) {
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        } else {
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if($request->has('filtro')) {
            $marcaRepository->filtro($request->filtro);
        }

        if($request->has('atributos')) {
            $marcaRepository->selectAtributos($request->atributos);
        } 

        return response()->json($marcaRepository->getResultadoPaginado(3), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMarcaRequest $request)
    {
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
        ]);
        
        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id) // Removido o Marca $marca
    {
        $marca = $this->marca->with('modelos')->find($id);

        if($marca === null) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }

        return response()->json($marca, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMarcaRequest $request, $id) // Removido o Marca $marca
    {       
        $marca = $this->marca->find($id);
        
        if($marca === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe.'], 404);  
        }
        
        if($request->file('imagem')) {
            Storage::disk('public')->delete($marca->imagem);
            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens/logos', 'public');
        }

        $marca->fill($request->all());
        $request->file('imagem') ? $marca->imagem = $imagem_urn : '';
        $marca->save();

        // $marca->update([
        //     'nome' => $request->nome,
        //     'imagem' => $imagem_urn,
        // ]);

        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) // Removido o Marca $marca
    {
        $marca = $this->marca->find($id);

        if($marca === null) {
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe.'], 404);  
        }

        Storage::disk('public')->delete($marca->imagem);

        $marca->delete();
        return response()->json(['msg', 'A marca foi removida com sucesso!'], 200);
    }
}
