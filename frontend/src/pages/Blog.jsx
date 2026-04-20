import { useEffect, useState } from 'react';
import './Blog.css';

function Blog() {
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Для демонстрации используем тестовые данные
    const mockPosts = [
      { 
        id: 1, 
        title: 'Как подготовиться к первому сеансу татуировки', 
        excerpt: 'Советы и рекомендации для тех, кто впервые собирается сделать тату.',
        content: 'Полный текст статьи...',
        date: '2024-01-15',
        author: 'Алексей Иванов',
        photo: null
      },
      { 
        id: 2, 
        title: 'Уход за татуировкой после нанесения', 
        excerpt: 'Правильный уход — залог яркости и долговечности вашей татуировки.',
        content: 'Полный текст статьи...',
        date: '2024-01-10',
        author: 'Мария Петрова',
        photo: null
      },
      { 
        id: 3, 
        title: 'Популярные стили татуировок в 2024 году', 
        excerpt: 'Обзор трендов и направлений в мире тату-искусства.',
        content: 'Полный текст статьи...',
        date: '2024-01-05',
        author: 'Дмитрий Сидоров',
        photo: null
      },
    ];
    
    setPosts(mockPosts);
    setLoading(false);
  }, []);

  if (loading) {
    return <div className="loading">Загрузка...</div>;
  }

  return (
    <div className="blog-page">
      <section className="section">
        <div className="container">
          <h1 className="section-title">Блог</h1>
          <p className="blog-intro">
            Статьи о татуировках, уходе и всём, что связано с тату-искусством.
          </p>
          
          <div className="grid blog-grid">
            {posts.map((post) => (
              <article key={post.id} className="blog-card">
                <div className="blog-photo">
                  {post.photo ? (
                    <img src={`/backend/uploads/${post.photo}`} alt={post.title} />
                  ) : (
                    <div className="blog-photo-placeholder">
                      <span>Изображение</span>
                    </div>
                  )}
                </div>
                <div className="blog-info">
                  <div className="blog-meta">
                    <span className="blog-date">{new Date(post.date).toLocaleDateString('ru-RU')}</span>
                    <span className="blog-author">{post.author}</span>
                  </div>
                  <h3 className="blog-title">{post.title}</h3>
                  <p className="blog-excerpt">{post.excerpt}</p>
                  <button className="btn btn-outline btn-small">Читать далее</button>
                </div>
              </article>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}

export default Blog;
