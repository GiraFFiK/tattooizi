import './Contacts.css';

function Contacts() {
  return (
    <div className="contacts-page">
      <section className="section">
        <div className="container">
          <h1 className="section-title">Контакты</h1>
          
          <div className="contacts-grid">
            <div className="contact-info">
              <h2 className="contact-subtitle">Информация</h2>
              
              <div className="contact-item">
                <h3>Адрес</h3>
                <p>г. Челябинск, Свердловский проспект 78</p>
              </div>
              
              <div className="contact-item">
                <h3>Телефон</h3>
                <p>+7 (999) 000-00-00</p>
              </div>
              
              <div className="contact-item">
                <h3>Email</h3>
                <p>info@tattooizi.ru</p>
              </div>
              
              <div className="contact-item">
                <h3>Режим работы</h3>
                <p>Ежедневно с 12:00 до 22:00</p>
              </div>
              
              <div className="contact-socials">
                <h3>Социальные сети</h3>
                <div className="social-links">
                  <a href="#" className="social-link">Instagram</a>
                  <a href="#" className="social-link">Telegram</a>
                  <a href="#" className="social-link">VK</a>
                </div>
              </div>
            </div>
            
            <div className="contact-map">
              <iframe 
                src="https://yandex.ru/map-widget/v1/?um=constructor%3A0&amp;source=constructor&amp;text=Челябинск%2C%20Свердловский%20проспект%2078" 
                width="100%" 
                height="400" 
                frameBorder="0"
                allowFullScreen={true}
                title="Карта tattooizi"
                style={{ borderRadius: '8px' }}
              ></iframe>
            </div>
          </div>
          
          <div className="contact-form-section">
            <h2 className="contact-subtitle">Напишите нам</h2>
            <form className="contact-form">
              <div className="form-row">
                <div className="form-group">
                  <label htmlFor="name">Ваше имя</label>
                  <input type="text" id="name" name="name" required />
                </div>
                <div className="form-group">
                  <label htmlFor="phone">Телефон</label>
                  <input type="tel" id="phone" name="phone" required />
                </div>
              </div>
              <div className="form-group">
                <label htmlFor="email">Email</label>
                <input type="email" id="email" name="email" />
              </div>
              <div className="form-group">
                <label htmlFor="message">Сообщение</label>
                <textarea id="message" name="message" rows="5" required></textarea>
              </div>
              <button type="submit" className="btn">Отправить</button>
            </form>
          </div>
        </div>
      </section>
    </div>
  );
}

export default Contacts;
