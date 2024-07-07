import React, { useState } from "react";
import { Link } from "react-router-dom";
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

const SidebarStudent = () => {


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
                        Estudiante<span className="text-primary text-4xl">.</span>
                    </h1>

                    <ul>
                        <li>
                            <Link
                                to="/"
                                className="flex items-center gap-4 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors text-white"
                            >
                                <RiBarChart2Line className="text-white text-2xl" /> Inicio
                            </Link>
                        </li>

                        <li>
                            <Link
                                to="/calendario"
                                className="flex items-center gap-4 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors text-white"
                            >
                                <RiCalendarTodoLine className="text-white text-2xl" /> Calendario
                            </Link>
                        </li>
                        <li>
                            <Link
                                to="/laboratorios"
                                className="flex items-center gap-4 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors text-white"
                            >
                                <RiFlaskLine className="text-white text-2xl" /> Laboratorios
                            </Link>
                        </li>

                        <li>
                            <Link
                                to="/reservas"
                                className="flex items-center gap-4 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors text-white"
                            >
                                <RiCalendarTodoLine className="text-white text-2xl" /> Reservas
                            </Link>
                        </li>


                        <li>
                            <Link
                                to="/communication"
                                className="flex items-center gap-4 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors text-white"
                            >
                                <RiMessage3Line className="text-white text-2xl" /> Comunicación
                            </Link>
                        </li>

                    </ul>
                </div>

                <button
                    className="flex items-center gap-4 py-2 px-4 rounded-lg transition-colors hover:bg-gray-700 text-white"
                >
                    <RiLogoutCircleRLine className="text-red-500 text-2xl" /> Cerrar sesión
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

export default SidebarStudent;
