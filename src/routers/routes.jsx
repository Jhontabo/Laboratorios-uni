// src/routes/MyRoutes.js
import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import { UserAuth } from "../context/AuthContext";
import LayoutAuth from "../layouts/layoutAuth";
import LayoutAdmin from "../layouts/layoutAdmin";
import Login from "../pages/Auth/Login";
import Home from "../pages/admin/Home";
import Profile from "../pages/admin/Profile";
import Chat from "../pages/admin/Chat";
import Error404 from "../pages/Errorpage";
import Calendar from "../pages/admin/Calendar";

export function MyRoutes() {
    const { user, loading } = UserAuth();

    const RequireAuth = ({ children }) => {
        if (loading) return <div>Loading...</div>;
        return user ? children : <Navigate to="/login" />;
    };

    return (
        <BrowserRouter>
            <Routes>
                <Route path="/login" element={<Login />} />
                <Route
                    path="/"
                    element={
                        <RequireAuth>
                            <LayoutAdmin />
                        </RequireAuth>
                    }
                >
                    <Route index element={<Home />} />
                    <Route path="perfil" element={<Profile />} />
                    <Route path="chat" element={<Chat />} />
                    <Route path="calendario" element={<Calendar />} />
                </Route>
                <Route path="*" element={<Error404 />} />
            </Routes>
        </BrowserRouter>
    );
}
