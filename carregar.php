<?php
/*
Template Name: carregar
*/

// Caminho para o arquivo JSON
$jsonFilePath = get_template_directory() . '/js/agenda.json';

if (file_exists($jsonFilePath)) {
  // Carregue o conteúdo do arquivo JSON
  $jsonData = file_get_contents($jsonFilePath);

  // Decodifique o JSON em um array associativo
  $postData = json_decode($jsonData, true);

  // Verifique se o JSON foi decodificado com sucesso
  if ($postData) {
    // Iterar sobre cada item do array
    foreach ($postData as $item) {
      // Crie um novo post no tipo de post personalizado
      $postId = wp_insert_post(array(
        'post_type' => 'agendas',
        'post_title' => $item['title'],
        'post_content' => $item['content'],
        'post_excerpt' => $item['excerpt'],
        'post_status' => $item['status'],
      ));

      // Verifique se o post foi criado com sucesso
      if ($postId) {
        // Defina os campos ACF para o post criado
        update_field('data', $item['acf']['data'], $postId);
        update_field('local', $item['acf']['local'], $postId);
        update_field('tipo', $item['acf']['tipo'], $postId);
        update_field('categoria', $item['acf']['categoria'], $postId);
        update_field('url', $item['acf']['url'], $postId);
        update_field('imagem_destaque', $item['acf']['imagem_destaque'], $postId);

        // Post criado com sucesso
        echo 'Post criado: ' . get_the_title($postId) . '<br>';
      } else {
        // Erro ao criar o post
        echo 'Erro ao criar o post<br>';
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
