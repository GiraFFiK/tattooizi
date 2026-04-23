# Бэкенд для Tattoo Studio на Python/Flask

## Установка

1. Установите зависимости:
```bash
pip install -r requirements.txt
```

2. Скопируйте файл конфигурации:
```bash
cp .env.example .env
```

3. Отредактируйте `.env` и настройте параметры базы данных.

4. Создайте базу данных и импортируйте схему:
```bash
mysql -u root -p tattoo_studio < ../database/schema.sql
```

5. Запустите приложение:
```bash
python app.py
```

Приложение будет доступно по адресу: http://localhost:5000

## Структура проекта

```
backend/
├── app.py              # Главный файл приложения
├── config.py           # Конфигурация
├── models.py           # Модели базы данных
├── requirements.txt    # Зависимости
├── routes/             # Маршруты API
│   ├── __init__.py
│   ├── masters.py      # API мастеров
│   ├── sketches.py     # API эскизов
│   ├── shop.py         # API магазина
│   └── admin.py        # Админ-панель
├── templates/admin/    # Шаблоны админ-панели
└── uploads/            # Загруженные файлы
```

## API Endpoints

### Мастера
- `GET /api/masters` - Получить всех мастеров
- `GET /api/masters/<id>` - Получить мастера по ID
- `POST /api/masters` - Создать мастера
- `PUT /api/masters/<id>` - Обновить мастера
- `DELETE /api/masters/<id>` - Удалить мастера

### Эскизы
- `GET /api/sketches` - Получить все свободные эскизы
- `POST /api/sketches/book` - Забронировать эскиз

### Магазин
- `GET /api/shop/courses` - Получить все курсы
- `GET /api/shop/merch` - Получить весь мерч
- `POST /api/shop/create_payment` - Создать платеж

### Админ-панель
- `/admin/login` - Вход
- `/admin/dashboard` - Главная панель
- `/admin/masters` - Управление мастерами
- `/admin/bookings` - Заявки на бронирование

## Тестовые данные для входа в админ-панель

- Логин: `admin`
- Пароль: `nhbrjirb3ldfrjnf2!`
