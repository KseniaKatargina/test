<?php
$config = require 'db_config.php';

try {
    $pdo = new PDO("pgsql:host={$config['host']};dbname={$config['dbname']}", $config['user'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo("Ошибка подключения к базе данных: " . $e->getMessage());
}

function fetchData($url) {
    $response = file_get_contents($url);
    if ($response === FALSE) {
        echo("Ошибка при загрузке данных с $url");
    }
    return json_decode($response, true);
}

$posts = fetchData('https://jsonplaceholder.typicode.com/posts');
$comments = fetchData('https://jsonplaceholder.typicode.com/comments');


$load_posts = $pdo->prepare("INSERT INTO posts (id, userId, title, body) VALUES (:id, :userId, :title, :body)");
foreach ($posts as $post) {
    $load_posts->execute([
        ':id' => $post['id'],
        ':userId' => $post['userId'],
        ':title' => $post['title'],
        ':body' => $post['body']
    ]);
}

// Загружаем комментарии в базу данных
$load_comments = $pdo->prepare("INSERT INTO comments (id, postId, name, email, body) VALUES (:id, :postId, :name, :email, :body)");
foreach ($comments as $comment) {
    $load_comments->execute([
        ':id' => $comment['id'],
        ':postId' => $comment['postId'],
        ':name' => $comment['name'],
        ':email' => $comment['email'],
        ':body' => $comment['body']
    ]);
}

// Выводим сообщение о завершении
echo "Загружено " . count($posts) . " записей и " . count($comments) . " комментариев.\n";
?>
