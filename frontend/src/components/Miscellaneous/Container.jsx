import React from "react";

export default function Container({ children }) {
  return (
    <div className="flex items-center gap-4 justify-center flex-row text-center m-10">
      {children}
    </div>
  );
}
