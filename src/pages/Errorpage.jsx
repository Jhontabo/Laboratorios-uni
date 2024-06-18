import React from "react";
import { Link } from "react-router-dom";

const Error404 = () => {
    return (
        <div className="flex flex-col items-center justify-center h-screen bg-gray-100">
            <div className="text-center">
                <h1 className="text-9xl font-bold text-indigo-600">404</h1>
                <p className="mt-4 text-3xl font-semibold text-gray-800">
                    Página no encontrada
                </p>
                <p className="mt-2 text-gray-600">
                    Lo sentimos, la página que estás buscando no existe.
                </p>
            </div>
            <div className="mt-8">
                <Link
                    to="/"
                    className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
                >
                    Regresar al inicio
                </Link>
            </div>
        </div>
    );
};

export default Error404;