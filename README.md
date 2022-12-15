# Muzz Test Exercise

Below you will find my trail of thought as well as some key information on how I go about working on something new for the first time!

## Getting Started

After going though the instructions I tried to figure out if I had any questions but almost everyting seemed staight forward. I say almost as
I feel my project architecture (and therefore DB architecture) is perhaps slightly different to what you would expect!

I decided to use Pure PHP (7.3.12 - Didn't want to go through the trouble to update to the latest version as usually you get issues so this stable version would do)
 for the back-end (I do not understand why some people prefer this over a nice framework like Laravel) and for the front-end I was gonna go with React/Typescript
 but at the end I decided to go with just React due to the time limitation of only 1 spot left. Database is MySql. I did not use Docker.

I thought to myself that I should start with the Front-end, as I think that will take me the most time but I changed my mind and I started to build the Back-end first!

### Prerequisites

Composer - I tried to make as little use of 3rd party packages as possible and when i needed to install one like Faker for my random data or php-jwt for my tokens, I used composer which is what I am most familiar with!
Npm - I also have installed npm and used it for other needed packages like Axios for my calls to the backend or something insignificant like SweetAlert2 to make the test feel a bit more complete!

### Setup
After pulling the code you would need to do the following things:

  - A) Change the local host db credentials in the api/services/DB.php file to what you are using for local development
  - B) You can use my exported dev db which I will inclide in the zip or create your own but you need some tables like users, tokens and matches
  - C) Go into /api and run `composer install` to install all dependancies
  - D) While at it run your local server enviroment. As mentioned above I use WAMPServer and created a virtualHost for this project or go into the index.php directory and run php -S localhost:3000
       or whichever port you want to run the backend
  - E) Go into /frontend and run `npm install` to install all dependancies
  - F) While in that folder run `npm run dev` to start the react project. If everything goes well you should see the project ready to test it!

### Over the top run down

First things first.. I loaded my WAMP server, created my virtual host (muzz.test) cause I think it's cleaner working that way for solo projects, created my database and tables. 
After that I started writting my back-end. Created my DB.php file which is my connection to my database of choice (local in this case), my routing file (i understand there are much better
ways of doing this in a more frameworky way but for this test this was fast and easily customizable), the htaccess file to allow all routes (not good practice, I know, but you wouldn't worry
about this if you are using a framework). I created 2 Controllers that would contain the logic required for Part 1 and 2. Oh forgot to mention that all my routes in routing.php go through `api/` first.
I added just a bit of server side validation (very basic stuff) same with the client side validation (i definitely cut corners with this as it was not even in the requirements but you should ALWAYS validate data you
will store in your database!)

For part 3 I used Vite to create a react projects (create-react-app is way to slow for my liking). I installed some dependancies using npm like Tailwind for my styling. I didn't want to spend too much time on the front end due to the restriction of time but my need to make it nice looking got the better of me and i even spend the time
to add a night mode and a fancy animated cursor. While doing my research i also found a nice package to do the swiping requirements (I would never do this for a commercial project, would definitely get influenced by it
but never just copy paste a package even if it's MIT licensed).

I started creating my components and tried to split them in a way that make sense. I even on purpose tried to use techniques I learned in the past about react (the jsx files under UI) so as when i get feedback I know
which of technique is better. Authorisation is split between the Login component under Auth and the auth-context.jsx under store. Together I dictate the logic of what is rendered on the screen -are you not logged in? Boom 
log in component! Are you logged in? Boom Swipe time!

## Issues I did not think of

I knew I would run into problems with CORS (Cross-origin resource sharing) since I have WAMP running on one server and my react app on another port but I thought if i add to my htaccess file the necessary headers all is fine.. WRONG. 
I spent some time trying to figure out why it wouldn't let my request through and till this point for some reason Axios is not letting my POST requests through and IDK why! Hopefully I will figure it out soon!

**UPDATE I figured it out.. The Axios docs are wrong. It says if you want to make a POST request with parameters you need to pass the 'data' option with your parameters but that throws the above error. A workaround, which is not good practice, is to pass them as url parameters using the 'params' option instead!

I tried to use react-modal to literally put my login form in a modal but for some odd reason when it would try to import it it would throw a 504 error. I thought to myself maybe it's a versioning issue or something and i tried to use
earlier versions but no nothing worked so I just did not use a modal. Same thing happened with Axios AND SweetAlert when i tried to import it in my components, found a workaround which is to use CDN instead.

**UPDATE#2 It will not let me save anything in the session or set any cookies. Maybe it has something to do with my permissions need to investigate further

### Testing

I really hope a) I get everything working  and b) also do the extras which are simple enough in my eyes but I need to fix the issues I mentioned above before I can even consider writing tests!

### Final Thoughts and Comments

I gave myself a time limitation for this task and at the time of writting this I am all out of time. I believe I got the test to a respectable lever. Might have went a little bit over the top with some stuff
and if I had time I would re engineer some logic like my routing plus try and clean up my react components, possibly even my db architecture (add a preferencce table for the bonus task for example to take those into consideration before showing potential profiles and so on!).

There are currently 3 issues and a smaller one which I am not happy I wasn't able to solve on time.
  - #1 I do not pass the user details after a successful log in into my swiping module therefore the logic is static (for testing i woould manually pass in a user_id). To make this ever so slightly better for you I created a config file which has a user_id and you can change it there to do your testing.
  - #2 There is an issue with the swipe buttons and the useMemo hook. On page load it's empty but once you save the file (Swipe.jsx) it populates and you can swipe as expected.
  - #3 The last issue is that I had issues with sessions and cookies during testing andw as not able to store them.

  For both of the above I would love to know the solution/how you would tackle this issues as part of my feedback

## Acknowledgments

  - Hat tip to my cats who turned off my pc more times I could count! They made me more resilient than ever since I did not toss them off the 8th floor right into the Thames! I will get an external power button to avoid that from happening in the future of course!
