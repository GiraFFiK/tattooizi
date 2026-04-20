import { useEffect, useState } from 'react';
import './Courses.css';

function Courses() {
  const [courses, setCourses] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Для демонстрации используем тестовые данные
    const mockCourses = [
      { 
        id: 1, 
        title: 'Основы татуировки', 
        description: 'Базовый курс для начинающих мастеров. Вы изучите основы безопасности, работу с оборудованием и техники нанесения татуировок.',
        price: 50000,
        duration: '4 недели',
        master_name: 'Алексей Иванов',
        photo: null
      },
      { 
        id: 2, 
        title: 'Художественная татуировка', 
        description: 'Продвинутый курс по созданию художественных татуировок. Работа с цветом, композицией и стилями.',
        price: 75000,
        duration: '6 недель',
        master_name: 'Мария Петрова',
        photo: null
      },
      { 
        id: 3, 
        title: 'Реализм в тату', 
        description: 'Специализированный курс по реалистичным татуировкам. Портреты, животные, детализация.',
        price: 90000,
        duration: '8 недель',
        master_name: 'Дмитрий Сидоров',
        photo: null
      },
    ];
    
    setCourses(mockCourses);
    setLoading(false);
  }, []);

  const handleBuy = (course) => {
    // Здесь будет интеграция с ЮMoney
    alert(`Переход к оплате курса "${course.title}"\nЦена: ${course.price.toLocaleString()} ₽`);
  };

  if (loading) {
    return <div className="loading">Загрузка...</div>;
  }

  return (
    <div className="courses-page">
      <section className="section">
        <div className="container">
          <h1 className="section-title">Курсы обучения</h1>
          <p className="courses-intro">
            Обучайтесь у профессиональных мастеров нашей студии. Практические занятия, индивидуальная программа и поддержка после окончания курса.
          </p>
          
          <div className="grid courses-grid">
            {courses.map((course) => (
              <div key={course.id} className="course-card">
                <div className="course-photo">
                  {course.photo ? (
                    <img src={`/backend/uploads/${course.photo}`} alt={course.title} />
                  ) : (
                    <div className="course-photo-placeholder">
                      <span>Фото курса</span>
                    </div>
                  )}
                </div>
                <div className="course-info">
                  <h3 className="course-title">{course.title}</h3>
                  <p className="course-master">Преподаватель: {course.master_name}</p>
                  <p className="course-duration">Длительность: {course.duration}</p>
                  <p className="course-description">{course.description}</p>
                  <div className="course-footer">
                    <span className="course-price">{course.price.toLocaleString()} ₽</span>
                    <button 
                      className="btn"
                      onClick={() => handleBuy(course)}
                    >
                      Купить курс
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

export default Courses;
