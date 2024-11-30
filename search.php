<?php
$config = require 'db_config.php';

$select = "
            SELECT posts.title, comments.body
            FROM comments
            JOIN posts ON posts.id = comments.postId
            WHERE comments.body ILIKE :searchText
        ";

try {
    $pdo = new PDO("pgsql:host={$config['host']};dbname={$config['dbname']}", $config['user'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Инициализация переменных
$results = [];
$error = '';
$searchText = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchText = trim(isset($_POST['searchText']) ? $_POST['searchText'] : '');

    // Проверяем длину текста
    if (mb_strlen($searchText) < 3) {
        $error = "Введите минимум 3 символа для поиска.";
    } else {

        $stmt = $pdo->prepare($select);
        $stmt->execute([':searchText' => '%' . $searchText . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($results)) {
            $error = "Ничего не нашлось.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результаты поиска</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Результаты поиска</h1>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php elseif (!empty($results)): ?>
        <ul>
            <?php foreach ($results as $result): ?>
                <li>
                    <strong>Заголовок:</strong> <?= htmlspecialchars($result['title']) ?><br>
                    <strong>Комментарий:</strong> <?= htmlspecialchars($result['body']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <a href="form.html">Вернуться к поиску</a>
</div>
</body>
</html>