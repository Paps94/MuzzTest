import React, { useEffect, useState } from "react";

export default function ScrollToTop() {
  const [isVisible, setIsVisible] = useState(false);

  // Top: 0 takes us all the way back to the top of the page
  // Behavior: smooth keeps it smooth!
  const scrollToTop = () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  };

  useEffect(() => {
    // Button is displayed after scrolling for 250 pixels
    const toggleVisibility = () => {
      if (window.pageYOffset > 250) {
        setIsVisible(true);
      } else {
        setIsVisible(false);
      }
    };

    window.addEventListener("scroll", toggleVisibility);

    return () => window.removeEventListener("scroll", toggleVisibility);
  }, []);

  return (
    <>
      {isVisible && (
        <div
          onClick={scrollToTop}
          className="fixed bottom-[20px] right-[15px] w-[45px] h-[45px] z-99 cursor-pointer"
          data-aos="fade-up"
          data-aos-duration="1200"
          data-aos-delay="200"
        >
          <span className="
            relative overflow-hidden bg-[#1a1a1a] p-[22px] inline-block rounded-[3px]  dark:after:border-b-black dark:bg-[#f5f8fc]
            after:w-0 after:h-0 after:absolute after:top-[14px] after:left-[17px] after:border-solid after:border-transparent after:border-[5px] after:z-2 after:border-b-[#f5f8fc]"
          ></span>
        </div>
      )}
    </>
  );
}
