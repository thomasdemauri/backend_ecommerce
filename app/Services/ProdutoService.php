<?php

namespace App\Services;

use App\Exceptions\ResourceNotFound;
use Exception;
use App\Models\Produto;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProdutoService 
{
    private Produto $model;

    public function __construct(Produto $model)
    {
        $this->model = $model;
    }

    public function paginate(int $perPage = 10)
    {
        $produtos = $this->model->paginate($perPage);

        $produtos->getCollection()->transform(function ($produto) {
            $produto->arquivo_3d = Storage::disk('produtos')->temporaryUrl(
                $produto->arquivo_3d, 
                now()->addMinutes(10),
                [
                    'ResponseContentType' => 'model/gltf-binary',
                    'ResponseContentDisposition' => 'inline',
                ]
            );
            $produto->capa = Storage::disk('produtos')->temporaryUrl($produto->capa, now()->addMinutes(10));
            return $produto;
        });

        return $produtos;
    }

    public function findById(string $id): ?Produto
    {
        try {
            $produto = $this->model::findOrFail($id);
            $produto->arquivo_3d = Storage::disk('produtos')->temporaryUrl(
                $produto->arquivo_3d, 
                now()->addMinutes(10),
                [
                    'ResponseContentType' => 'model/gltf-binary',
                    'ResponseContentDisposition' => 'inline',
                ]
            );
            $produto->capa = Storage::disk('produtos')->temporaryUrl($produto->capa, now()->addMinutes(10));
        } catch (Exception $e) {
            throw new ResourceNotFound();
        }

        return $produto;
    }

    public function store(array $payload): Produto
    {
        if (empty($payload)) {
            throw new Exception("Payload vazio.");
        }
        DB::beginTransaction();
        
        try {
            
            $objetoPath = $this->storeOnAWS(file:$payload['arquivo_3d'], subPasta:'objetos');
            $capaPath = $this->storeOnAWS(file:$payload['capa'], subPasta:'capa');
            
            
            $produto = $this->model::create([
                'arquivo_3d'    => $objetoPath,
                'capa'          => $capaPath,
                'titulo'        => $payload['titulo'],
                'descricao'     => $payload['descricao'],
                'valor'         => $payload['valor'],
            ]);
            
            DB::commit();
            
        } catch (Exception $e) {
            throw new Exception("Falha ao salvar novo produto. " . $e->getMessage());
        }
        
        return $produto;
    }
    
    private function storeOnAWS($file = null, string $subPasta = ''): string
    {
        if (empty($file)) {
            throw new Exception("Arquivo vazio.");
        }

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $subPasta . '/' . $filename;

        try 
        {
            $path = Storage::disk('produtos')->putFileAs('produtos', $file, $path);

        } catch (Exception $e) {
            throw new Exception("Falha ao salvar arquivo na S3. " . $e->getMessage());
        }
        return $path;
    }
}