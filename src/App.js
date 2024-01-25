import React from 'react';
import { BrowserRouter, Route, Routes } from 'react-router-dom';
import Navbar from './components/navbar.jsx';
import Home from './components/home.jsx';
import Schedule from './components/schedule.jsx';
import Reservations from './components/reservations.jsx';
import Equipos from './components/equipos.jsx';
import Chat from './components/chat.jsx';
import Logout from './components/logout.jsx';
import './App.css'

const App = () => {
  return (
    <BrowserRouter>
      <Navbar />
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/Schedule" element={<Schedule />} />
        <Route path="/Reservations" element={<Reservations />} />
        <Route path="/Equipos" element={<Equipos />} />
        <Route path="/Chat" element={<Chat />} />
        <Route path="/Logout" element={<Logout />} />
      </Routes>
    </BrowserRouter>
  );
};

export default App;
