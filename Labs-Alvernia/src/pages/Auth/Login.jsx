import { useEffect, useState } from "react";
import { UserAuth } from "../../context/AuthContext";
import { useNavigate } from "react-router-dom";
import React from 'react';
import googleLogo from '../../assets/google.svg';

const Login = () => {

    const [isDarkMode, setIsDarkMode] = useState(true);

    const navigate = useNavigate();

    const { user, googleSignIn } = UserAuth();
    const iniciarSesion = async () => {
        try {
            await googleSignIn();
        } catch (error) {
            console.log(error);
        }
    }
    useEffect(() => {
        if (user != null) {
            navigate("/")
        }
    }, [user])


    const toggleDarkMode = () => {
        setIsDarkMode(!isDarkMode);
    }

    return (
        <div className={`flex justify-center items-center min-h-screen ${isDarkMode ? 'bg-gray-800' : 'bg-gray-100'}`}>
            <div className={`relative w-[470px] h-[520px] p-4 border ${isDarkMode ? 'bg-gray-700 border-gray-600' : 'bg-white border-gray-300'} rounded-lg`}>
                <div className="flex flex-col text-center my-5">
                    <header className={`font-poppins text-2xl mb-1 ${isDarkMode ? 'text-white' : 'text-gray-800'}`}>Hola, bienvenido a CAMPUS VIRTUAL UMARIANA</header>
                </div>
                <div className="relative w-full my-8">
                    <hr className={`border-t ${isDarkMode ? 'border-gray-600' : 'border-gray-300'}`} />
                    <p className={`absolute left-1/2 transform -translate-x-1/2 -translate-y-1/2 px-2 ${isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600'}`}>O</p>
                </div>

                <div className="flex gap-4">
                    <button onClick={iniciarSesion} className={`flex items-center justify-between w-full h-12 px-6 ${isDarkMode ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-800'} border ${isDarkMode ? 'border-gray-500' : 'border-gray-400'} rounded-md hover:opacity-90`}>
                        <img src={googleLogo} alt="Google Logo" className="w-6" />
                        <p className="font-poppins text-lg w-full text-center">Iniciar sesión con Google</p>
                    </button>
                </div>
                <button onClick={toggleDarkMode} className={`absolute top-2 right-2 p-2 rounded-full ${isDarkMode ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-800'}`}>
                    {isDarkMode ? '☀️' : '🌙'}
                </button>
            </div>
        </div>
    );
};

export default Login;