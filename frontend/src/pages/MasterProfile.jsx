import { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import './MasterProfile.css';

function MasterProfile() {
  const { id } = useParams();
  const [master, setMaster] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Для демонстрации используем тестовые данные
    const mockMaster = {
      id: parseInt(id),
      name: 'Алексей Иванов',
      description: 'Специализируюсь на реализме и портретах. Опыт работы более 5 лет. В татуировке мне важно передать эмоции и характер через детали. Каждый эскиз разрабатываю индивидуально с учётом пожеланий клиента и анатомии тела.',
      photo: null,
      instagram: '@alex_tattoo',
      works: [
        { id: 1, photo: null, description: 'Портрет' },
        { id: 2, photo: null, description: 'Реализм' },
        { id: 3, photo: null, description: 'Чёрно-белая татуировка' },
        { id: 4, photo: null, description: 'Детализация' },
      ]
    };
    
    setMaster(mockMaster);
    setLoading(false);
  }, [id]);

  if (loading) {
    return <div className="loading">Загрузка...</div>;
  }

  if (!master) {
    return <div className="container section">Мастер не найден</div>;
  }

  return (
    <div className="master-profile-page">
      <section className="section master-header">
        <div className="container">
          <Link to="/masters" className="back-link">← Назад к мастерам</Link>
          
          <div className="master-profile-header">
            <div className="master-profile-photo">
              {master.photo ? (
                <img src={`/backend/uploads/${master.photo}`} alt={master.name} />
              ) : (
                <div className="master-photo-large-placeholder">
                  <span>Фото мастера</span>
                </div>
              )}
            </div>
            
            <div className="master-profile-info">
              <h1 className="master-profile-name">{master.name}</h1>
              {master.instagram && (
                <p className="master-profile-instagram">{master.instagram}</p>
              )}
              <p className="master-profile-description">{master.description}</p>
            </div>
          </div>
        </div>
      </section>

      <section className="section master-works">
        <div className="container">
          <h2 className="section-title">Работы мастера</h2>
          
          <div className="grid works-grid">
            {master.works && master.works.map((work) => (
              <div key={work.id} className="work-item">
                <div className="work-photo">
                  {work.photo ? (
                    <img src={`/backend/uploads/${work.photo}`} alt={work.description} />
                  ) : (
                    <div className="work-photo-placeholder">
                      <span>Фото работы</span>
                    </div>
                  )}
                </div>
                {work.description && (
                  <p className="work-description">{work.description}</p>
                )}
              </div>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}

export default MasterProfile;
