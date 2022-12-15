<?php

namespace Api\Controllers;

use Services\DB;
//Import Faker
use Faker;

class UsersController {
    public $conn = null;

    public function __construct() {
      // create connection.
      $this->conn = (new DB())->connectToDatabase();
      //Start session
      session_start();
    }

    /**
     * Create a user at random
     */
    public function createRandomUser() {
      try {
        // Use the Faker PHP library to generate random data
        $faker = \Faker\Factory::create();
        $gender = $faker->randomElement(['male', 'female']);
        $age = $faker->numberBetween(18, 100);
        //For convinience purposes I will give each user the same password of  'helloWorld' which we hash of course
        $hashPassword = password_hash('helloWorld', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users(`name`, `email`, `password`, `gender`, `age`)
                VALUES (
                    '$faker->name',
                    '$faker->email',
                    '$hashPassword',
                    '$gender',
                    '$age'
                  )";

        if (mysqli_query($this->conn, $sql)) {
            echo "New random user created successfully";
        } else {
            echo "Error: ". $sql ."<br/>". mysqli_error($this->conn);
        }
        mysqli_close($this->conn);
      }
      catch(\Exception $e) {
          var_dump($e->getMessage());
          exit;
      }
    }

    /**
     * Function to create 10 random users instead of doing it one by one
     * @return void
     */
    public function createMany() {
      try {
        $faker = \Faker\Factory::create();
        //For convinience purposes I will give each user the same password of  'helloWorld' which we hash of course
        $hashPassword = password_hash('helloWorld', PASSWORD_DEFAULT);
        //Generate our 10 random profiles/users
        $data = '';
        for ($x = 0; $x <= 10; $x++) {
          // Use the Faker PHP library to generate random data
          $gender = $faker->randomElement(['male', 'female']);
          $age = $faker->numberBetween(18, 100);

          $data .= "( '" . $faker->name . "', '" . $faker->email . "', '" . $hashPassword . "', '" . $gender . "', " . $age . "),";
        }
        $data = rtrim($data, ","); // Remove trailing comma

        $sql = "INSERT INTO users(`name`, `email`, `password`, `gender`, `age`)
                VALUES " . $data ;

        if (mysqli_query($this->conn, $sql)) {
          echo "Ten (10) new users created successfully";
        } else {
          echo "Error: ". $sql ."<br/>". mysqli_error($this->conn);
        }
        mysqli_close($this->conn);
      }
      catch(\Exception $e) {
          var_dump($e->getMessage());
          exit;
      }
    }

    /**
     * Returns all the possible matches for a user
     */
    public function possibleMatches() {
       // if(!isset($_SESSION['access_token']) ){
       //   header("Location: http://muzz.test/api/login");
       // }
      try {
        //Retrive our parameters
        $id = $_GET['id'];
        //Find our user from the database
        $sql = "SELECT * FROM users WHERE id = $id LIMIT 1";
        //If we successfully query the db
        if($result = mysqli_query($this->conn, $sql)) {
          //If the results don't come back empty
          if(mysqli_num_rows($result) > 0) {
            $user  = $result->fetch_assoc();
            $gender = $user['gender'];
            //For the time being we retrive all the users of the opposite gender
            $sql = "SELECT id, name, gender, age FROM users WHERE gender != '$gender'";
            if($result = mysqli_query($this->conn, $sql)) {
              if(mysqli_num_rows($result) > 0){
                //Return all the users we find
                echo json_encode($result->fetch_all(MYSQLI_ASSOC));
              }
            }
            return 'No matches found!';
          }
          return "User doesn't exist";
        }
        else{
            echo "Error: ". $sql ."<br/>". mysqli_error($this->conn);
        }
        mysqli_close($this->conn);
      }
      catch(\Exception $e) {
          var_dump($e->getMessage());
          exit;
      }
    }

    /**
     * Respond to a profile and store in the database
     */
    public function swipe() {
       // if(!isset($_SESSION['access_token']) ){
       //   header("Location: http://muzz.test/api/login");
       // }
      try {
        //Retrive our parameters
        $userID = $_REQUEST['userid'];
        $profileID = $_REQUEST['profileid'];
        $preference = filter_var($_REQUEST['preference'], FILTER_VALIDATE_BOOLEAN);
        //Insert into the matches table a new record
        $sql = "INSERT INTO matches(`user_id`, `profile_id`, `preference`)
                VALUES (
                    '$userID',
                    '$profileID',
                    '$preference'
                  )";
        if (mysqli_query($this->conn, $sql)) {
          // echo "New match added successfully";
          /* Check if there is a match
          I will go on a limp here and assume that a profile_id is a user_id. It could be the case of course that these are 2 different entities in the databse on a one to one relationship.
          It could of course be to not overcomplicate the users table and that way you can use a profile for analystics such as #ofTimes a user accessed the app in a day
          or #noMatches or even flags as offensive profile and so on
          */
          //No point seeing for a match if the preference is a NO
          if ($preference) {
            //Following the above logic if user A likes profile B (user B) then if we search if user B liked profile A (user A) and preference is YES then we have a match
            $sql = "SELECT * FROM matches WHERE user_id = $profileID AND profile_id = $userID AND preference = 1 LIMIT 1";
            if($result = mysqli_query($this->conn, $sql)) {
              //If we have a result in the db then we have a match
              if(mysqli_num_rows($result) > 0){
                //Return that we found a match
                echo 'You have a match!';
              }
            }
          }
        } else {
            echo "Error: ". $sql ."<br/>". mysqli_error($this->conn);
        }
        mysqli_close($this->conn);
      }
      catch(\Exception $e) {
          var_dump($e->getMessage());
          exit;
      }
    }

    /**
     * Returns all the possible matches for a user that you have not already swiped on
     */
    public function possibleMatchesImproved() {
      // if(!isset($_SESSION['access_token']) ){
      //   header("Location: http://muzz.test/api/login");
      // }
      try {
        //Retrive our parameters
        $id = $_GET['id'];
        //Find our user from the database
        $userSql = "SELECT * FROM users WHERE id = $id LIMIT 1";
        //If we successfully query the db
        if($result = mysqli_query($this->conn, $userSql)) {
          //If the results don't come back empty
          if(mysqli_num_rows($result) > 0) {
            $user  = $result->fetch_assoc();
            $gender = $user['gender'];
            //Find all the user's existing matches
            $matchesSql = "SELECT profile_id FROM matches WHERE user_id = $id";
            if($result = mysqli_query($this->conn, $matchesSql)) {
              if(mysqli_num_rows($result) > 0){
                $notIn = '';
                $resultArray = $result->fetch_all(MYSQLI_ASSOC);
                foreach ($resultArray as $profile) {
                  if ($profile === end($resultArray)) {
                    $notIn .= '\'' .  $profile['profile_id'] . '\'';                    
                  } else {
                    $notIn .= '\'' .  $profile['profile_id'] . '\',';                    
                  }
                }

                //For the time being we retrive all the users of the opposite gender
                $sql = "SELECT * FROM users WHERE gender != '$gender' AND id NOT IN ($notIn)";
                if($result = mysqli_query($this->conn, $sql)) {
                  if(mysqli_num_rows($result) > 0){
                    //Return all the users we find
                    return $result->fetch_all(MYSQLI_ASSOC);
                  }
                }
                return 'No matches found!';
              }
            }
          }
          return "User doesn't exist";
        }
        else{
            echo "Error: ". $userSql ."<br/>". mysqli_error($this->conn);
        }
        mysqli_close($this->conn);
      }
      catch(\Exception $e) {
          var_dump($e->getMessage());
          exit;
      }
    }
}
