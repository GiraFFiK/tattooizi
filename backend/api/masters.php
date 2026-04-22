<?php
/**
 * API для получения данных о мастерах
 */

require_once 'config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$pdo = getDBConnection();

// Получение всех мастеров
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['id'])) {
    try {
        $stmt = $pdo->query("SELECT * FROM masters ORDER BY created_at DESC");
        $masters = $stmt->fetchAll();
        
        // Для каждого мастера получаем его работы
        foreach ($masters as &$master) {
            $stmt = $pdo->prepare("SELECT * FROM master_works WHERE master_id = ? ORDER BY created_at DESC");
            $stmt->execute([$master['id']]);
            $master['works'] = $stmt->fetchAll();
        }
        
        echo json_encode(['success' => true, 'data' => $masters]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
    }
    exit;
}

// Получение одного мастера
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM masters WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $master = $stmt->fetch();
        
        if (!$master) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Мастер не найден']);
            exit;
        }
        
        $stmt = $pdo->prepare("SELECT * FROM master_works WHERE master_id = ? ORDER BY created_at DESC");
        $stmt->execute([$master['id']]);
        $master['works'] = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'data' => $master]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
    }
    exit;
}

// Создание мастера (требуется авторизация)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Здесь должна быть проверка авторизации
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['name'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Имя обязательно']);
            exit;
        }
        
        $stmt = $pdo->prepare("INSERT INTO masters (name, description, photo, instagram, telegram, vk) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['photo'] ?? null,
            $data['instagram'] ?? null,
            $data['telegram'] ?? null,
            $data['vk'] ?? null
        ]);
        
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
    }
    exit;
}

// Обновление мастера
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['id'])) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $pdo->prepare("UPDATE masters SET name = ?, description = ?, photo = ?, instagram = ? WHERE id = ?");
        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['photo'] ?? null,
            $data['instagram'] ?? null,
            $_GET['id']
        ]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
    }
    exit;
}

// Удаление мастера
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM masters WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
?>
