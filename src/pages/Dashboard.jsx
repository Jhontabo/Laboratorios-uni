import React, { useState } from "react";
import { UserAuth } from "../context/AuthContext";

export function Dashboard() {
    const { user, logOut } = UserAuth();
    const [sidebarOpen, setSidebarOpen] = useState(false);

    const cerrarSesión = async () => {
        try {
            await logOut();
        } catch (error) {
            console.log(error);
        }
    };

    const toggleSidebar = () => {
        setSidebarOpen(!sidebarOpen);
    };

    return (
        <div className="flex min-h-screen">
            {/* Botón de menú para móviles */}
            <div className="md:hidden p-4">
                <button onClick={toggleSidebar} className="text-gray-500 focus:outline-none">
                    <i className="fas fa-bars"></i>
                </button>
            </div>

            {/* Sidebar */}
            <div className={`fixed inset-y-0 left-0 bg-white border-r border-gray-200 transform ${sidebarOpen ? 'translate-x-0' : '-translate-x-full'} transition-transform duration-300 ease-in-out md:relative md:translate-x-0 md:w-64`}>
                <div className="p-4">
                    <h2 className="text-xl font-semibold text-gray-800">Sidebar</h2>
                    <ul className="mt-4 space-y-2">
                        <li>
                            <a href="#" className="text-gray-700 hover:text-purple-600 block">Calendario</a>
                        </li>
                        <li>
                            <a href="#" className="text-gray-700 hover:text-purple-600 block">Estadísticas</a>
                        </li>
                        <li>
                            <a href="#" className="text-gray-700 hover:text-purple-600 block">Contacto</a>
                        </li>
                        <li>
                            <a href="#" className="text-gray-700 hover:text-purple-600 block">Pedir Equipos</a>
                        </li>
                        <li>
                            <button onClick={cerrarSesión} className="w-full text-left text-gray-700 hover:text-purple-600 block">Cerrar Sesión</button>
                        </li>
                    </ul>
                </div>
            </div>

            {/* Contenido principal */}
            <div className="flex-1 p-6 bg-gray-100">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold text-gray-800">Dashboard - Laboratorio Institucional</h1>
                </div>
                <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div className="bg-white p-6 rounded-lg shadow-md">
                        <h2 className="text-2xl font-semibold text-gray-800 mb-2">Calendario</h2>
                        <p className="text-gray-600">Calendario de reservas y eventos</p>
                    </div>
                    <div className="bg-white p-6 rounded-lg shadow-md">
                        <h2 className="text-2xl font-semibold text-gray-800 mb-2">Estadísticas</h2>
                        <p className="text-gray-600">Estadísticas de uso de equipos y recursos</p>
                    </div>
                    <div className="bg-white p-6 rounded-lg shadow-md">
                        <h2 className="text-2xl font-semibold text-gray-800 mb-2">Contacto</h2>
                        <p className="text-gray-600">Información de contacto y soporte</p>
                    </div>
                    <div className="bg-white p-6 rounded-lg shadow-md">
                        <h2 className="text-2xl font-semibold text-gray-800 mb-2">Pedir Equipos</h2>
                        <p className="text-gray-600">Solicitud de equipos y recursos adicionales</p>
                    </div>
                </div>
            </div>
        </div>
    );
}
