"""
Маршруты админ-панели
"""
from flask import Blueprint, render_template, request, redirect, url_for, session, flash, current_app
from werkzeug.utils import secure_filename
import os
from models import db, Admin, Master, MasterWork, Sketch, Course, Merch, BlogPost, SketchBooking

admin_bp = Blueprint('admin', __name__, template_folder='../templates/admin')


def login_required(f):
    """Декоратор для проверки авторизации"""
    from functools import wraps
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if 'admin_id' not in session:
            return redirect(url_for('admin.login'))
        return f(*args, **kwargs)
    return decorated_function


@admin_bp.route('/admin/login', methods=['GET', 'POST'])
def login():
    """Вход в админ-панель"""
    if request.method == 'POST':
        username = request.form.get('username', '')
        password = request.form.get('password', '')
        
        if not username or not password:
            flash('Введите логин и пароль', 'error')
            return render_template('admin/login.html')
        
        admin = Admin.query.filter_by(username=username).first()
        
        if admin and admin.check_password(password):
            session['admin_id'] = admin.id
            session['admin_username'] = admin.username
            return redirect(url_for('admin.dashboard'))
        else:
            flash('Неверный логин или пароль', 'error')
    
    return render_template('admin/login.html')


@admin_bp.route('/admin/logout')
def logout():
    """Выход из админ-панели"""
    session.pop('admin_id', None)
    session.pop('admin_username', None)
    return redirect(url_for('admin.login'))


@admin_bp.route('/admin')
@admin_bp.route('/admin/dashboard')
@login_required
def dashboard():
    """Главная панель управления"""
    masters_count = Master.query.count()
    sketches_count = Sketch.query.filter_by(is_booked=False).count()
    courses_count = Course.query.count()
    merch_count = Merch.query.count()
    blog_count = BlogPost.query.count()
    
    return render_template('admin/dashboard.html',
                         masters_count=masters_count,
                         sketches_count=sketches_count,
                         courses_count=courses_count,
                         merch_count=merch_count,
                         blog_count=blog_count)


@admin_bp.route('/admin/masters', methods=['GET', 'POST'])
@login_required
def masters():
    """Управление мастерами"""
    if request.method == 'POST':
        action = request.form.get('action', '')
        
        if action in ['add', 'update']:
            name = request.form.get('name', '')
            description = request.form.get('description', '')
            instagram = request.form.get('instagram', '')
            photo = request.form.get('existing_photo', '')
            
            # Загрузка нового фото
            if 'photo' in request.files:
                file = request.files['photo']
                if file and file.filename:
                    filename = secure_filename(file.filename)
                    unique_filename = f"{os.urandom(16).hex()}.{filename.split('.')[-1]}"
                    filepath = os.path.join(current_app.config['UPLOAD_FOLDER'], 'masters', unique_filename)
                    file.save(filepath)
                    photo = f"masters/{unique_filename}"
            
            if action == 'add':
                master = Master(name=name, description=description, photo=photo, instagram=instagram)
                db.session.add(master)
                flash('Мастер успешно добавлен', 'success')
            else:
                master_id = request.form.get('id')
                master = Master.query.get(master_id)
                if master:
                    master.name = name
                    master.description = description
                    master.photo = photo
                    master.instagram = instagram
                    flash('Мастер успешно обновлен', 'success')
        
        elif action == 'delete':
            master_id = request.form.get('id')
            master = Master.query.get(master_id)
            if master:
                db.session.delete(master)
                flash('Мастер успешно удален', 'success')
        
        elif action == 'add_work':
            master_id = request.form.get('master_id')
            work_description = request.form.get('work_description', '')
            
            if 'work_photo' in request.files:
                file = request.files['work_photo']
                if file and file.filename:
                    filename = secure_filename(file.filename)
                    unique_filename = f"{os.urandom(16).hex()}.{filename.split('.')[-1]}"
                    filepath = os.path.join(current_app.config['UPLOAD_FOLDER'], 'masters', unique_filename)
                    file.save(filepath)
                    
                    work = MasterWork(master_id=master_id, photo=f"masters/{unique_filename}", description=work_description)
                    db.session.add(work)
                    flash('Работа успешно добавлена', 'success')
        
        db.session.commit()
    
    masters = Master.query.order_by(Master.created_at.desc()).all()
    return render_template('admin/masters.html', masters=masters)


@admin_bp.route('/admin/bookings')
@login_required
def bookings():
    """Просмотр заявок на бронирование"""
    bookings = SketchBooking.query.order_by(SketchBooking.created_at.desc()).all()
    return render_template('admin/bookings.html', bookings=bookings)
