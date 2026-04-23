<?php
/**
 * Админ-панель: управление мастерами
 */

session_start();
require_once '../config.php';

// Проверка авторизации
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = getDBConnection();
$message = '';

// Обработка добавления/обновления мастера
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'update') {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $instagram = $_POST['instagram'] ?? '';
        $photo = $_POST['existing_photo'] ?? '';
        
        // Загрузка нового фото
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $upload = uploadFile($_FILES['photo'], 'masters');
            if ($upload['success']) {
                $photo = $upload['path'];
            }
        }
        
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO masters (name, description, photo, instagram) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $description, $photo, $instagram]);
            $message = 'Мастер успешно добавлен';
        } else {
            $id = $_POST['id'] ?? 0;
            $stmt = $pdo->prepare("UPDATE masters SET name = ?, description = ?, photo = ?, instagram = ? WHERE id = ?");
            $stmt->execute([$name, $description, $photo, $instagram, $id]);
            $message = 'Мастер успешно обновлен';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM masters WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Мастер успешно удален';
    } elseif ($action === 'add_work') {
        $masterId = $_POST['master_id'] ?? 0;
        $description = $_POST['work_description'] ?? '';
        
        if (isset($_FILES['work_photo']) && $_FILES['work_photo']['error'] === UPLOAD_ERR_OK) {
            $upload = uploadFile($_FILES['work_photo'], 'masters');
            if ($upload['success']) {
                $stmt = $pdo->prepare("INSERT INTO master_works (master_id, photo, description) VALUES (?, ?, ?)");
                $stmt->execute([$masterId, $upload['path'], $description]);
                $message = 'Работа успешно добавлена';
            }
        }
    }
}

// Получение всех мастеров
$stmt = $pdo->query("SELECT * FROM masters ORDER BY created_at DESC");
$masters = $stmt->fetchAll();

