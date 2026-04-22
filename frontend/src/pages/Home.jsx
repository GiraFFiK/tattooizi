import { Link } from 'react-router-dom';
import './Home.css';

function Home() {
  return (
    <div className="home">
      {/* Hero Section */}
      <section className="hero">
        <div className="container hero-content">
          <h1 className="hero-title">tattooizi</h1>
          <p className="hero-subtitle">тату студия на питерский лад</p>
          <Link to="/masters" className="btn">Наши мастера</Link>
        </div>
      </section>

      {/* About Section */}
      <section className="section about">
        <div className="container">
          <h2 className="section-title">О студии</h2>
          <div className="about-content">
            <p>
              Мы — команда профессиональных тату-мастеров, создающих уникальные работы 
              с вниманием к деталям и индивидуальным подходом к каждому клиенту.
            </p>
            <p>
              Наша студия работает с использованием современного оборудования и 
              качественных материалов, обеспечивая безопасность и комфорт во время процедуры.
            </p>
          </div>
        </div>
      </section>

      {/* Masters Preview */}
      <section className="section masters-preview">
        <div className="container">
          <h2 className="section-title">Наши мастера</h2>
          <div className="grid masters-grid">
            {[1, 2, 3].map((item) => (
              <Link key={item} to="/masters" className="master-card-preview">
                <div className="master-photo-placeholder">
                  <span>Фото мастера</span>
                </div>
                <h3 className="master-name">Мастер {item}</h3>
                <p className="master-specialty">Татуировщик</p>
              </Link>
            ))}
          </div>
          <div className="text-center" style={{ marginTop: '40px' }}>
            <Link to="/masters" className="btn btn-outline">Все мастера</Link>
          </div>
        </div>
      </section>

      {/* Sketches Preview */}
      <section className="section sketches-preview" style={{ backgroundColor: 'white' }}>
        <div className="container">
          <h2 className="section-title">Готовые эскизы</h2>
          <p className="text-center" style={{ marginBottom: '30px', color: '#666' }}>
            Выберите готовый эскиз и запишитесь на сеанс
          </p>
          <div className="text-center">
            <Link to="/sketches" className="btn">Смотреть эскизы</Link>
          </div>
        </div>
      </section>

      {/* Info Section */}
      <section className="section info-section">
        <div className="container">
          <div className="grid info-grid">
            <div className="info-card">
              <h3>Курсы</h3>
              <p>Обучение от профессиональных мастеров</p>
              <Link to="/courses" className="btn btn-outline" style={{ marginTop: '15px' }}>Подробнее</Link>
            </div>
            <div className="info-card">
              <h3>Мерч</h3>
              <p>Фирменная продукция студии</p>
              <Link to="/merch" className="btn btn-outline" style={{ marginTop: '15px' }}>В магазин</Link>
            </div>
            <div className="info-card">
              <h3>Блог</h3>
              <p>Статьи о татуировках и уходе</p>
              <Link to="/blog" className="btn btn-outline" style={{ marginTop: '15px' }}>Читать</Link>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}

export default Home;
