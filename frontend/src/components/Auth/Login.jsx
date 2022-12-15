import React, {
  useState,
  useEffect,
  useReducer,
  useContext,
  useRef,
} from "react";

import Card from "../UI/Card/Card";
import Button from "../UI/Button/Button";
import AuthContext from "../../store/auth-context";
import Input from "../UI/Input/Input";
import Swal from "sweetalert2";
import configData from "../../../../config.json";

//Simple email validation that checks for the '@' character, I understand this is not adequete but for the sake of validation example it will do
const emailReducer = (state, action) => {
  if (action.type === "USER_INPUT") {
    return { value: action.val, isValid: action.val.includes("@") };
  }
  if (action.type === "INPUT_BLUR") {
    return { value: state.value, isValid: state.value.includes("@") };
  }
  return { value: "", isValid: false };
};

//Simple password validation that requires it to be more than a character for the sake of validation
const passwordReducer = (state, action) => {
  if (action.type === "USER_INPUT") {
    return { value: action.val, isValid: action.val.trim().length > 1 };
  }
  if (action.type === "INPUT_BLUR") {
    return { value: state.value, isValid: state.value.trim().length > 1 };
  }
  return { value: "", isValid: false };
};

const Login = () => {
  const [formIsValid, setFormIsValid] = useState(false);
  const [emailState, dispatchEmail] = useReducer(emailReducer, {
    value: "",
    isValid: null,
  });
  const [passwordState, dispatchPassword] = useReducer(passwordReducer, {
    value: "",
    isValid: null,
  });

  const authCtx = useContext(AuthContext);

  //Capture the user imput
  const emailInputRef = useRef();
  const passwordInputRef = useRef();

  const { isValid: emailIsValid } = emailState;
  const { isValid: passwordIsValid } = passwordState;

  useEffect(() => {
    const identifier = setTimeout(() => {
      console.log("Checking form validity!");
      setFormIsValid(emailIsValid && passwordIsValid);
    }, 500);

    return () => {
      clearTimeout(identifier);
    };
  }, [emailIsValid, passwordIsValid]);

  const emailChangeHandler = (event) => {
    dispatchEmail({ type: "USER_INPUT", val: event.target.value });
  };

  const passwordChangeHandler = (event) => {
    dispatchPassword({ type: "USER_INPUT", val: event.target.value });
  };

  const validateEmailHandler = () => {
    dispatchEmail({ type: "INPUT_BLUR" });
  };

  const validatePasswordHandler = () => {
    dispatchPassword({ type: "INPUT_BLUR" });
  };

  const submitHandler = (event) => {
    event.preventDefault();
    //Same issue here having to pass the details as part of the $_REQUEST which is not ideal
    //but the docs are wrong!
    if (formIsValid) {
        axios({
          method: "POST",
          url: configData.SERVER_URL + "login",
          params: {
            email: emailState.value,
            password: passwordState.value,
          },
        })
        .then((response) => {
          console.log(response);
          if (response.data == 'Success!') {
            Swal.fire({
              icon: "success",
              title: "Success!",
              text: "You are now logged in! Happy swiping!",
              timer: 3000,
              showCancelButton: false,
              showConfirmButton: false,
            });
            authCtx.onLogin(emailState.value, passwordState.value);
          } else {
            Swal.fire({
              icon: "warning",
              title: "Something went wrong!",
              text: response.data,
              timer: 3000,
              showCancelButton: false,
              showConfirmButton: false,
            });
          }
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
    } else if (!emailIsValid) {
      emailInputRef.current.focus();
    } else {
      passwordInputRef.current.focus();
    }
  };

  return (
    <Card className="w-full max-w-[2rem auto] m-[2rem auto] p-[2rem]">
      <form onSubmit={submitHandler}>
        <Input
          ref={emailInputRef}
          id="email"
          label="E-Mail"
          type="email"
          isValid={emailIsValid}
          value={emailState.value}
          onChange={emailChangeHandler}
          onBlur={validateEmailHandler}
        />
        <Input
          ref={passwordInputRef}
          id="password"
          label="Password"
          type="password"
          isValid={passwordIsValid}
          value={passwordState.value}
          onChange={passwordChangeHandler}
          onBlur={validatePasswordHandler}
        />
        <div className="text-center">
          <Button
            type="submit"
            className="bg-[#FB406C] text-white font-bold py-2 px-4 rounded opacity-80 cursor-pointer transition duration-200 hover:opacity-50 hover:scale-[1.05]"
          >
            Log in
          </Button>
        </div>
      </form>
    </Card>
  );
};

export default Login;
