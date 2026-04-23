"""
API маршруты для магазина (курсы и мерч)
"""
import uuid
from flask import Blueprint, request, jsonify, current_app
from models import db, Course, Merch, Payment

shop_bp = Blueprint('shop', __name__)


@shop_bp.route('/shop/courses', methods=['GET', 'OPTIONS'])
def get_courses():
    """Получение всех курсов"""
    if request.method == 'OPTIONS':
        return '', 200
    
    try:
        courses = Course.query.order_by(Course.created_at.desc()).all()
        result = [c.to_dict(include_master=True) for c in courses]
        return jsonify({'success': True, 'data': result})
    
    except Exception as e:
        return jsonify({'success': False, 'message': 'Ошибка сервера'}), 500


@shop_bp.route('/shop/merch', methods=['GET', 'OPTIONS'])
def get_merch():
    """Получение всего мерча"""
    if request.method == 'OPTIONS':
        return '', 200
    
    try:
        merch = Merch.query.filter(Merch.stock > 0).order_by(Merch.created_at.desc()).all()
        return jsonify({'success': True, 'data': [m.to_dict() for m in merch]})
    
    except Exception as e:
        return jsonify({'success': False, 'message': 'Ошибка сервера'}), 500


@shop_bp.route('/shop/create_payment', methods=['POST', 'OPTIONS'])
def create_payment():
    """Создание платежа через ЮMoney"""
    if request.method == 'OPTIONS':
        return '', 200
    
    try:
        data = request.form if request.form else request.get_json()
        
        payment_type = data.get('payment_type')  # course, merch, sketch
        item_id = data.get('item_id')
        amount = data.get('amount')
        
        if not payment_type or not item_id or not amount:
            return jsonify({'success': False, 'message': 'Некорректные данные'}), 400
        
        # Генерируем уникальный ID заказа
        order_id = f"order_{uuid.uuid4().hex[:12]}"
        
        # Сохраняем платеж в базу
        payment = Payment(
            order_id=order_id,
            amount=amount,
            payment_type=payment_type,
            item_id=item_id
        )
        db.session.add(payment)
        db.session.commit()
        
        # Формируем данные для ЮMoney
        config = current_app.config
        payment_data = {
            'shop_id': config.get('YUMONEY_SHOP_ID', ''),
            'order_id': order_id,
            'amount': amount,
            'currency': 'RUB',
            'description': f'Оплата {"курса" if payment_type == "course" else ("мерча" if payment_type == "merch" else "эскиза")}',
            'success_url': f"{config.get('SITE_URL', '')}/payment-success.html",
            'fail_url': f"{config.get('SITE_URL', '')}/payment-fail.html"
        }
        
        # В реальном проекте здесь будет запрос к API ЮMoney
        # Для демонстрации возвращаем тестовые данные
        from urllib.parse import urlencode
        payment_url = f"https://yoomoney.ru/quickpay/confirm?{urlencode(payment_data)}"
        
        return jsonify({
            'success': True,
            'payment_url': payment_url,
            'order_id': order_id
        })
    
    except Exception as e:
        db.session.rollback()
        return jsonify({'success': False, 'message': 'Ошибка сервера'}), 500
