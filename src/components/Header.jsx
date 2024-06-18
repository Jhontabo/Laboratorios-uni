import React from "react";
import {
  RiNotification3Line,
  RiArrowDownSLine,
  RiSettings3Line,
  RiLogoutCircleRLine,
  RiThumbUpLine,
  RiChat3Line,
} from "react-icons/ri";
import { Menu, MenuItem, MenuButton } from "@szhsin/react-menu";
import "@szhsin/react-menu/dist/index.css";
import "@szhsin/react-menu/dist/transitions/slide.css";
import { Link } from "react-router-dom";
import { UserAuth } from "../context/AuthContext";

const Header = () => {
  const { user, logOut } = UserAuth();

  const cerrarSesión = async () => {
    try {
      await logOut();
    } catch (error) {
      console.log(error);
    }
  };

  return (
    <header className="h-[7vh] md:h-[10vh] border-b border-gray-300 p-8 flex items-center justify-end">
      <nav className="flex items-center gap-2">
        <Menu
          menuButton={
            <MenuButton className="relative hover:bg-gray-200 p-2 rounded-lg transition-colors">
              <RiNotification3Line />
              <span className="absolute -top-0.5 right-0 bg-primary py-0.5 px-[5px] box-content text-black rounded-full text-[8px] font-bold">
                2
              </span>
            </MenuButton>
          }
          align="end"
          arrow
          transition
          arrowClassName="bg-gray-200"
          menuClassName="bg-white p-4 shadow-md"
        >
          <h1 className="text-gray-700 text-center font-medium">
            Notificaciones (2)
          </h1>
          <hr className="my-6 border-gray-300" />
          <MenuItem className="p-0 hover:bg-transparent">
            <Link
              to="/"
              className="text-gray-700 flex flex-1 items-center gap-4 py-2 px-4 hover:bg-gray-200 transition-colors rounded-lg"
            >
              <img
                src="https://img.freepik.com/foto-gratis/feliz-optimista-guapo-gerente-ventas-latina-apuntando-lado-mirando-camara_1262-12679.jpg"
                className="w-8 h-8 object-cover rounded-full"
              />
              <div className="text-sm flex flex-col">
                <div className="flex items-center justify-between gap-4">
                  <span>Jorge Luis Trejo</span>{" "}
                  <span className="text-[8px]">21/10/2022</span>
                </div>
                <p className="text-gray-600 text-xs">
                  Lorem ipsum dolor sit amet...
                </p>
              </div>
            </Link>
          </MenuItem>
          <MenuItem className="p-0 hover:bg-transparent">
            <Link
              to="/"
              className="text-gray-700 flex flex-1 items-center gap-4 py-2 px-4 hover:bg-gray-200 transition-colors rounded-lg"
            >
              <RiThumbUpLine className="p-2 bg-blue-500 text-white box-content rounded-full" />
              <div className="text-sm flex flex-col">
                <div className="flex items-center justify-between gap-4">
                  <span>Nuevo like</span>{" "}
                  <span className="text-[8px]">21/10/2022</span>
                </div>
                <p className="text-gray-600 text-xs">
                  A Jorge Trejo le gusta tu pub...
                </p>
              </div>
            </Link>
          </MenuItem>
          <MenuItem className="p-0 hover:bg-transparent">
            <Link
              to="/"
              className="text-gray-700 flex flex-1 items-center gap-4 py-2 px-4 hover:bg-gray-200 transition-colors rounded-lg"
            >
              <RiChat3Line className="p-2 bg-yellow-500 text-white box-content rounded-full" />
              <div className="text-sm flex flex-col">
                <div className="flex items-center justify-between gap-4">
                  <span>Nuevo comentario</span>{" "}
                  <span className="text-[8px]">21/10/2022</span>
                </div>
                <p className="text-gray-600 text-xs">
                  Jorge Trejo ha comentado tu...
                </p>
              </div>
            </Link>
          </MenuItem>
          <hr className="my-6 border-gray-300" />
          <MenuItem className="p-0 hover:bg-transparent flex justify-center cursor-default">
            <Link
              to="/"
              className="text-gray-600 text-sm hover:text-gray-800 transition-colors"
            >
              Todas las notificaciones
            </Link>
          </MenuItem>
        </Menu>
        <Menu
          menuButton={
            <MenuButton className="flex items-center gap-x-2 hover:bg-gray-200 p-2 rounded-lg transition-colors">
              <img
                src={user.photoURL}
                className="w-6 h-6 object-cover rounded-full"
              />
              <span>{user.displayName}</span>
              <RiArrowDownSLine />
            </MenuButton>
          }
          align="end"
          arrow
          arrowClassName="bg-gray-200"
          transition
          menuClassName="bg-white p-4 shadow-md"
        >
          <MenuItem className="p-0 hover:bg-transparent">
            <Link
              to="/perfil"
              className="rounded-lg transition-colors text-gray-700 hover:bg-gray-200 flex items-center gap-x-4 py-2 px-6 flex-1"
            >
              <img
                src={user.photoURL} className="w-8 h-8 object-cover rounded-full"
              />
              <div className="flex flex-col text-sm">
                <span className="text-sm">{user.displayName}</span>
                <span className="text-xs text-gray-600">{user.email}</span>
              </div>
            </Link>
          </MenuItem>
          <hr className="my-4 border-gray-300" />

          <MenuItem className="p-0 hover:bg-transparent">
            <Link
              to="/configuracion"
              className="rounded-lg transition-colors text-gray-700 hover:bg-gray-200 flex items-center gap-x-4 py-2 px-6 flex-1"
            >
              <RiSettings3Line /> Configuración
            </Link>
          </MenuItem>

          <MenuItem className="p-0 hover:bg-transparent">
            <button
              onClick={cerrarSesión}
              className="rounded-lg transition-colors text-gray-700 hover:bg-gray-200 flex items-center gap-x-4 py-2 px-6 flex-1"
            >
              <RiLogoutCircleRLine /> Cerrar sesión
            </button>
          </MenuItem>
        </Menu>
      </nav>
    </header>
  );
};

export default Header;