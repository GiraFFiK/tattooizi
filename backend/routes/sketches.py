"""
API маршруты для эскизов и бронирования
"""
from flask import Blueprint, request, jsonify
from models import db, Sketch, SketchBooking

sketches_bp = Blueprint('sketches', __name__)


@sketches_bp.route('/sketches', methods=['GET', 'OPTIONS'])
@sketches_bp.route('/sketches/<int:sketch_id>', methods=['GET', 'OPTIONS'])
def get_sketches(sketch_id=None):
    """Получение всех эскизов или одного эскиза по ID"""
    if request.method == 'OPTIONS':
        return '', 200
    
    try:
        if sketch_id:
            sketch = Sketch.query.get(sketch_id)
            if not sketch:
                return jsonify({'success': False, 'message': 'Эскиз не найден'}), 404
            return jsonify({'success': True, 'data': sketch.to_dict()})
        else:
            # Получаем только свободные эскизы
            sketches = Sketch.query.filter_by(is_booked=False).order_by(Sketch.created_at.desc()).all()
            return jsonify({'success': True, 'data': [s.to_dict() for s in sketches]})
    
    except Exception as e:
        return jsonify({'success': False, 'message': 'Ошибка сервера'}), 500


@sketches_bp.route('/sketches/book', methods=['POST', 'OPTIONS'])
def book_sketch():
    """Бронирование эскиза"""
    if request.method == 'OPTIONS':
        return '', 200
    
    try:
        data = request.form if request.form else request.get_json()
        
        sketch_id = data.get('sketch_id')
        client_name = data.get('client_name')
        client_phone = data.get('client_phone')
        client_email = data.get('client_email')
        booking_date = data.get('booking_date')
        
        if not sketch_id or not client_name or not client_phone:
            return jsonify({'success': False, 'message': 'Заполните обязательные поля'}), 400
        
        # Проверяем существование эскиза
        sketch = Sketch.query.get(sketch_id)
        if not sketch or sketch.is_booked:
            return jsonify({'success': False, 'message': 'Эскиз недоступен'}), 400
        
        # Создаем заявку
        booking = SketchBooking(
            sketch_id=sketch_id,
            client_name=client_name,
            client_phone=client_phone,
            client_email=client_email,
            booking_date=booking_date if booking_date else None
        )
        
        # Помечаем эскиз как забронированный
        sketch.is_booked = True
        
        db.session.add(booking)
        db.session.commit()
        
        return jsonify({'success': True, 'message': 'Заявка успешно создана'})
    
    except Exception as e:
        db.session.rollback()
        return jsonify({'success': False, 'message': 'Ошибка сервера'}), 500
