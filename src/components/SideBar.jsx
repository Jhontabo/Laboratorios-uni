import React, { useState } from "react";
import { Link } from "react-router-dom";
import { UserAuth } from "../context/AuthContext";
import {
    RiBarChart2Line,
    RiEarthLine,
    RiCustomerService2Line,
    RiCalendarTodoLine,
    RiLogoutCircleRLine,
    RiArrowRightSLine,
    RiMenu3Line,
    RiCloseLine,
    RiFlaskLine,
    RiToolsLine,
    RiUserLine,
    RiFileListLine,
    RiNotificationLine,
    RiSettings3Line,
    RiMessage3Line
} from "react-icons/ri";

const Sidebar = () => {
    const { user, logOut } = UserAuth();

    const cerrarSesion = async () => {
        try {
            await logOut();
        } catch (error) {
            console.log(error);
        }
    };

    const [showMenu, setShowMenu] = useState(false);
    const [showSubmenu, setShowSubmenu] = useState(false);

    return (
        <>
            <div
                className={`xl:h-[100vh] overflow-y-scroll fixed xl:static w-[80%] md:w-[40%] lg:w-[30%] xl:w-auto h-full top-0 bg-gray-800 p-4 flex flex-col justify-between z-50 ${showMenu ? "left-0" : "-left-full"
                    } transition-all`}
            >
                <div>
                    <h1 className="text-center text-2xl font-bold text-white mb-10">
                        Admin<span className="text-primary text-4xl">.</span>
                    </h1>

                    <ul>
                        <li>
                            <Link
                                to="/"
                                className="flex items-center gap-4 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors text-white"
                            >
                                <RiBarChart2Line className="text-white" /> Dashboard
                            </Link>
                        </li>

                        <li>
                            <Link
                                to="/calendario"
                                className="flex items-center gap-4 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors text-white"
                            >
                                <RiCalendarTodoLine className="text-white" /> Calendario
                            </Link>
                        </li>
                        <li>
                            <Link
                                to="/laboratorios"
                                className="flex items-center gap-4 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors text-white"
                            >
                                <RiFlaskLine className="text-white" /> Laboratorios
                            </Link>
                        </li>
                        <li>
                            <Link
                                to="/equipos"
                                className="flex items-center gap-4 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors text-white"
                            >
                                <RiToolsLine className="text-white" /> Equipos
                            </Link>
                        </li>
                        <li>
                            <Link
                                to="/reservas"
                                className="flex items-center gap-4 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors text-white"
                            >
                                <RiCalendarTodoLine className="text-white" /> Reservas
                            </Link>
                        </li>
                        <li>
                            <Link
                                to="/usuarios"
                                className="flex items-center gap-4 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors text-white"
                            >
                                <RiUserLine className="text-white" /> Usuarios
                            </Link>
                        </li>
                        <li>
                            <Link
                                to="/reportes"
                                className="flex items-center gap-4 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors text-white"
                            >
                                <RiFileListLine className="text-white" /> Reportes
                            </Link>
                        </li>

                        <li>
                            <Link
                                to="/mantenimiento"
                                className="flex items-center gap-4 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors text-white"
                            >
                                <RiToolsLine className="text-white" /> Mantenimiento
                            </Link>
                        </li>

                        <li>
                            <Link
                                to="/communication"
                                className="flex items-center gap-4 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors text-white"
                            >
                                <RiMessage3Line className="text-white" /> Comunicación
                            </Link>
                        </li>

                    </ul>
                </div>

                <button
                    onClick={cerrarSesion} className="flex items-center gap-4 py-2 px-4 rounded-lg transition-colors hover:bg-gray-700 text-white"
                >
                    <RiLogoutCircleRLine className="text-red-500 " /> Cerrar sesión
                </button>

            </div>

            <button
                onClick={() => setShowMenu(!showMenu)}
                className="xl:hidden fixed bottom-4 right-4 bg-gray-800 text-white p-3 rounded-full z-50 hover:bg-gray-700"
            >
                {showMenu ? <RiCloseLine /> : <RiMenu3Line />}
            </button>
        </>
    );
};

export default Sidebar;
