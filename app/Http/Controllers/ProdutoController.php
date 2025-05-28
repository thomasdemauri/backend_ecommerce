<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Produto;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\ProdutoResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProdutoStoreRequest;
use App\Services\ProdutoService;
use Symfony\Component\HttpFoundation\Response;

class ProdutoController extends Controller
{

    private ProdutoService $produto;

    public function __construct(ProdutoService $produto)
    {
        $this->produto = $produto;
    }


    public function index()
    {
        
        $produtos = $this->produto->paginate(10); // Ou $this->produto->allPaginated(10)
    
        return response()->json([
            'data' => ProdutoResource::collection($produtos),
            'meta' => [
                'current_page' => $produtos->currentPage(),
                'per_page' => $produtos->perPage(),
                'total' => $produtos->total(),
                'last_page' => $produtos->lastPage(),
            ],
            'links' => [
                'first' => $produtos->url(1),
                'last' => $produtos->url($produtos->lastPage()),
                'prev' => $produtos->previousPageUrl(),
                'next' => $produtos->nextPageUrl(),
            ]
        ], Response::HTTP_OK);

        return response()->json(ProdutoResource::collection(Produto::paginate(10)), Response::HTTP_OK);
    }


    public function store(ProdutoStoreRequest $request)
    {
        $payload = $request->validated();
        
        try {
            
            $produto = $this->produto->store($payload);

            return response()->json(new ProdutoResource($produto));

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }

   
    public function show(string $id)
    {
        $produto = $this->produto->findById($id);

        return response()->json([
            'produto' => new ProdutoResource($produto)
        ], Response::HTTP_OK);
    }

    
    public function update(Request $request, Produto $produto)
    {
    }

   
    public function destroy(Produto $produto)
    {
    }
}
