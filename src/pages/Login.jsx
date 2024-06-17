// src/components/SignIn.jsx
import { useEffect } from "react";
import { UserAuth } from "../context/AuthContext";
import { useNavigate } from "react-router-dom";
import React from 'react';
import googleLogo from '../assets/google.svg';

const Login = () => {
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

    return (
        <div className="flex justify-center items-center min-h-screen">
            <div className="relative w-[470px] h-[520px] p-4 bg-white border border-gray-300 rounded-lg">
                <div className="flex flex-col text-center my-5">
                    <header className="font-poppins text-gray-800 text-2xl mb-1">Bienvenid@</header>
                    <p className="text-gray-600">Estamos felices de tenerte de vuelta!</p>
                </div>
                <form className="relative w-full" onSubmit={handleSubmit}>
                    <div className="relative w-full mb-6">
                        <input type="text" id="email" className="w-full h-12 pl-4 mb-6 border border-gray-300 rounded-md outline-none focus:border-purple-600" autoComplete="off" required />
                        <label htmlFor="email" className="absolute top-3 left-4 text-gray-600 transition-transform duration-150 transform-gpu scale-75 -translate-y-6 bg-white px-1">Correo</label>
                    </div>
                    <div className="relative w-full mb-6">
                        <input type="password" id="password" className="w-full h-12 pl-4 mb-2 border border-gray-300 rounded-md outline-none focus:border-purple-600" autoComplete="off" required />
                        <label htmlFor="password" className="absolute top-3 left-4 text-gray-600 transition-transform duration-150 transform-gpu scale-75 -translate-y-6 bg-white px-1">Contraseña</label>
                    </div>
                    <div className="flex justify-between mb-6">
                        {/* Aquí podrías agregar más contenido */}
                    </div>
                    <div className="relative w-full mb-6">
                        <input type="submit" className="font-poppins text-white text-lg bg-purple-600 border-none rounded-md cursor-pointer py-2 w-full" value="Iniciar sesión" />
                    </div>
                </form>
                <div className="relative w-full my-8">
                    <hr className="border-t border-gray-300" />
                    <p className="absolute left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white px-2 text-gray-600">O</p>
                </div>
                <div className="flex gap-4">
                    <button onClick={iniciarSesion} className="flex items-center justify-between w-full h-12 px-6 bg-white border border-gray-400 rounded-md hover:opacity-90">
                        <img src={googleLogo} alt="Google Logo" className="w-6" />
                        <p className="font-poppins text-lg w-full text-center">Iniciar sesión con Google</p>
                    </button>
                </div>
            </div>
        </div>
    );
};

export default Login;
