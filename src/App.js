// App.js
import React from 'react';
import './App.css';
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faHome, faCalendar, faClipboard, faComment, faTools, faDoorClosed, faDoorOpen } from '@fortawesome/free-solid-svg-icons';
import SidebarItem from './components/sidebarItems.jsx';
import dataSidebarItems from './components/dataSidebarItems';

library.add(faHome, faCalendar, faClipboard, faComment, faTools, faDoorClosed, faDoorOpen);

const App = () => {
  return (

    <div className="App">

      <section className="wrapper row">
        <aside className="sidebar">
          <div className="sidebarItems">
            <div className="sidebar-logo">
              <a href="#" className="sidebar-link">
                <FontAwesomeIcon icon="door-open" />
              Bienvenido nombre usuario
              </a>
            </div>

            <ul className="sidebar-nav">
            {dataSidebarItems.map((testimonio, index) => (
              <SidebarItem key={index} {...testimonio} />))}
            </ul>

          </div>
        </aside>
      </section>
    </div>
  );
}

export default App;
