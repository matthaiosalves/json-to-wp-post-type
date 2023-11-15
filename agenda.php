<?php
/*
Template Name: carregar
*/

// Caminho para o arquivo JSON
$jsonFilePath = get_template_directory() . '/js/importados/agenda-2.json';

if (file_exists($jsonFilePath)) {
  // Carregue o conteúdo do arquivo JSON
  $jsonData = file_get_contents($jsonFilePath);

  // Decodifique o JSON em um array associativo
  $postData = json_decode($jsonData, true);

  // Verifique se o JSON foi decodificado com sucesso
  if ($postData) {
    // Iterar sobre cada item do array
    foreach ($postData as $item) {
      // Verificar se o post já existe com base no título ou slug
      $existingPost = get_page_by_title($item['title'], OBJECT, 'agendas');
      if (!$existingPost) {
        $existingPost = get_page_by_path($item['slug'], OBJECT, 'agendas');
      }

      // Criar um novo post apenas se o post não existir
      if (!$existingPost) {
        // Crie um novo post no tipo de post personalizado
        $postId = wp_insert_post(array(
          'post_type' => 'agendas',
          'post_title' => isset($item['title']) ? $item['title'] : "",
          'post_content' => isset($item['content']) ? $item['content'] : "",
          'post_excerpt' => isset($item['excerpt']) ? $item['excerpt'] : "",
          'post_name' => isset($item['slug']) ? $item['slug'] : sanitize_title($item['title']),
          'post_status' => isset($item['status']) ? $item['status'] : "publish",
        ));

        // Verifique se o post foi criado com sucesso
        if ($postId) {
          // Defina os campos ACF para o post criado
          update_field('data', isset($item['acf']['data']) ? $item['acf']['data'] : "", $postId);
          update_field('local', isset($item['acf']['local']) ? $item['acf']['local'] : "", $postId);
          update_field('tipo', isset($item['acf']['tipo']) ? $item['acf']['tipo'] : "", $postId);
          update_field('categoria', isset($item['acf']['categoria']) ? $item['acf']['categoria'] : "", $postId);

          // Crie um array para o campo de URL
          $urlArray = array(
            'title' => isset($item['acf']['url']['title']) ? $item['acf']['url']['title'] : "",
            'url' => isset($item['acf']['url']['url']) ? $item['acf']['url']['url'] : "",
            'target' => isset($item['acf']['url']['target']) ? $item['acf']['url']['target'] : "",
          );
          update_field('url', $urlArray, $postId);

          update_field('imagem_destaque', isset($item['acf']['imagem_destaque']) ? $item['acf']['imagem_destaque'] : "", $postId);

          // Post criado com sucesso
          echo 'Post criado: ' . get_the_title($postId) . '<br>';
        } else {
          // Erro ao criar o post
          echo 'Erro ao criar o post<br>';
        }
      } else {
        // Post já existe, pule a criação
        echo 'Post já existe: ' . get_the_title($existingPost->ID) . '<br>';
      }
    }
  } else {
    // Erro ao decodificar o JSON
    echo 'Erro ao decodificar o JSON<br>';
  }
} else {
  // O arquivo JSON não foi encontrado
  echo 'Arquivo JSON não encontrado<br>';
}
