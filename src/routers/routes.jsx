import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import { UserAuth } from "../context/AuthContext";
import LayoutAuth from "../layouts/layoutAuth";
import LayoutAdmin from "../layouts/layoutAdmin";
import Login from "../pages/Auth/Login";
import Home from "../pages/admin/Home";
import Profile from "../pages/admin/Profile";
import Communication from "../pages/admin/Communication";
import Error404 from "../pages/Errorpage";
import MiCalendario from "../pages/admin/Calendario";


export function MyRoutes() {
    const { user } = UserAuth();

    const RequireAuth = ({ children }) => {
        return user ? children : <Navigate to={"/login"} />;
    }

    return (<BrowserRouter>
        <Routes>
            <Route path="/login" element={<Login />} />
            <Route path="/" element={<RequireAuth><LayoutAdmin /></RequireAuth>}>
                <Route index element={<Home />} />
                <Route path="perfil" element={<Profile />} />
                <Route path="communication" element={<Communication />} />
                <Route path="calendario" element={<MiCalendario />} />

            </Route>
            <Route path="*" element={<Error404 />} />


        </Routes>
    </BrowserRouter>);
}