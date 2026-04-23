"""
Модели базы данных для Tattoo Studio
"""
from flask_sqlalchemy import SQLAlchemy
from datetime import datetime
from werkzeug.security import generate_password_hash, check_password_hash

db = SQLAlchemy()


class Master(db.Model):
    """Модель мастера тату-салона"""
    __tablename__ = 'masters'
    
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(255), nullable=False)
    description = db.Column(db.Text)
    photo = db.Column(db.String(255))
    instagram = db.Column(db.String(255))
    telegram = db.Column(db.String(255))
    vk = db.Column(db.String(255))
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    
    # Связи
    works = db.relationship('MasterWork', backref='master', lazy='dynamic', cascade='all, delete-orphan')
    courses = db.relationship('Course', backref='master', lazy='dynamic')
    blog_posts = db.relationship('BlogPost', backref='author', lazy='dynamic')
    
    def to_dict(self, include_works=False):
        """Конвертация модели в словарь"""
        data = {
            'id': self.id,
            'name': self.name,
            'description': self.description,
            'photo': self.photo,
            'instagram': self.instagram,
            'telegram': self.telegram,
            'vk': self.vk,
            'created_at': self.created_at.isoformat() if self.created_at else None
        }
        if include_works:
            data['works'] = [work.to_dict() for work in self.works.order_by(MasterWork.created_at.desc()).all()]
        return data


class MasterWork(db.Model):
    """Модель работы мастера"""
    __tablename__ = 'master_works'
    
    id = db.Column(db.Integer, primary_key=True)
    master_id = db.Column(db.Integer, db.ForeignKey('masters.id'), nullable=False)
    photo = db.Column(db.String(255), nullable=False)
    description = db.Column(db.Text)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    
    def to_dict(self):
        """Конвертация модели в словарь"""
        return {
            'id': self.id,
            'master_id': self.master_id,
            'photo': self.photo,
            'description': self.description,
            'created_at': self.created_at.isoformat() if self.created_at else None
        }


class Sketch(db.Model):
    """Модель эскиза"""
    __tablename__ = 'sketches'
    
    id = db.Column(db.Integer, primary_key=True)
    title = db.Column(db.String(255), nullable=False)
    description = db.Column(db.Text)
    photo = db.Column(db.String(255), nullable=False)
    price = db.Column(db.Numeric(10, 2))
    is_booked = db.Column(db.Boolean, default=False)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    
    bookings = db.relationship('SketchBooking', backref='sketch', lazy='dynamic', cascade='all, delete-orphan')
    
    def to_dict(self):
        """Конвертация модели в словарь"""
        return {
            'id': self.id,
            'title': self.title,
            'description': self.description,
            'photo': self.photo,
            'price': float(self.price) if self.price else None,
            'is_booked': self.is_booked,
            'created_at': self.created_at.isoformat() if self.created_at else None
        }


class Course(db.Model):
    """Модель курса"""
    __tablename__ = 'courses'
    
    id = db.Column(db.Integer, primary_key=True)
    title = db.Column(db.String(255), nullable=False)
    description = db.Column(db.Text)
    photo = db.Column(db.String(255))
    price = db.Column(db.Numeric(10, 2), nullable=False)
    master_id = db.Column(db.Integer, db.ForeignKey('masters.id'))
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    
    def to_dict(self, include_master=False):
        """Конвертация модели в словарь"""
        data = {
            'id': self.id,
            'title': self.title,
            'description': self.description,
            'photo': self.photo,
            'price': float(self.price) if self.price else None,
            'master_id': self.master_id,
            'created_at': self.created_at.isoformat() if self.created_at else None
        }
        if include_master and self.master:
            data['master_name'] = self.master.name
        return data


