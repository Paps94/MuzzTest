<?php

namespace Api;

class Api
{
    public static function routing($current_link, $urls) {
        try {
            foreach($urls as $index => $url) {
                if($index != $current_link) {
                    continue;
                }

                // Getting controller and method out.
                $routeElement = explode('@', $url[0]);
                $className = $routeElement[0];
                $function =  $routeElement[1];

                // Check if controller present.
                if(!file_exists("controllers/". $className . ".php")) {
                    return "Controller not found";
                }

                $class = "api\controllers\\$className";
                $object = new $class();
                $object->$function();
            }
        }
        catch(\Exception $e)
        {
            var_dump($e->getMessage());
        }
    }
}
