<?php
$servername = "db";
$username = "user";
$password = "userpassword";
$dbname = "adstable";

// takes about 10s to intiate
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// here we go again
$categories = ['factory', 'galley', 'unemployed'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $category = trim($_POST['category']);
    $title = trim($_POST['title']);
    $text = trim($_POST['text']);

    if ($email && $category && $title && $text && in_array($category, $categories)) {
        $stmt = $conn->prepare("INSERT INTO ads (email, category, title, text) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $email, $category, $title, $text);
        $stmt->execute();
        $stmt->close();
        echo "<p>Объявление добавлено!</p>";
    } else {
        echo "<p>Пожалуйста, заполните все поля корректно.</p>";
    }
}

echo "<h1>Список объявлений</h1>";
echo "<form method='post'>";
echo "<label for='email'>Email:</label><br>";
echo "<input type='email' name='email' required><br><br>";

echo "<label for='category'>Категория:</label><br>";
echo "<select name='category' required>";
foreach ($categories as $category) {
    echo "<option value='$category'>$category</option>";
}
echo "</select><br><br>";

echo "<label for='title'>Заголовок объявления:</label><br>";
echo "<input type='text' name='title' required><br><br>";

echo "<label for='text'>Текст объявления:</label><br>";
echo "<textarea name='text' rows='4' required></textarea><br><br>";

echo "<input type='submit' value='Добавить'>";
echo "</form>";

echo "<h2>Объявления</h2>";
foreach ($categories as $category) {
    echo "<h3>" . ucfirst($category) . "</h3>";
    echo "<ul>";

    $stmt = $conn->prepare("SELECT title, text FROM ads WHERE category = ?");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $title = htmlspecialchars($row['title']);
            $content = htmlspecialchars($row['text']);
            echo "<li><strong>$title:</strong> $content</li>";
        }
    } else {
        echo "<li>Нет объявлений.</li>";
    }

    echo "</ul>";
}

$conn->close();
?>
