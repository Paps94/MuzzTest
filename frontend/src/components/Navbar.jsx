import React, { useContext } from "react";

import Button from "./UI/Button/Button";
import AuthContext from "./../store/auth-context";

export default function Navbar() {
  const authCtx = useContext(AuthContext);

  return (
    <>
      <nav className="w-full bg-[#FB406C] shadow">
        <div className="justify-between px-4 mx-auto lg:max-w-7xl md:items-center md:flex md:px-8">
          <div>
            <div className="flex items-center justify-between py-3 md:py-5 md:block">
              <a href="javascript:void(0)">
                <h2 className="text-2xl font-bold text-white">
                  Antreas Papadopoulos
                </h2>
              </a>
            </div>
          </div>
          <div className="hidden space-x-2 md:inline-block">
            {authCtx.isLoggedIn && (
              <Button
                className="px-4 py-2 text-[#FB406C] bg-white  rounded-md shadow hover:opacity-80"
                onClick={authCtx.onLogout}
              >
                Logout
              </Button>
            )}
          </div>
        </div>
      </nav>
    </>
  );
}
