import React from "react";
import { Outlet } from "react-router-dom";
import SidebarStudent from "../components/SideBarStudent";
import Header from "../components/Header";

const LayoutStudent = () => {
  return (
    <div className="min-h-screen grid grid-cols-1 xl:grid-cols-6">
      <SidebarStudent />
      <div className="xl:col-span-5">
        <Header />
        <div className="h-[90vh] overflow-y-scroll p-8">
          <Outlet />
        </div>
      </div>
    </div>
  );
};

export default LayoutStudent;