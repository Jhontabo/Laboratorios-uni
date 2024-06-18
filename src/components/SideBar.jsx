import React, { useState } from "react";
import { Link } from "react-router-dom";
import { UserAuth } from "../context/AuthContext";
// Icons
import {
    RiBarChart2Line,
    RiEarthLine,
    RiCustomerService2Line,
    RiCalendarTodoLine,
    RiLogoutCircleRLine,
    RiArrowRightSLine,
    RiMenu3Line,
    RiCloseLine,
} from "react-icons/ri";

const Sidebar = () => {
    const { user, logOut } = UserAuth();

    const cerrarSesión = async () => {
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
                className={`xl:h-[100vh] overflow-y-scroll fixed xl:static w-[80%] md:w-[40%] lg:w-[30%] xl:w-auto h-full top-0 bg-gray-700 p-4 flex flex-col justify-between z-50 ${showMenu ? "left-0" : "-left-full"
                    } transition-all`}
            >
                <div>
                    <h1 className="text-center text-2xl font-bold text-black mb-10">
                        Dashboard<span className="text-primary text-4xl">.</span>
                    </h1>

                    <ul>
                        <li>
                            <Link
                                to="/"
                                className="flex items-center gap-4 py-2 px-4 rounded-lg hover:bg-blue-500 transition-colors"
                            >
                                <RiBarChart2Line className="text-black" /> Analíticas
                            </Link>
                        </li>
                        <li>
                            <button
                                onClick={() => setShowSubmenu(!showSubmenu)}
                                className="w-full flex items-center justify-between py-2 px-4 rounded-lg hover:bg-blue-500 transition-colors"
                            >
                                <span className="flex items-center gap-4">
                                    <RiEarthLine className="text-black" /> Redes sociales
                                </span>
                                <RiArrowRightSLine
                                    className={`mt-1 ${showSubmenu && "rotate-90"
                                        } transition-all`}
                                />
                            </button>
                            <ul
                                className={` ${showSubmenu ? "h-[130px]" : "h-0"
                                    } overflow-y-hidden transition-all`}
                            >
                                <li>
                                    <Link
                                        to="/"
                                        className="py-2 px-4 border-l border-gray-500 ml-6 block relative before:w-3 before:h-3 before:absolute before:bg-primary before:rounded-full before:-left-[6.5px] before:top-1/2 before:-translate-y-1/2 before:border-4 before:border-secondary-100 hover:text-white transition-colors"
                                    >
                                        Post red social
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        to="/"
                                        className="py-2 px-4 border-l border-gray-500 ml-6 block relative before:w-3 before:h-3 before:absolute before:bg-gray-500 before:rounded-full before:-left-[6.5px] before:top-1/2 before:-translate-y-1/2 before:border-4 before:border-secondary-100 hover:text-white transition-colors"
                                    >
                                        Estadisticas
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        to="/"
                                        className="py-2 px-4 border-l border-gray-500 ml-6 block relative before:w-3 before:h-3 before:absolute before:bg-gray-500 before:rounded-full before:-left-[6.5px] before:top-1/2 before:-translate-y-1/2 before:border-4 before:border-secondary-100 hover:text-white transition-colors"
                                    >
                                        Perfiles
                                    </Link>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <Link
                                to="/chat"
                                className="flex items-center gap-4 py-2 px-4 rounded-lg hover:bg-blue-500 transition-colors"
                            >
                                <RiCustomerService2Line className="text-black" /> Soporte
                                técnico
                            </Link>
                        </li>
                        <li>
                            <Link
                                to="/calendario"
                                className="flex items-center gap-4 py-2 px-4 rounded-lg hover:bg-blue-500 transition-colors"
                            >
                                <RiCalendarTodoLine className="text-black" /> Calendario
                            </Link>
                        </li>
                    </ul>

                </div>

                <button
                    onClick={cerrarSesión} className="flex items-center gap-4 py-2 px-4 rounded-lg  transition-colors  hover:bg-blue-500"
                >
                    <RiLogoutCircleRLine className="text-red-500 " /> Cerrar sesión
                </button>

            </div>

            <button

                onClick={() => setShowMenu(!showMenu)}
                className="xl:hidden fixed bottom-4 right-4 bg-primary text-black p-3 rounded-full z-50 hover:bg-blue-500"
            >
                {showMenu ? <RiCloseLine /> : <RiMenu3Line />}

            </button>
        </>
    );
};

export default Sidebar;