<?php
/**
 * Админ-панель: главная панель управления
 */

session_start();
require_once '../config.php';

// Проверка авторизации
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = getDBConnection();

// Получаем статистику
$stmt = $pdo->query("SELECT COUNT(*) FROM masters");
$mastersCount = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM sketches WHERE is_booked = FALSE");
$sketchesCount = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM courses");
$coursesCount = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM merch");
$merchCount = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM blog_posts");
$blogCount = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Tattoo Studio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #fafafa;
        }
        
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 250px;
            background: white;
            padding: 20px;
            border-right: 1px solid #eee;
        }
        
        .logo {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 40px;
            text-align: center;
        }
        
        .nav-menu {
            list-style: none;
        }
        
        .nav-menu li {
            margin-bottom: 10px;
        }
        
        .nav-menu a {
            display: block;
            padding: 12px 15px;
            color: #555;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .nav-menu a:hover,
        .nav-menu a.active {
            background-color: #f5f5f5;
            color: #333;
        }
        
        .logout {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
        }
        
        .logout a {
            display: block;
            padding: 12px 15px;
            color: #c33;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
        }
        
        .logout a:hover {
            background-color: #fee;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 40px;
        }
        
        .header {
            margin-bottom: 40px;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 300;
            color: #333;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .stat-card h3 {
            font-size: 14px;
            color: #777;
            margin-bottom: 10px;
            font-weight: 400;
        }
        
        .stat-card .number {
            font-size: 36px;
            color: #333;
            font-weight: 300;
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo">Tattoo Studio</div>
        
        <ul class="nav-menu">
            <li><a href="dashboard.php" class="active">Главная</a></li>
            <li><a href="masters.php">Мастера</a></li>
            <li><a href="sketches.php">Эскизы</a></li>
            <li><a href="courses.php">Курсы</a></li>
            <li><a href="merch.php">Мерч</a></li>
            <li><a href="blog.php">Блог</a></li>
            <li><a href="bookings.php">Заявки</a></li>
        </ul>
        
        <div class="logout">
            <a href="logout.php">Выйти</a>
        </div>
    </aside>
    
    <main class="main-content">
        <div class="header">
            <h1>Панель управления</h1>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Мастера</h3>
                <div class="number"><?php echo $mastersCount; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Свободные эскизы</h3>
                <div class="number"><?php echo $sketchesCount; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Курсы</h3>
                <div class="number"><?php echo $coursesCount; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Мерч</h3>
                <div class="number"><?php echo $merchCount; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Статьи в блоге</h3>
                <div class="number"><?php echo $blogCount; ?></div>
            </div>
        </div>
    </main>
</body>
</html>
