import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import { UserAuth } from "../context/AuthContext";
import { Dashboard } from "../pages/Dashboard";
import Login from "../pages/Login";


export function MyRoutes() {
    const { user } = UserAuth();

    const RequireAuth = ({ children }) => {
        return user ? children : <Navigate to={"/Login"} />;
    }

    return (<BrowserRouter>
        <Routes>
            <Route path="/" element={<RequireAuth>
                <Dashboard />
            </RequireAuth>} />
            <Route path="/Login" element={<Login />} />

        </Routes>
    </BrowserRouter>);
}