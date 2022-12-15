import React, { useState, useEffect, useMemo, useRef } from "react";
import TinderCard from "react-tinder-card";
import Swal from "sweetalert2";
import configData from "../../../../config.json";


function Swipe() {
  //Store our profiles
  const [results, setResults] = useState([]);

  useEffect(() => {
    axios({
      method: "GET",
      url: configData.SERVER_URL + "profiles",
      params: {
        id: '63'
      }
    })
    .then((response) => {
      setResults(response.data);
      setCurrentIndex(response.data.length -1);
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
  }, []);

  const [currentIndex, setCurrentIndex] = useState( - 1);
  const [lastDirection, setLastDirection] = useState();
  // used for outOfFrame closure
  const currentIndexRef = useRef(currentIndex);

  const childRefs = useMemo(
    () =>
      Array(results.length)
        .fill(0)
        .map((i) => React.createRef()),
    []
  );

  const updateCurrentIndex = (val) => {
    setCurrentIndex(val);
    currentIndexRef.current = val;
  };

  const canGoBack = currentIndex < results.length - 1;

  const canSwipe = currentIndex >= 0;

  // set last direction and decrease current index
  const swiped = (direction, nameToDelete, index) => {
    setLastDirection(direction);
    updateCurrentIndex(index - 1);
  };

  const makeBackEndRequest = (dir) => {
    //Logic to change the direaction into a boolean
    var preference = (dir == 'right') ? 1 : 0;
    //Make new request to update the db based on the swipe
    //For some reason if I use data as the axios docs say for post request it throws an error but if I use params it works
    //Which means I need to change the logic in the UsersCotroller to look for the $_REQUEST instead of $_POST object
    axios({
      method: "POST",
      url: configData.SERVER_URL + "swipe",
      params: {
        userid: configData.USER_ID,
        profileid: results[currentIndex].id,
        preference: preference,
      }
    })
    .then((response) => {
      if (response.data == 'You have a match!') {
        Swal.fire({
          icon: "success",
          title: "Congratulations!",
          text: response.data,
          timer: 3000,
          showCancelButton: false,
          showConfirmButton: false,
        });
      }
      console.log(response);
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
  }

  const outOfFrame = (name, idx) => {
    console.log(`${name} left the screen!`, currentIndexRef.current);
    // handle the case in which go back is pressed before card goes outOfFrame
    currentIndexRef.current >= idx && childRefs[idx].current.restoreCard();
  };
  //On page load ChildRefs will not be saved but if you save this file for some reason they are there
  //This might be me writting react code for the past 5 hours and I am missing something...

  const swipe = async (dir) => {
    if (canSwipe && currentIndex < results.length) {
      // Swipe the card!
      await childRefs[currentIndex].current.swipe(dir); 
      makeBackEndRequest(dir);
    }
  };

  // increase current index and show card
  // Ideally there is functionallity for this to go back and change the db record and so on but for this example we will assume that is being considered
  const goBack = async () => {
    if (!canGoBack) return;
    const newIndex = currentIndex + 1;
    updateCurrentIndex(newIndex);
    await childRefs[newIndex].current.restoreCard();
  };

  return (
    <div className="mt-[200px] ">
      <link
        href="https://fonts.googleapis.com/css?family=Damion&display=swap"
        rel="stylesheet"
      />
      <link
        href="https://fonts.googleapis.com/css?family=Alatsi&display=swap"
        rel="stylesheet"
      />
      <div className="cardContainer m-auto mb-[100px] ">
        {results.map((character, index) => (
          <TinderCard
            ref={childRefs[index]}
            className="swipe"
            key={character.name}
            onSwipe={(dir) => swiped(dir, character.name, index)}
            onCardLeftScreen={() => outOfFrame(character.name, index)}
          >
            <div
              style={{ backgroundImage: "url(/assets/avatar.png)" }}
              className="card"
            >
              <h1 className="absolute text-white p-1 rounded-md bottom-10 bg-black/50 w-[fit-content]">{character.name}</h1>
              <h2 className="absolute text-white p-1 rounded-md bottom-0 bg-black/50 w-[fit-content]">{character.age} years old</h2>
            </div>
          </TinderCard>
        ))}
      </div>
      <div className="buttons justify-center">
        <button
          style={{ backgroundColor: !canSwipe && "#c3c4d3" }}
          onClick={() => swipe("left")}
        >
          Swipe left!
        </button>
        <button
          style={{ backgroundColor: !canGoBack && "#c3c4d3" }}
          onClick={() => goBack()}
        >
          Undo swipe!
        </button>
        <button
          style={{ backgroundColor: !canSwipe && "#c3c4d3" }}
          onClick={() => swipe("right")}
        >
          Swipe right!
        </button>
      </div>
      {lastDirection ? (
        <h2 key={lastDirection} className="infoText">
          You swiped {lastDirection}
        </h2>
      ) : (
        <h2 className="infoText">
          Swipe a card or press a button to get Restore Card button visible!
        </h2>
      )}
    </div>
  );
}

export default Swipe;
