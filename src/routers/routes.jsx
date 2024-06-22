import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import { UserAuth } from "../context/AuthContext";
import LayoutAuth from "../layouts/layoutAuth";
import LayoutAdmin from "../layouts/layoutAdmin";
import Login from "../pages/Auth/Login";
import Dashboard from "../pages/admin/Dashboard";
import Profile from "../pages/admin/Profile";
import Communication from "../pages/admin/Communication";
import Error404 from "../pages/Errorpage";
import MiCalendario from "../pages/admin/Calendario";
import Laboratorios from "../pages/admin/Laboratorios";
import Mantenimiento from "../pages/admin/maintenancePage";
import Reportes from "../pages/admin/Reports";
import Usuarios from "../pages/admin/Users";
import Equipos from "../pages/admin/EquiposPage";
import Reservas from "../pages/admin/ReservasPage";


export function MyRoutes() {
    const { user } = UserAuth();

    const RequireAuth = ({ children }) => {
        return user ? children : <Navigate to={"/login"} />;
    }

    return (<BrowserRouter>
        <Routes>
            <Route path="/login" element={<Login />} />
            <Route path="/" element={<RequireAuth><LayoutAdmin /></RequireAuth>}>
                <Route index element={<Dashboard />} />
                <Route path="perfil" element={<Profile />} />
                <Route path="communication" element={<Communication />} />
                <Route path="calendario" element={<MiCalendario />} />
                <Route path="laboratorios" element={<Laboratorios />} />
                <Route path="mantenimiento" element={<Mantenimiento />} />
                <Route path="reportes" element={<Reportes />} />
                <Route path="usuarios" element={<Usuarios />} />
                <Route path="equipos" element={<Equipos />} />
                <Route path="reservas" element={<Reservas />} />


            </Route>
            <Route path="*" element={<Error404 />} />


        </Routes>
    </BrowserRouter>);
}