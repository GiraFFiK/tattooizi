import { Link } from 'react-router-dom';
import './Footer.css';

function Footer() {
  const currentYear = new Date().getFullYear();

  return (
    <footer className="footer">
      <div className="container footer-container">
        <div className="footer-section">
          <h3 className="footer-title">tattooizi</h3>
          <p className="footer-text">
            Тату студия на питерский лад
          </p>
        </div>

        <div className="footer-section">
          <h4 className="footer-subtitle">Навигация</h4>
          <ul className="footer-links">
            <li><Link to="/masters">Мастера</Link></li>
            <li><Link to="/sketches">Мгновенное бронирование</Link></li>
            <li><Link to="/courses">Курсы</Link></li>
            <li><Link to="/merch">Мерч</Link></li>
            <li><Link to="/blog">Блог</Link></li>
            <li><Link to="/contacts">Контакты</Link></li>
          </ul>
        </div>

        <div className="footer-section">
          <h4 className="footer-subtitle">Контакты</h4>
          <ul className="footer-contact">
            <li>г. Челябинск, Свердловский проспект 78</li>
            <li>+7 (999) 000-00-00</li>
            <li>info@tattoostudio.ru</li>
          </ul>
        </div>
      </div>

      <div className="container footer-bottom">
        <p>&copy; {currentYear} Tattoo Studio. Все права защищены.</p>
      </div>
    </footer>
  );
}

export default Footer;
