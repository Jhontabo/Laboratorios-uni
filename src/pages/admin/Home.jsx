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
                <h1 className="text-4xl text-black">Home</h1>
                <div className="flex items-center gap-2 text-3xl">
                    <RiArrowLeftSLine className="hover:cursor-pointer hover:text-white transition-colors" />
                    <RiArrowRightSLine className="hover:cursor-pointer hover:text-white transition-colors" />
                </div>
            </div>
        </div >
    );
};

export default Home;