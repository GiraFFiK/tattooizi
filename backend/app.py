"""
Главный файл приложения Flask
"""
import os
from flask import Flask, jsonify
from flask_cors import CORS
from flask_session import Session
from config import Config, create_upload_folders
from models import db

# Импорт маршрутов
from routes.masters import masters_bp
from routes.sketches import sketches_bp
from routes.shop import shop_bp
from routes.admin import admin_bp


def create_app(config_class=Config):
    """Фабрика приложения Flask"""
    app = Flask(__name__, 
                static_folder='../frontend',
                static_url_path='')
    
    # Загрузка конфигурации
    app.config.from_object(config_class)
    
    # Инициализация расширений
    CORS(app, resources={r"/api/*": {"origins": "*"}})
    db.init_app(app)
    Session(app)
    
    # РегистрацияBlueprints
    app.register_blueprint(masters_bp, url_prefix='/api')
    app.register_blueprint(sketches_bp, url_prefix='/api')
    app.register_blueprint(shop_bp, url_prefix='/api')
    app.register_blueprint(admin_bp)
    
    # Создание папок для загрузок
    with app.app_context():
        create_upload_folders()
    
    # Обработка CORS preflight запросов
    @app.after_request
    def after_request(response):
        response.headers.add('Access-Control-Allow-Origin', '*')
        response.headers.add('Access-Control-Allow-Headers', 'Content-Type,Authorization')
        response.headers.add('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS')
        return response
    
    # Главный маршрут (отдает frontend)
    @app.route('/')
    def index():
        return app.send_static_file('index.html')
    
    # Маршрут для тестирования API
    @app.route('/api/health')
    def health():
        return jsonify({'success': True, 'message': 'API работает'})
    
    # Обработка ошибок
    @app.errorhandler(404)
    def not_found(error):
        return jsonify({'success': False, 'message': 'Не найдено'}), 404
    
    @app.errorhandler(500)
    def internal_error(error):
        db.session.rollback()
        return jsonify({'success': False, 'message': 'Ошибка сервера'}), 500
    
    return app


if __name__ == '__main__':
    app = create_app()
    app.run(debug=True, host='0.0.0.0', port=5000)