class Merch(db.Model):
    """Модель мерча"""
    __tablename__ = 'merch'
    
    id = db.Column(db.Integer, primary_key=True)
    title = db.Column(db.String(255), nullable=False)
    description = db.Column(db.Text)
    photo = db.Column(db.String(255))
    price = db.Column(db.Numeric(10, 2), nullable=False)
    stock = db.Column(db.Integer, default=0)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    
    def to_dict(self):
        """Конвертация модели в словарь"""
        return {
            'id': self.id,
            'title': self.title,
            'description': self.description,
            'photo': self.photo,
            'price': float(self.price) if self.price else None,
            'stock': self.stock,
            'created_at': self.created_at.isoformat() if self.created_at else None
        }


class BlogPost(db.Model):
    """Модель статьи блога"""
    __tablename__ = 'blog_posts'
    
    id = db.Column(db.Integer, primary_key=True)
    title = db.Column(db.String(255), nullable=False)
    content = db.Column(db.Text, nullable=False)
    photo = db.Column(db.String(255))
    author_id = db.Column(db.Integer, db.ForeignKey('masters.id'))
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    def to_dict(self):
        """Конвертация модели в словарь"""
        return {
            'id': self.id,
            'title': self.title,
            'content': self.content,
            'photo': self.photo,
            'author_id': self.author_id,
            'created_at': self.created_at.isoformat() if self.created_at else None,
            'updated_at': self.updated_at.isoformat() if self.updated_at else None
        }


class Admin(db.Model):
    """Модель администратора"""
    __tablename__ = 'admins'
    
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(100), unique=True, nullable=False)
    password_hash = db.Column(db.String(255), nullable=False)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    
    def set_password(self, password):
        """Установка пароля"""
        self.password_hash = generate_password_hash(password)
    
    def check_password(self, password):
        """Проверка пароля"""
        return check_password_hash(self.password_hash, password)
    
    def to_dict(self):
        """Конвертация модели в словарь (без пароля)"""
        return {
            'id': self.id,
            'username': self.username,
            'created_at': self.created_at.isoformat() if self.created_at else None
        }


class SketchBooking(db.Model):
    """Модель заявки на бронирование эскиза"""
    __tablename__ = 'sketch_bookings'
    
    id = db.Column(db.Integer, primary_key=True)
    sketch_id = db.Column(db.Integer, db.ForeignKey('sketches.id'), nullable=False)
    client_name = db.Column(db.String(255), nullable=False)
    client_phone = db.Column(db.String(50), nullable=False)
    client_email = db.Column(db.String(255))
    booking_date = db.Column(db.Date)
    status = db.Column(db.Enum('pending', 'confirmed', 'cancelled'), default='pending')
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    
    def to_dict(self):
        """Конвертация модели в словарь"""
        return {
            'id': self.id,
            'sketch_id': self.sketch_id,
            'client_name': self.client_name,
            'client_phone': self.client_phone,
            'client_email': self.client_email,
            'booking_date': self.booking_date.isoformat() if self.booking_date else None,
            'status': self.status,
            'created_at': self.created_at.isoformat() if self.created_at else None
        }


class Payment(db.Model):
    """Модель платежа"""
    __tablename__ = 'payments'
    
    id = db.Column(db.Integer, primary_key=True)
    order_id = db.Column(db.String(100), nullable=False)
    amount = db.Column(db.Numeric(10, 2), nullable=False)
    payment_type = db.Column(db.Enum('course', 'merch', 'sketch'), nullable=False)
    item_id = db.Column(db.Integer, nullable=False)
    status = db.Column(db.Enum('pending', 'completed', 'failed'), default='pending')
    yandex_payment_id = db.Column(db.String(255))
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    
    def to_dict(self):
        """Конвертация модели в словарь"""
        return {
            'id': self.id,
            'order_id': self.order_id,
            'amount': float(self.amount) if self.amount else None,
            'payment_type': self.payment_type,
            'item_id': self.item_id,
            'status': self.status,
            'yandex_payment_id': self.yandex_payment_id,
            'created_at': self.created_at.isoformat() if self.created_at else None
        }
