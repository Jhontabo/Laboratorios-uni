import React from 'react';
import { Link } from 'react-router-dom';
import { FaHome, FaCalendar, FaClipboard, FaTools, FaComment, FaDoorClosed } from 'react-icons/fa';
import '../styleSheets/navbarItem.css'
const Navbar = () => {
  const iconMap = {
    home: <FaHome />,
    calendar: <FaCalendar />,
    clipboard: <FaClipboard />,
    tools: <FaTools />,
    comment: <FaComment />,
    'door-closed': <FaDoorClosed />,
  };

  const renderIcon = (icon) => iconMap[icon] || null;

  return (
    <nav className='navbar'>
      <Link to="/" className="navbar-logo">{renderIcon("home")} Inicio</Link>
      <ul>
        <li>
          <Link className='menu-a' to="/">
            {renderIcon("home")} Inicio
          </Link>
        </li>
        <li>
          <Link className='menu-a' to="/schedule">
            {renderIcon("calendar")} Horarios
          </Link>
        </li>
        <li>
          <Link className='menu-a' to="/reservations">
            {renderIcon("clipboard")} Reservas
          </Link>
        </li>
        <li>
          <Link className='menu-a' to="/equipos">
            {renderIcon("tools")} Equipos
          </Link>
        </li>
        <li>
          <a className='menu-a' href="https://wa.me/+573235937501" target="_blank" rel="noopener noreferrer">
            {renderIcon("comment")} Chat
          </a>
        </li>
        <li>
          <Link className='menu-a' to="/logout">
            {renderIcon("door-closed")} Cerrar sesión
          </Link>
        </li>
      </ul>
    </nav>
  );
};

export default Navbar;
