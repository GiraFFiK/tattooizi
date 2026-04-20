import { useEffect, useState } from 'react';
import './Sketches.css';

const API_URL = '/backend/api/sketches.php';

function Sketches() {
  const [sketches, setSketches] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedSketch, setSelectedSketch] = useState(null);
  const [formData, setFormData] = useState({
    client_name: '',
    client_phone: '',
    client_email: '',
    booking_date: ''
  });

  useEffect(() => {
    // Для демонстрации используем тестовые данные
    const mockSketches = [
      { id: 1, title: 'Геометрический лев', description: 'Стиль: геометрия, размер: 15x20 см', price: 5000, photo: null },
      { id: 2, title: 'Японский дракон', description: 'Стиль: ориентал, размер: 30x40 см', price: 15000, photo: null },
      { id: 3, title: 'Цветочный узор', description: 'Стиль: акварель, размер: 10x15 см', price: 4000, photo: null },
      { id: 4, title: 'Череп с розой', description: 'Стиль: олд скул, размер: 12x15 см', price: 6000, photo: null },
      { id: 5, title: 'Надпись на латыни', description: 'Стиль: леттеринг, размер: индивидуальный', price: 3000, photo: null },
      { id: 6, title: 'Лиса в лесу', description: 'Стиль: реализм, размер: 20x25 см', price: 12000, photo: null },
    ];
    
    setSketches(mockSketches);
    setLoading(false);
  }, []);

  const handleInputChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    // Здесь будет отправка данных на сервер
    alert('Заявка отправлена! Мы свяжемся с вами в ближайшее время.');
    setSelectedSketch(null);
    setFormData({
      client_name: '',
      client_phone: '',
      client_email: '',
      booking_date: ''
    });
  };

  if (loading) {
    return <div className="loading">Загрузка...</div>;
  }

  return (
    <div className="sketches-page">
      <section className="section">
        <div className="container">
          <h1 className="section-title">Мгновенное бронирование</h1>
          <p className="sketches-intro">
            Выберите готовый эскиз и запишитесь на сеанс. Все эскизы уникальны и доступны в единственном экземпляре.
          </p>
          
          <div className="grid sketches-grid">
            {sketches.map((sketch) => (
              <div key={sketch.id} className="sketch-card">
                <div className="sketch-photo">
                  {sketch.photo ? (
                    <img src={`/backend/uploads/${sketch.photo}`} alt={sketch.title} />
                  ) : (
                    <div className="sketch-photo-placeholder">
                      <span>Эскиз</span>
                    </div>
                  )}
                </div>
                <div className="sketch-info">
                  <h3 className="sketch-title">{sketch.title}</h3>
                  <p className="sketch-description">{sketch.description}</p>
                  <p className="sketch-price">{sketch.price.toLocaleString()} ₽</p>
                  <button 
                    className="btn"
                    onClick={() => setSelectedSketch(sketch)}
                  >
                    Забронировать
                  </button>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Модальное окно бронирования */}
      {selectedSketch && (
        <div className="modal-overlay" onClick={() => setSelectedSketch(null)}>
          <div className="modal-content" onClick={(e) => e.stopPropagation()}>
            <button className="modal-close" onClick={() => setSelectedSketch(null)}>×</button>
            <h2 className="modal-title">Бронирование эскиза</h2>
            <p className="modal-sketch-name">{selectedSketch.title}</p>
            <p className="modal-sketch-price">{selectedSketch.price.toLocaleString()} ₽</p>
            
            <form onSubmit={handleSubmit} className="booking-form">
              <div className="form-group">
                <label htmlFor="client_name">Ваше имя *</label>
                <input
                  type="text"
                  id="client_name"
                  name="client_name"
                  value={formData.client_name}
                  onChange={handleInputChange}
                  required
                />
              </div>
              
              <div className="form-group">
                <label htmlFor="client_phone">Телефон *</label>
                <input
                  type="tel"
                  id="client_phone"
                  name="client_phone"
                  value={formData.client_phone}
                  onChange={handleInputChange}
                  required
                />
              </div>
              
              <div className="form-group">
                <label htmlFor="client_email">Email</label>
                <input
                  type="email"
                  id="client_email"
                  name="client_email"
                  value={formData.client_email}
                  onChange={handleInputChange}
                />
              </div>
              
              <div className="form-group">
                <label htmlFor="booking_date">Желаемая дата</label>
                <input
                  type="date"
                  id="booking_date"
                  name="booking_date"
                  value={formData.booking_date}
                  onChange={handleInputChange}
                />
              </div>
              
              <button type="submit" className="btn btn-full">Отправить заявку</button>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}

export default Sketches;
