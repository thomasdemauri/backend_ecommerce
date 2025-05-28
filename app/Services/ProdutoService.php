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

        return $produtos;
    }

    public function findById(string $id): ?Produto
    {
        try {
            $produto = $this->model::findOrFail($id);
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

            $objetoPath = $this->storeOnAWS($payload['arquivo_3d'], 'objetos');
            $capaPath = $this->storeOnAWS($payload['capa'], 'capa');

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

        try 
        {
            $path = Storage::disk('produtos')->putFileAs($subPasta, $file, $filename);

        } catch (Exception $e) {
            throw new Exception("Falha ao salvar arquivo na S3. " . $e->getMessage());
        }

        return $path;
    }
}