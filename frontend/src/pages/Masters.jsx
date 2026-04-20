import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import './Masters.css';

const API_URL = '/backend/api/masters.php';

function Masters() {
  const [masters, setMasters] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // В реальном проекте здесь будет fetch запрос к API
    // Для демонстрации используем тестовые данные
    const mockMasters = [
      {
        id: 1,
        name: 'Алексей Иванов',
        description: 'Специализируюсь на реализме и портретах. Опыт работы более 5 лет.',
        photo: null,
        instagram: '@alex_tattoo'
      },
      {
        id: 2,
        name: 'Мария Петрова',
        description: 'Мастер художественной татуировки, работаю в стилях акварель и графика.',
        photo: null,
        instagram: '@maria_art'
      },
      {
        id: 3,
        name: 'Дмитрий Сидоров',
        description: 'Олд скул и нью скул татуировки. Индивидуальный подход к каждому клиенту.',
        photo: null,
        instagram: '@dim_tattoo'
      }
    ];
    
    // Раскомментируйте для работы с реальным API
    /*
    fetch(API_URL)
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          setMasters(data.data);
        }
      })
      .finally(() => setLoading(false));
    */
    
    setMasters(mockMasters);
    setLoading(false);
  }, []);

  if (loading) {
    return <div className="loading">Загрузка...</div>;
  }

  return (
    <div className="masters-page">
      <section className="section">
        <div className="container">
          <h1 className="section-title">Наши мастера</h1>
          
          <div className="grid masters-list">
            {masters.map((master) => (
              <Link to={`/masters/${master.id}`} key={master.id} className="master-card">
                <div className="master-card-image">
                  {master.photo ? (
                    <img src={`/backend/uploads/${master.photo}`} alt={master.name} />
                  ) : (
                    <div className="master-image-placeholder">
                      <span>Фото</span>
                    </div>
                  )}
                </div>
                <div className="master-card-info">
                  <h3 className="master-card-name">{master.name}</h3>
                  <p className="master-card-description">{master.description}</p>
                  {master.instagram && (
                    <p className="master-instagram">{master.instagram}</p>
                  )}
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}

export default Masters;
