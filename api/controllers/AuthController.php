<?php

namespace Api\Controllers;

use Exception;
use Services\DB;
//Import Firebase library to generate a JWT (JSON Web Token)
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController
{
    public $conn = null;
    public $configData = null;
    
    public function __construct() {
        // create connection.
        $this->conn = (new DB())->connectToDatabase();
        //Start session
        session_start();
        //Turn the config file into an array
        $this->configData = json_decode(file_get_contents("../config.json"), true);
    }

    /**
     * Function that attempts to log in a user if their credentials are correct
     */
    public function login() {
      if ($_SERVER["REQUEST_METHOD"] === "POST") {
        //Ideally at this point the data passed through our client side validation but we will also validate it on the server side.
        $valid = $this->validateData($_REQUEST);
        if ($valid) {
          //Use of real_escape_string to avoid sql injections. Of course with the use of frameworks you do not need to do this manually
          $formEmail = $this->conn->real_escape_string($_REQUEST["email"]);
          $userSql = "SELECT * FROM users WHERE email = '$formEmail' LIMIT 1";
          //If we successfully query the db
          if($result = mysqli_query($this->conn, $userSql)) {
            //If the results don't come back empty
            if(mysqli_num_rows($result) > 0) {
              $user = $result->fetch_assoc();

              $userID = $user["id"];
              $userName = $user["name"];
              $userEmail = $user["email"];
              if (password_verify($_REQUEST["password"], $user["password"])) {
                //Since we have a match we will return a JWT token
                //Expires in 10 minutes
                $expires = time() + 600;
                $payload = [
                  'iss' => $this->configData['SERVER_URL'],
                  'aud' => $this->configData['SERVER_URL'],
                  'exp' => $expires, 
                  'data' => [
                      'name'  =>  $userName,
                      'email' =>  $userEmail,
                  ],
                ];
                $key = "SecretMuzzKey";
                //Used the example algorith used by the Git Example 'https://github.com/firebase/php-jwt'
                $jwt = JWT::encode($payload, $key, 'HS256');
                //Change the UNIX Timestamp into an sql readable form
                $expires = date("Y-m-d H:i:s", $expires);
                //At this point we can also make a sql insertion into our token table (if that is our db architecture) for example:
                $sql = "INSERT INTO tokens(`user_id`, `token`, `expires`)
                VALUES (
                    '$userID',
                    '$jwt',
                    '$expires'
                  )";
                $successMsg = "Token '" . $jwt ."' successfully added for " . $userName;
                //Search of a token record already exists for said user
                $tokenSql = "SELECT * FROM tokens WHERE user_id = $userID LIMIT 1";
                $result = mysqli_query($this->conn, $tokenSql)->fetch_assoc();
                //If it already exists we update and not insert a new one
                if ($result) {
                  $sql = "UPDATE tokens SET token = '$jwt', expires = '$expires' WHERE user_id = $userID";
                  $successMsg = "Token '" . $jwt ."' successfully updated for " . $userName;
                }
                //Add Token to Session
                setcookie($userEmail, $jwt, time()+3600, "", $this->configData['SERVER_URL']);

                //Write the logged in user in the session or at least their tokens
                $_SESSION['user'] = $userName;
                $_SESSION['access_token'] = $jwt;
                $_SESSION['access_token_expires'] = $expires;

                if (mysqli_query($this->conn, $sql)) {
                  // echo $successMsg;
                  echo 'Success!';
                  exit;
                } else {
                  echo "Error: ". $sql ."<br/>". mysqli_error($this->conn);
                  exit;
                }
              }
              echo 'Wrong credentials';
              exit;
            }
            echo 'User not found!';
            exit;
          }
        }
        echo 'DB issue!';
        exit;
      }
    }

    /**
     * Function that destroys the session. Can also redirect you to the login page.
     * @return void
     */
    public function logout() {
      session_destroy();
      header("Location: " . $this->configData['SERVER_URL']);
    }

    /**
     * 
     */
    public function checkToken() {
      try{
        $key = "SecretMuzzKey";
        $user_data = JWT::decode($_SESSION['access_token'], new Key($key, 'HS256'));
        
        $data = $user_data->data;
      
        echo json_encode([
            'status' => 1,
            'message' => $data,
        ]);
      }catch(Exception $e){
        echo json_encode([
            'status' => 0,
            'message' => $e->getMessage(),
        ]);
      }
    }

    /**
     * Function that goes through our login(in this case) post data passed by the front-end and decide if it's ok to process
     */
    private function validateData($postData) {
      foreach ($postData as $name => $value) {
        switch ($name) {
          case 'email':
            $result = (!filter_var($value, FILTER_VALIDATE_EMAIL) || empty($value)) ? false : true;
          case 'name':
            //For names and such you can add a regex that checks for weird characters like &*()!£$% but honestly is better to just let the names be
            $result = empty($value) ? false : true;
            break;
          case 'password':            
            //We can also check that is not empty
            $result = (empty($value)) ? false : true;
          case 'verifyPassword':            
            //For passwords it would be more targeted towards registration validation if the password and verifyPoassword match but we can also check that is not empty
            $result = ($value != $postData['password'] || empty($value)) ? false : true;
          default:
            break;
        }
        /**
         * After each data check we see if it's invalid. If so we break the loop and return that the data is invalid. Of course in an actual working project it would be smart to
         * return a good error message and display that to the front end in one way or another but for the time being this will do.
         */ 
        if (!$result) {
          return $result;
        }
      }
      return true;
    }
}
