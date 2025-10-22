<?php
// Função auxiliar para calcular tempo decorrido (JÁ CORRIGIDA)
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    // Calcula semanas a partir dos dias
    $weeks = floor($diff->d / 7);
    $days = $diff->d % 7;

    $string = array(
        'y' => 'ano',
        'm' => 'mês',
        'w' => 'semana',
        'd' => 'dia',
        'h' => 'hora',
        'i' => 'minuto',
        's' => 'segundo',
    );

    foreach ($string as $k => &$v) {
        if ($k == 'w') {
            $value = $weeks;
        } elseif ($k == 'd') {
            $value = $days;
        } else {
            $value = $diff->$k;
        }

        if ($value) {
            $v = $value . ' ' . $v . ($value > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' atrás' : 'agora mesmo';
}

// Simulação de dados de notícias (substitua pela sua fonte real)
$noticias = [
    [
        'id' => 1,
        'titulo' => 'Nova atualização do sistema traz melhorias de performance',
        'conteudo' => 'A última atualização do nosso sistema incluiu significativas melhorias de performance e novas funcionalidades para os usuários...',
        'data_publicacao' => '2024-01-15 14:30:00',
        'autor' => 'Admin',
        'categoria' => 'Sistema',
        'imagem' => 'noticia1.jpg',
        'destaque' => true
    ],
    [
        'id' => 2,
        'titulo' => 'Empresa atinge marca de 10.000 usuários',
        'conteudo' => 'Estamos felizes em anunciar que nossa plataforma atingiu a marca de 10.000 usuários ativos...',
        'data_publicacao' => '2024-01-10 09:15:00',
        'autor' => 'Marketing',
        'categoria' => 'Empresa',
        'imagem' => 'noticia2.jpg',
        'destaque' => true
    ],
    [
        'id' => 3,
        'titulo' => 'Novos recursos disponíveis para desenvolvedores',
        'conteudo' => 'A API foi atualizada com novos endpoints e documentação aprimorada...',
        'data_publicacao' => '2024-01-05 16:45:00',
        'autor' => 'Dev Team',
        'categoria' => 'Desenvolvimento',
        'imagem' => 'noticia3.jpg',
        'destaque' => false
    ],
    [
        'id' => 4,
        'titulo' => 'Manutenção programada para o próximo fim de semana',
        'conteudo' => 'Informamos que haverá uma manutenção programada no sistema no próximo sábado...',
        'data_publicacao' => '2024-01-03 11:20:00',
        'autor' => 'Suporte',
        'categoria' => 'Manutenção',
        'imagem' => 'noticia4.jpg',
        'destaque' => false
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notícias</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            color: #333;
        }

        .noticias-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
            padding: 20px 0;
        }

        .page-header h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .page-header p {
            color: #7f8c8d;
            font-size: 1.1rem;
        }

        /* Destaque */
        .destaque {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #3498db;
        }

        .destaque h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }

        .destaque-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .destaque-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .destaque p {
            color: #555;
            line-height: 1.8;
        }

        /* Grid de Notícias */
        .noticias-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .noticia-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .noticia-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .noticia-imagem {
            width: 100%;
            height: 200px;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 0.9rem;
        }

        .noticia-content {
            padding: 20px;
        }

        .noticia-categoria {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-bottom: 10px;
        }

        .noticia-titulo {
            font-size: 1.3rem;
            color: #2c3e50;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .noticia-resumo {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .noticia-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: #7f8c8d;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        .noticia-autor {
            font-weight: bold;
        }

        .noticia-data {
            color: #95a5a6;
        }

        /* Responsivo */
        @media (max-width: 768px) {
            .noticias-grid {
                grid-template-columns: 1fr;
            }
            
            .destaque {
                padding: 20px;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="noticias-container">
        <header class="page-header">
            <h1>Últimas Notícias</h1>
            <p>Fique por dentro das novidades e atualizações</p>
        </header>

        <?php if (!empty($noticias)): ?>
            <!-- Notícias em Destaque -->
            <?php 
            $destaques = array_filter($noticias, function($noticia) {
                return $noticia['destaque'];
            });
            ?>
            
            <?php if (!empty($destaques)): ?>
                <section class="destaque">
                    <?php $destaque = current($destaques); ?>
                    <h2><?php echo htmlspecialchars($destaque['titulo']); ?></h2>
                    <div class="destaque-meta">
                        <span class="categoria"><?php echo htmlspecialchars($destaque['categoria']); ?></span>
                        <span class="autor">Por: <?php echo htmlspecialchars($destaque['autor']); ?></span>
                        <span class="data"><?php echo time_elapsed_string($destaque['data_publicacao']); ?></span>
                    </div>
                    <p><?php echo htmlspecialchars($destaque['conteudo']); ?></p>
                </section>
            <?php endif; ?>

            <!-- Grid de Notícias -->
            <section class="noticias-grid">
                <?php foreach ($noticias as $noticia): ?>
                    <?php if (!$noticia['destaque']): ?>
                        <article class="noticia-card">
                            <div class="noticia-imagem">
                                [Imagem: <?php echo htmlspecialchars($noticia['imagem']); ?>]
                            </div>
                            <div class="noticia-content">
                                <span class="noticia-categoria"><?php echo htmlspecialchars($noticia['categoria']); ?></span>
                                <h3 class="noticia-titulo"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                                <p class="noticia-resumo"><?php echo htmlspecialchars($noticia['conteudo']); ?></p>
                                <div class="noticia-meta">
                                    <span class="noticia-autor"><?php echo htmlspecialchars($noticia['autor']); ?></span>
                                    <span class="noticia-data"><?php echo time_elapsed_string($noticia['data_publicacao']); ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>
        <?php else: ?>
            <div class="empty-state">
                <h3>Nenhuma notícia encontrada</h3>
                <p>Volte em breve para conferir as novidades!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<!-- Substitua o array $noticias pela sua fonte de dados real (banco de dados, API, etc.)

Ajuste os campos conforme sua estrutura de dados

Modifique o CSS para combinar com o design do seu site

Implemente o carregamento de imagens reais -->