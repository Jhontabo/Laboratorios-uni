// src/pages/Comunicacion.js
import React from "react";
import { RiWhatsappLine, RiMailLine } from "react-icons/ri";

const Comunicacion = () => {
    return (
        <>
            <h2 className="text-4xl font-bold mb-8 text-center lg:text-center text-gray-800">Comunicación</h2>

            <div className="p-4 bg-white min-h-screen text-gray-800 flex flex-col lg:flex-row pt-0 lg:pt-6">
                <div className="lg:w-1/2 flex flex-col pr-8">
                    <div className="bg-gray-100 p-6 rounded-lg shadow-lg flex items-center space-x-6 mb-8">
                        <RiWhatsappLine className="text-9xl text-green-500" />
                        <div>
                            <h3 className="text-2xl font-bold mb-2">WhatsApp</h3>
                            <p className="text-gray-600 mb-4">
                                Para contactar vía WhatsApp, haga clic en el enlace siguiente:
                            </p>
                            <a
                                href="https://wa.me/3116896936"
                                target="_blank"
                                rel="noopener noreferrer"
                                className="text-lg text-green-500 underline hover:text-green-400"
                            >
                                Enviar mensaje por WhatsApp
                            </a>
                            <p className="mt-2 text-sm text-gray-500">
                                Puedes usar este enlace para comunicarte directamente con nuestro equipo a través de WhatsApp. Estamos disponibles para responder tus consultas y proporcionarte asistencia.
                            </p>
                        </div>
                    </div>
                    <div className="bg-gray-100 p-6 rounded-lg shadow-lg flex items-center space-x-6">
                        <RiMailLine className="text-7xl text-blue-500" />
                        <div className="text-blue-500 cursor-pointer select-all"
                            onMouseEnter={(e) => e.target.style.color = "#3182ce"}
                            onMouseLeave={(e) => e.target.style.color = "inherit"}
                            onClick={() => navigator.clipboard.writeText("coordlabing@umariana.edu.co")}
                        >
                            <h3 className="text-2xl font-bold mb-2">Correo Electrónico</h3>
                            <p className="text-gray-600 mb-4">
                                Para contactar vía correo electrónico, utilice la dirección siguiente:
                            </p>
                            <p className="mb-2">
                                <a
                                    href="mailto:coordlabing@umariana.edu.co"
                                    className="text-lg text-blue-500 underline hover:text-blue-400"
                                >
                                    coordlabing@umariana.edu.co
                                </a>
                            </p>
                            <p className="text-sm text-gray-500">
                                Envía tus consultas por correo electrónico y te responderemos lo antes posible. ¡Estamos aquí para ayudarte!
                            </p>
                        </div>
                    </div>
                </div>
                <div className="lg:w-1/2 flex items-center justify-center mt-6 lg:mt-0">
                    <img
                        src="/src/assets/AtencionCliente.jpg"
                        alt="Atencion al cliente"
                        className="w-full h-full object-cover rounded-lg"
                    />
                </div>
            </div>
        </>
    );
};

export default Comunicacion;
