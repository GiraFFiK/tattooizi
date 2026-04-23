<?php
/**
 * Конфигурация базы данных и настроек сайта
 */

// Настройки базы данных
define('DB_HOST', 'localhost');
define('DB_NAME', 'tattoo_studio');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Настройки сайта
define('SITE_URL', 'http://localhost/tattoo-studio');
define('SITE_NAME', 'Tattoo Studio');

// Настройки загрузки файлов
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Настройки ЮMoney
define('YUMONEY_SHOP_ID', 'your_shop_id_here');
define('YUMONEY_SECRET_KEY', 'your_secret_key_here');

// Подключение к базе данных
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        die("Ошибка подключения к базе данных: " . $e->getMessage());
    }
}

// Функция для безопасного вывода данных
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Функция для генерации уникального имени файла
function generateUniqueFilename($extension) {
    return bin2hex(random_bytes(16)) . '.' . $extension;
}

// Функция для проверки загружаемого файла
function validateUploadFile($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Ошибка загрузки файла'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'Файл слишком большой'];
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'message' => 'Недопустимый формат файла'];
    }
    
    return ['success' => true];
}

// Функция для обработки загрузки файла
function uploadFile($file, $subdir) {
    $validation = validateUploadFile($file);
    if (!$validation['success']) {
        return $validation;
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = generateUniqueFilename($extension);
    $destination = UPLOAD_DIR . $subdir . '/' . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'message' => 'Не удалось сохранить файл'];
    }
    
    return ['success' => true, 'filename' => $filename, 'path' => $subdir . '/' . $filename];
}
?>
