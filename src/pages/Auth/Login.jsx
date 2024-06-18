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

    const handleSubmit = (e) => {
        e.preventDefault();
        const email = e.target.email.value;
        const password = e.target.password.value;
        console.log('Email:', email);
        console.log('Password:', password);
    };

    const toggleDarkMode = () => {
        setIsDarkMode(!isDarkMode);
    }

    return (
        <div className={`flex justify-center items-center min-h-screen ${isDarkMode ? 'bg-gray-800' : 'bg-gray-100'}`}>
            <div className={`relative w-[470px] h-[520px] p-4 border ${isDarkMode ? 'bg-gray-700 border-gray-600' : 'bg-white border-gray-300'} rounded-lg`}>
                <div className="flex flex-col text-center my-5">
                    <header className={`font-poppins text-2xl mb-1 ${isDarkMode ? 'text-white' : 'text-gray-800'}`}>Hola, bienvenido a CAMPUS VIRTUAL UMARIANA</header>
                </div>
                <form className="relative w-full" onSubmit={handleSubmit}>
                    <div className="relative w-full mb-6">
                        <input type="text" id="email" className={`w-full h-12 pl-4 mb-6 border ${isDarkMode ? 'bg-gray-600 border-gray-500 text-white' : 'bg-gray-100 border-gray-300 text-gray-800'} rounded-md outline-none focus:border-purple-600`} autoComplete="off" required />
                        <label htmlFor="email" className={`absolute top-3 left-4 transition-transform duration-150 transform-gpu scale-75 -translate-y-6 px-1 ${isDarkMode ? 'text-gray-300 bg-gray-700' : 'text-gray-600 bg-gray-100'}`}>Correo</label>
                    </div>
                    <div className="relative w-full mb-6">
                        <input type="password" id="password" className={`w-full h-12 pl-4 mb-2 border ${isDarkMode ? 'bg-gray-600 border-gray-500 text-white' : 'bg-gray-100 border-gray-300 text-gray-800'} rounded-md outline-none focus:border-purple-600`} autoComplete="off" required />
                        <label htmlFor="password" className={`absolute top-3 left-4 transition-transform duration-150 transform-gpu scale-75 -translate-y-6 px-1 ${isDarkMode ? 'text-gray-300 bg-gray-700' : 'text-gray-600 bg-gray-100'}`}>Contraseña</label>
                    </div>
                    <div className="flex justify-between mb-6">
                        {/* Aquí podrías agregar más contenido */}
                    </div>
                    <div className="relative w-full mb-6">
                        <input type="submit" className={`font-poppins text-white text-lg ${isDarkMode ? 'bg-purple-500' : 'bg-purple-500'} border-none rounded-md cursor-pointer py-2 w-full`} value="Iniciar sesión" />
                    </div>
                </form>
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