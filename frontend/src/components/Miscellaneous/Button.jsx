import React from "react";
import Swal from "sweetalert2";
import configData from "../../../../config.json";

//This gave me a 504 when trying to load it fron vendor so i used the cdn package
// import axios from "axios";

const Button = ({ title, request, link, classes }) => {
  const handleRequest = () => {
    axios({
      method: request,
      url: configData.SERVER_URL + link,
    })
      .then((response) => {
        console.log(response);
        Swal.fire({
          icon: "success",
          title: "Success!",
          text: response.data,
          timer: 3000,
          showCancelButton: false,
          showConfirmButton: false,
        });
      })
      .catch((error) => {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: error.message,
          timer: 3000,
          showCancelButton: false,
          showConfirmButton: false,
        });
        console.log(error);
      });
  };

  return (
    <>
      <button className={classes} onClick={handleRequest}>
        {title}
      </button>
    </>
  );
};

export default Button;
