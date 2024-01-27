import React, { useState } from 'react';
import { BrowserRouter, Route, Routes, Navigate } from 'react-router-dom';
import Navbar from './components/navbar.jsx';
import Home from './components/home.jsx';
import Schedule from './components/schedule.jsx';
import Reservations from './components/reservations.jsx';
import Equipos from './components/equipos.jsx';
import Chat from './components/chat.jsx';
import Logout from './components/logout.jsx';
import Settings from './components/settings.jsx';
import { Login } from './components/login.jsx';
import './App.css';
import '../src/styleSheets/contenidoPagina.css';

const App = () => {
  const [isLoggedIn, setIsLoggedIn] = useState(false);

  return (
    <BrowserRouter>
      <div className={isLoggedIn ? 'principal' : ''}>
        {isLoggedIn && <Navbar />}
        <Routes>
          <Route
            path="/"
            element={
              <>
                {isLoggedIn ? <Navigate to="/home" /> : <Login onLogin={() => setIsLoggedIn(true)} />}
              </>
            }
          />
          <Route path="/home" element={<Home />} />
          <Route path="/schedule" element={<Schedule />} />
          <Route path="/reservations" element={<Reservations />} />
          <Route path="/equipos" element={<Equipos />} />
          <Route path="/chat" element={<Chat />} />
          <Route path="/logout" element={<Logout onLogout={() => setIsLoggedIn(false)} />} />
          <Route path="/settings" element={<Settings />} />
        </Routes>
      </div>
    </BrowserRouter>
  );
};

export default App;
