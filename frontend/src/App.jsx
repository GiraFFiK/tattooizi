import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Header from './components/Header';
import Footer from './components/Footer';
import Home from './pages/Home';
import Masters from './pages/Masters';
import MasterProfile from './pages/MasterProfile';
import Sketches from './pages/Sketches';
import Courses from './pages/Courses';
import Merch from './pages/Merch';
import Blog from './pages/Blog';
import Contacts from './pages/Contacts';

function App() {
  return (
    <Router>
      <div className="app">
        <Header />
        <main>
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/masters" element={<Masters />} />
            <Route path="/masters/:id" element={<MasterProfile />} />
            <Route path="/sketches" element={<Sketches />} />
            <Route path="/courses" element={<Courses />} />
            <Route path="/merch" element={<Merch />} />
            <Route path="/blog" element={<Blog />} />
            <Route path="/contacts" element={<Contacts />} />
          </Routes>
        </main>
        <Footer />
      </div>
    </Router>
  );
}

export default App;
