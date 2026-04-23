"""
Конфигурация приложения и базы данных
"""
import os
import secrets
from datetime import timedelta
from dotenv import load_dotenv

# Загрузка переменных окружения
load_dotenv()

class Config:
    """Класс конфигурации приложения"""
    
    # Секретный ключ для сессий
    SECRET_KEY = os.getenv('SECRET_KEY', secrets.token_hex(32))
    
    # Настройки базы данных
    DB_HOST = os.getenv('DB_HOST', 'localhost')
    DB_NAME = os.getenv('DB_NAME', 'tattoo_studio')
    DB_USER = os.getenv('DB_USER', 'root')
    DB_PASS = os.getenv('DB_PASS', '')
    
    # SQLAlchemy URI
    SQLALCHEMY_DATABASE_URI = f"mysql+mysqlconnector://{DB_USER}:{DB_PASS}@{DB_HOST}/{DB_NAME}"
    SQLALCHEMY_TRACK_MODIFICATIONS = False
    SQLALCHEMY_POOL_RECYCLE = 3600
    
    # Настройки сайта
    SITE_URL = os.getenv('SITE_URL', 'http://localhost:5000')
    SITE_NAME = os.getenv('SITE_NAME', 'Tattoo Studio')
    
    # Настройки загрузки файлов
    UPLOAD_FOLDER = os.path.join(os.path.dirname(__file__), os.getenv('UPLOAD_DIR', 'uploads'))
    MAX_CONTENT_LENGTH = int(os.getenv('MAX_FILE_SIZE', 5 * 1024 * 1024))
    ALLOWED_EXTENSIONS = set(os.getenv('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif,webp').split(','))
    
    # Настройки сессии
    PERMANENT_SESSION_LIFETIME = timedelta(hours=24)
    SESSION_TYPE = 'filesystem'
    
    # Настройки ЮMoney
    YUMONEY_SHOP_ID = os.getenv('YUMONEY_SHOP_ID', '')
    YUMONEY_SECRET_KEY = os.getenv('YUMONEY_SECRET_KEY', '')


def create_upload_folders():
    """Создание папок для загрузки файлов"""
    import os
    subdirs = ['masters', 'sketches', 'courses', 'merch', 'blog']
    for subdir in subdirs:
        path = os.path.join(Config.UPLOAD_FOLDER, subdir)
        os.makedirs(path, exist_ok=True)
