<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdutoStoreRequest;
use App\Http\Resources\ProdutoResource;
use App\Models\Produto;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ProdutoController extends Controller
{

    private Produto $produto;

    public function __construct(Produto $produto)
    {
        $this->produto = $produto;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produtos = $this->produto::paginate(10);

        return response()->json(ProdutoResource::collection($produtos), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function salvarProduto(ProdutoStoreRequest $request)
    {
        // $payload = $request->validated();

        return response()->json(['ok'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Produto $produto)
    {
        return response()->json([
            'produto' => new ProdutoResource($produto)
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produto $produto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto)
    {
        //
    }
}
