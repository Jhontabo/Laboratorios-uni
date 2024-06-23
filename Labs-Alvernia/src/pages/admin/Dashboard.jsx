import React from "react";
import { Link } from "react-router-dom";
import { RiArrowLeftSLine, RiArrowRightSLine } from "react-icons/ri";
import { Menu, MenuItem, MenuButton } from "@szhsin/react-menu";
import "@szhsin/react-menu/dist/index.css";
import "@szhsin/react-menu/dist/transitions/slide.css";

const Home = () => {
    return (
        <div>
            <div className="flex items-center justify-between mb-10">
                <h1 className="text-4xl text-black">Dashboard</h1>
            </div>
        </div >
    );
};

export default Home;