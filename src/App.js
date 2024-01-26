import React from 'react';
import { BrowserRouter, Route, Routes } from 'react-router-dom';
import Navbar from './components/navbar.jsx';
import Home from './components/home.jsx';
import Schedule from './components/schedule.jsx';
import Reservations from './components/reservations.jsx';
import Equipos from './components/equipos.jsx';
import Chat from './components/chat.jsx';
import Logout from './components/logout.jsx';
import Settings from './components/settings.jsx';
import './App.css'
import '../src/styleSheets/contenidoPagina.css'

const App = () => {
  return (
    <BrowserRouter>
    <div className='contenedor-principal'>
      <Navbar />
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/Schedule" element={<Schedule />} />
        <Route path="/Reservations" element={<Reservations />} />
        <Route path="/Equipos" element={<Equipos />} />
        <Route path="/Chat" element={<Chat />} />
        <Route path="/Logout" element={<Logout />} />
        <Route path="/Settings" element={<Settings />} />
      </Routes>
    </div>
    </BrowserRouter>
    
  );
};

export default App;
