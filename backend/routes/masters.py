"""
API маршруты для управления мастерами
"""
from flask import Blueprint, request, jsonify
from models import db, Master, MasterWork

masters_bp = Blueprint('masters', __name__)


@masters_bp.route('/masters', methods=['GET', 'OPTIONS'])
@masters_bp.route('/masters/<int:master_id>', methods=['GET', 'OPTIONS'])
def get_masters(master_id=None):
    """Получение всех мастеров или одного мастера по ID"""
    if request.method == 'OPTIONS':
        return '', 200
    
    try:
        if master_id:
            # Получение одного мастера
            master = Master.query.get(master_id)
            if not master:
                return jsonify({'success': False, 'message': 'Мастер не найден'}), 404
            
            works = MasterWork.query.filter_by(master_id=master_id).order_by(MasterWork.created_at.desc()).all()
            data = master.to_dict(include_works=True)
            return jsonify({'success': True, 'data': data})
        else:
            # Получение всех мастеров
            masters = Master.query.order_by(Master.created_at.desc()).all()
            result = []
            for master in masters:
                master_data = master.to_dict(include_works=True)
                result.append(master_data)
            
            return jsonify({'success': True, 'data': result})
    
    except Exception as e:
        return jsonify({'success': False, 'message': 'Ошибка сервера'}), 500


@masters_bp.route('/masters', methods=['POST', 'OPTIONS'])
def create_master():
    """Создание нового мастера"""
    if request.method == 'OPTIONS':
        return '', 200
    
    try:
        data = request.get_json()
        
        if not data or not data.get('name'):
            return jsonify({'success': False, 'message': 'Имя обязательно'}), 400
        
        master = Master(
            name=data['name'],
            description=data.get('description'),
            photo=data.get('photo'),
            instagram=data.get('instagram'),
            telegram=data.get('telegram'),
            vk=data.get('vk')
        )
        
        db.session.add(master)
        db.session.commit()
        
        return jsonify({'success': True, 'id': master.id}), 201
    
    except Exception as e:
        db.session.rollback()
        return jsonify({'success': False, 'message': 'Ошибка сервера'}), 500


@masters_bp.route('/masters/<int:master_id>', methods=['PUT', 'OPTIONS'])
def update_master(master_id):
    """Обновление мастера"""
    if request.method == 'OPTIONS':
        return '', 200
    
    try:
        master = Master.query.get(master_id)
        if not master:
            return jsonify({'success': False, 'message': 'Мастер не найден'}), 404
        
        data = request.get_json()
        
        master.name = data.get('name', master.name)
        master.description = data.get('description', master.description)
        master.photo = data.get('photo', master.photo)
        master.instagram = data.get('instagram', master.instagram)
        master.telegram = data.get('telegram', master.telegram)
        master.vk = data.get('vk', master.vk)
        
        db.session.commit()
        
        return jsonify({'success': True})
    
    except Exception as e:
        db.session.rollback()
        return jsonify({'success': False, 'message': 'Ошибка сервера'}), 500


@masters_bp.route('/masters/<int:master_id>', methods=['DELETE', 'OPTIONS'])
def delete_master(master_id):
    """Удаление мастера"""
    if request.method == 'OPTIONS':
        return '', 200
    
    try:
        master = Master.query.get(master_id)
        if not master:
            return jsonify({'success': False, 'message': 'Мастер не найден'}), 404
        
        db.session.delete(master)
        db.session.commit()
        
        return jsonify({'success': True})
    
    except Exception as e:
        db.session.rollback()
        return jsonify({'success': False, 'message': 'Ошибка сервера'}), 500
