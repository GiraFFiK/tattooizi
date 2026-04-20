import { useEffect, useState } from 'react';
import './Merch.css';

function Merch() {
  const [merch, setMerch] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Для демонстрации используем тестовые данные
    const mockMerch = [
      { 
        id: 1, 
        title: 'Футболка Studio Logo', 
        description: 'Классическая футболка с логотипом студии. 100% хлопок.',
        price: 2500,
        stock: 15,
        photo: null
      },
      { 
        id: 2, 
        title: 'Худи Black Edition', 
        description: 'Стильное худи чёрного цвета с минималистичным принтом.',
        price: 5500,
        stock: 8,
        photo: null
      },
      { 
        id: 3, 
        title: 'Кепка Tattoo Studio', 
        description: 'Бейсболка с вышитым логотипом. Регулируемый размер.',
        price: 1500,
        stock: 20,
        photo: null
      },
      { 
        id: 4, 
        title: 'Набор стикеров', 
        description: 'Коллекция виниловых стикеров с авторскими эскизами.',
        price: 500,
        stock: 50,
        photo: null
      },
    ];
    
    setMerch(mockMerch);
    setLoading(false);
  }, []);

  const handleBuy = (item) => {
    // Здесь будет интеграция с ЮMoney
    alert(`Переход к оплате товара "${item.title}"\nЦена: ${item.price.toLocaleString()} ₽`);
  };

  if (loading) {
    return <div className="loading">Загрузка...</div>;
  }

  return (
    <div className="merch-page">
      <section className="section">
        <div className="container">
          <h1 className="section-title">Мерч студии</h1>
          <p className="merch-intro">
            Фирменная продукция нашей студии. Качественные материалы и уникальный дизайн.
          </p>
          
          <div className="grid merch-grid">
            {merch.map((item) => (
              <div key={item.id} className="merch-card">
                <div className="merch-photo">
                  {item.photo ? (
                    <img src={`/backend/uploads/${item.photo}`} alt={item.title} />
                  ) : (
                    <div className="merch-photo-placeholder">
                      <span>Фото товара</span>
                    </div>
                  )}
                </div>
                <div className="merch-info">
                  <h3 className="merch-title">{item.title}</h3>
                  <p className="merch-description">{item.description}</p>
                  <div className="merch-footer">
                    <div className="merch-price-stock">
                      <span className="merch-price">{item.price.toLocaleString()} ₽</span>
                      {item.stock <= 5 && (
                        <span className="merch-stock-low">Осталось мало</span>
                      )}
                    </div>
                    <button 
                      className="btn"
                      onClick={() => handleBuy(item)}
                      disabled={item.stock === 0}
                    >
                      {item.stock === 0 ? 'Нет в наличии' : 'Купить'}
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}

export default Merch;
