<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProdutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Produto::create([
            'arquivo_3d' => 'app/public/produtos/objetos/objeto-01.glb',
            'capa' => 'app/public/produtos/fotos/foto-01.jpeg',
            'titulo' => 'Mochila Adventure',
            'descricao' => 'Mochila resistente para trilhas e viagens curtas.',
            'valor' => 399.99
        ]);

        Produto::create([
            'arquivo_3d' => 'app/public/produtos/objetos/objeto-02.glb',
            'capa' => 'app/public/produtos/fotos/foto-02.jpeg',
            'titulo' => 'Fone Bluetooth Pro',
            'descricao' => 'Som de alta qualidade e bateria de longa duração.',
            'valor' => 256.70
        ]);

        Produto::create([
            'arquivo_3d' => 'app/public/produtos/objetos/objeto-03.glb',
            'capa' => 'app/public/produtos/fotos/foto-03.jpeg',
            'titulo' => 'Relógio SmartFit',
            'descricao' => 'Monitor de atividades com design moderno e leve.',
            'valor' => 359.20
        ]);

        Produto::create([
            'arquivo_3d' => 'app/public/produtos/objetos/objeto-04.glb',
            'capa' => 'app/public/produtos/fotos/foto-04.jpeg',
            'titulo' => 'Câmera Compacta HD',
            'descricao' => 'Fotos nítidas e vídeos em alta definição.',
            'valor' => 620.80
        ]);

        Produto::create([
            'arquivo_3d' => 'app/public/produtos/objetos/objeto-05.glb',
            'capa' => 'app/public/produtos/fotos/foto-05.jpeg',
            'titulo' => 'Tênis Running Flex',
            'descricao' => 'Confortável e ideal para corridas urbanas.',
            'valor' => 1200.00
        ]);       

        Produto::factory()->count(10)->create();
    }
}
