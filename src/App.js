import React from 'react';
import './App.css';
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faHome, faCalendar, faClipboard, faComment, faTools, faDoorClosed, faDoorOpen } from '@fortawesome/free-solid-svg-icons';
import NavbarItem from './components/navbarItem.jsx';
import DataNavbarItems from './components/dataNavbarItems.jsx';

library.add(faHome, faCalendar, faClipboard, faComment, faTools, faDoorClosed, faDoorOpen);

const App = () => {
  return (
    <nav className="navbar">
      <div className="navbarItems">
        <div className="navbar-logo">
          <a href="#" className="navbar-link">
            <FontAwesomeIcon icon="door-open" />
            Bienvenido nombre usuario
          </a>
        </div>

        <ul className="navbar-nav">
          {DataNavbarItems.map((item, index) => (
            <NavbarItem key={index} {...item} />
          ))}
        </ul>
      </div>
    </nav>
  );
}

export default App;
