import React from 'react';
import { Link } from 'react-router-dom';
import { FaHome, FaCalendar, FaClipboard, FaTools, FaComment, FaDoorClosed, FaCog } from 'react-icons/fa';
import '../styleSheets/navbarItem.css';

const Navbar = () => {
  const iconMap = {
    home: <FaHome />,
    calendar: <FaCalendar />,
    clipboard: <FaClipboard />,
    tools: <FaTools />,
    comment: <FaComment />,
    'door-closed': <FaDoorClosed />,
    settings: <FaCog />, 
  };

  const renderIcon = (icon) => iconMap[icon] || null;

  return (
    <nav className='navbar'>
      <div className='navbar-logo-link'>
        <Link to="/" className="navbar-logo">{renderIcon("home")} Bienvenido Jhontabo</Link>
      </div>
      <ul>
        <li><Link className='menu-a' to="/">{renderIcon("home")} Inicio</Link></li>
        <li><Link className='menu-a' to="/schedule">{renderIcon("calendar")} Horarios</Link></li>
        <li><Link className='menu-a' to="/reservations">{renderIcon("clipboard")} Reservas</Link></li>
        <li><Link className='menu-a' to="/equipos">{renderIcon("tools")} Equipos</Link></li>
        <li><Link className='menu-a' to="/chat">{renderIcon("comment")} Chat</Link></li>
        <li><Link className='menu-a' to="/settings">{renderIcon("settings")} Ajustes</Link></li>
        <li><Link className='menu-a' to="/logout">{renderIcon("door-closed")} Cerrar sesión</Link></li>

        
      </ul>
    </nav>
  );
};

export default Navbar;