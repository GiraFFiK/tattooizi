<?php
/**
 * API для эскизов (мгновенное бронирование)
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

// Получение всех эскизов
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['id'])) {
    try {
        $stmt = $pdo->query("SELECT * FROM sketches WHERE is_booked = FALSE ORDER BY created_at DESC");
        $sketches = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $sketches]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
    }
    exit;
}

// Бронирование эскиза
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'book') {
    try {
        $sketchId = $_POST['sketch_id'] ?? null;
        $clientName = $_POST['client_name'] ?? null;
        $clientPhone = $_POST['client_phone'] ?? null;
        $clientEmail = $_POST['client_email'] ?? null;
        $bookingDate = $_POST['booking_date'] ?? null;
        
        if (!$sketchId || !$clientName || !$clientPhone) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Заполните обязательные поля']);
            exit;
        }
        
        $pdo->beginTransaction();
        
        // Создаем заявку
        $stmt = $pdo->prepare("INSERT INTO sketch_bookings (sketch_id, client_name, client_phone, client_email, booking_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$sketchId, $clientName, $clientPhone, $clientEmail, $bookingDate]);
        
        // Помечаем эскиз как забронированный
        $stmt = $pdo->prepare("UPDATE sketches SET is_booked = TRUE WHERE id = ?");
        $stmt->execute([$sketchId]);
        
        $pdo->commit();
        
        echo json_encode(['success' => true, 'message' => 'Заявка успешно создана']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
?>
