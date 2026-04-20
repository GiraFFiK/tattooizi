import { Link, useLocation } from 'react-router-dom';
import './Header.css';

function Header() {
  const location = useLocation();

  const navLinks = [
    { path: '/', label: 'Главная' },
    { path: '/masters', label: 'Мастера' },
    { path: '/sketches', label: 'Мгновенное бронирование' },
    { path: '/courses', label: 'Курсы' },
    { path: '/merch', label: 'Мерч' },
    { path: '/blog', label: 'Блог' },
    { path: '/contacts', label: 'Контакты' },
  ];

  return (
    <header className="header">
      <div className="container header-container">
        <Link to="/" className="logo">
          TATTOO STUDIO
        </Link>
        
        <nav className="nav">
          {navLinks.map((link) => (
            <Link
              key={link.path}
              to={link.path}
              className={`nav-link ${location.pathname === link.path ? 'active' : ''}`}
            >
              {link.label}
            </Link>
          ))}
        </nav>
      </div>
    </header>
  );
}

export default Header;
