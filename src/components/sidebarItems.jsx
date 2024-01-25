import React from 'react';
import '../styleSheets/sidebarItems.css'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

const SidebarItem = (props) => {
  return (
    <li className="sidebar-item">
      <a href={props.link} className="sidebar-link" id={props.id}>
        <FontAwesomeIcon icon={props.icon} />
        {props.label}
      </a>
    </li>
  );
}

export default SidebarItem;
