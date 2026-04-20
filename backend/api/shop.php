<?php
/**
 * API для курсов и мерча
 */

require_once 'config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$pdo = getDBConnection();

// Получение всех курсов
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type']) && $_GET['type'] === 'courses') {
    try {
        $stmt = $pdo->query("SELECT c.*, m.name as master_name FROM courses c LEFT JOIN masters m ON c.master_id = m.id ORDER BY c.created_at DESC");
        $courses = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $courses]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
    }
    exit;
}

// Получение всего мерча
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type']) && $_GET['type'] === 'merch') {
    try {
        $stmt = $pdo->query("SELECT * FROM merch WHERE stock > 0 ORDER BY created_at DESC");
        $merch = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $merch]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
    }
    exit;
}

// Создание платежа через ЮMoney
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_payment') {
    try {
        $paymentType = $_POST['payment_type'] ?? null; // course, merch, sketch
        $itemId = $_POST['item_id'] ?? null;
        $amount = $_POST['amount'] ?? null;
        
        if (!$paymentType || !$itemId || !$amount) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Некорректные данные']);
            exit;
        }
        
        // Генерируем уникальный ID заказа
        $orderId = uniqid('order_');
        
        // Сохраняем платеж в базу
        $stmt = $pdo->prepare("INSERT INTO payments (order_id, amount, payment_type, item_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$orderId, $amount, $paymentType, $itemId]);
        
        // Формируем данные для ЮMoney
        $paymentData = [
            'shop_id' => YUMONEY_SHOP_ID,
            'order_id' => $orderId,
            'amount' => $amount,
            'currency' => 'RUB',
            'description' => 'Оплата ' . ($paymentType === 'course' ? 'курса' : ($paymentType === 'merch' ? 'мерча' : 'эскиза')),
            'success_url' => SITE_URL . '/payment-success.html',
            'fail_url' => SITE_URL . '/payment-fail.html'
        ];
        
        // В реальном проекте здесь будет запрос к API ЮMoney
        // Для демонстрации возвращаем тестовые данные
        echo json_encode([
            'success' => true, 
            'payment_url' => 'https://yoomoney.ru/quickpay/confirm?' . http_build_query($paymentData),
            'order_id' => $orderId
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
?>