// Получение работ для каждого мастера
foreach ($masters as &$master) {
    $stmt = $pdo->prepare("SELECT * FROM master_works WHERE master_id = ? ORDER BY created_at DESC");
    $stmt->execute([$master['id']]);
    $master['works'] = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мастера - Админ-панель</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #fafafa; }
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 250px; background: white; padding: 20px; border-right: 1px solid #eee; }
        .logo { font-size: 20px; font-weight: 600; color: #333; margin-bottom: 40px; text-align: center; }
        .nav-menu { list-style: none; }
        .nav-menu li { margin-bottom: 10px; }
        .nav-menu a { display: block; padding: 12px 15px; color: #555; text-decoration: none; border-radius: 4px; }
        .nav-menu a:hover, .nav-menu a.active { background: #f5f5f5; color: #333; }
        .logout { position: absolute; bottom: 20px; left: 20px; right: 20px; }
        .logout a { display: block; padding: 12px 15px; color: #c33; text-decoration: none; border-radius: 4px; text-align: center; }
        .main-content { margin-left: 250px; padding: 40px; }
        .header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 28px; font-weight: 300; color: #333; }
        .btn { padding: 10px 20px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; }
        .btn:hover { background: #555; }
        .btn-small { padding: 6px 12px; font-size: 12px; }
        .btn-danger { background: #c33; }
        .btn-danger:hover { background: #a22; }
        .message { background: #efe; color: #272; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .masters-grid { display: grid; gap: 30px; }
        .master-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .master-header { display: flex; gap: 20px; margin-bottom: 20px; }
        .master-photo { width: 100px; height: 100px; border-radius: 8px; object-fit: cover; background: #f5f5f5; }
        .master-info h3 { font-size: 20px; font-weight: 500; margin-bottom: 10px; }
        .master-info p { color: #666; font-size: 14px; margin-bottom: 10px; }
        .works-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; margin-top: 20px; }
        .work-item { position: relative; }
        .work-item img { width: 100%; height: 150px; object-fit: cover; border-radius: 4px; }
        .modal { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; }
        .modal.active { display: flex; align-items: center; justify-content: center; }
        .modal-content { background: white; padding: 40px; border-radius: 8px; width: 100%; max-width: 500px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #555; font-size: 14px; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .form-group textarea { height: 100px; resize: vertical; }
        .actions { display: flex; gap: 10px; margin-top: 20px; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo">Tattoo Studio</div>
        <ul class="nav-menu">
            <li><a href="dashboard.php">Главная</a></li>
            <li><a href="masters.php" class="active">Мастера</a></li>
            <li><a href="sketches.php">Эскизы</a></li>
            <li><a href="courses.php">Курсы</a></li>
            <li><a href="merch.php">Мерч</a></li>
            <li><a href="blog.php">Блог</a></li>
            <li><a href="bookings.php">Заявки</a></li>
        </ul>
        <div class="logout"><a href="logout.php">Выйти</a></div>
    </aside>
    
    <main class="main-content">
        <div class="header">
            <h1>Мастера</h1>
            <button class="btn" onclick="openModal()">Добавить мастера</button>
        </div>
        
        <?php if ($message): ?>
            <div class="message"><?php echo e($message); ?></div>
        <?php endif; ?>
        
        <div class="masters-grid">
            <?php foreach ($masters as $master): ?>
                <div class="master-card">
                    <div class="master-header">
                        <?php if ($master['photo']): ?>
                            <img src="../uploads/<?php echo e($master['photo']); ?>" alt="<?php echo e($master['name']); ?>" class="master-photo">
                        <?php endif; ?>
                        <div class="master-info">
                            <h3><?php echo e($master['name']); ?></h3>
                            <p><?php echo e($master['description']); ?></p>
                            <?php if ($master['instagram']): ?>
                                <p>Instagram: <?php echo e($master['instagram']); ?></p>
                            <?php endif; ?>
                            <div class="actions">
                                <button class="btn btn-small" onclick='editMaster(<?php echo json_encode($master); ?>)'>Редактировать</button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Удалить мастера?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $master['id']; ?>">
                                    <button type="submit" class="btn btn-small btn-danger">Удалить</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <h4 style="margin: 20px 0 10px;">Работы мастера</h4>
                    <div class="works-grid">
                        <?php foreach ($master['works'] as $work): ?>
                            <div class="work-item">
                                <img src="../uploads/<?php echo e($work['photo']); ?>" alt="Работа">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <button class="btn btn-small" style="margin-top: 15px;" onclick="openWorkModal(<?php echo $master['id']; ?>)">Добавить работу</button>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    
    <!-- Модальное окно добавления/редактирования мастера -->
    <div class="modal" id="masterModal">
        <div class="modal-content">
            <h2 style="margin-bottom: 20px;" id="modalTitle">Добавить мастера</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="masterId">
                <input type="hidden" name="existing_photo" id="existingPhoto">
                
                <div class="form-group">
                    <label>Имя</label>
                    <input type="text" name="name" id="masterName" required>
                </div>
                <div class="form-group">
                    <label>Описание</label>
                    <textarea name="description" id="masterDescription"></textarea>
                </div>
                <div class="form-group">
                    <label>Instagram</label>
                    <input type="text" name="instagram" id="masterInstagram">
                </div>
                <div class="form-group">
                    <label>Фото</label>
                    <input type="file" name="photo" accept="image/*">
                </div>
                <div class="actions">
                    <button type="submit" class="btn">Сохранить</button>
                    <button type="button" class="btn" onclick="closeModal()" style="background: #999;">Отмена</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Модальное окно добавления работы -->
    <div class="modal" id="workModal">
        <div class="modal-content">
            <h2 style="margin-bottom: 20px;">Добавить работу</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_work">
                <input type="hidden" name="master_id" id="workMasterId">
                
                <div class="form-group">
                    <label>Фото работы</label>
                    <input type="file" name="work_photo" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label>Описание</label>
                    <textarea name="work_description"></textarea>
                </div>
                <div class="actions">
                    <button type="submit" class="btn">Сохранить</button>
                    <button type="button" class="btn" onclick="document.getElementById('workModal').classList.remove('active')" style="background: #999;">Отмена</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openModal() {
            document.getElementById('modalTitle').textContent = 'Добавить мастера';
            document.getElementById('formAction').value = 'add';
            document.getElementById('masterId').value = '';
            document.getElementById('masterName').value = '';
            document.getElementById('masterDescription').value = '';
            document.getElementById('masterInstagram').value = '';
            document.getElementById('existingPhoto').value = '';
            document.getElementById('masterModal').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('masterModal').classList.remove('active');
        }
        
        function editMaster(master) {
            document.getElementById('modalTitle').textContent = 'Редактировать мастера';
            document.getElementById('formAction').value = 'update';
            document.getElementById('masterId').value = master.id;
            document.getElementById('masterName').value = master.name;
            document.getElementById('masterDescription').value = master.description || '';
            document.getElementById('masterInstagram').value = master.instagram || '';
            document.getElementById('existingPhoto').value = master.photo || '';
            document.getElementById('masterModal').classList.add('active');
        }
        
        function openWorkModal(masterId) {
            document.getElementById('workMasterId').value = masterId;
            document.getElementById('workModal').classList.add('active');
        }
    </script>
</body>
</html>
