<?php
/**  
 * This is the file comment for the fromYummly class.
 * 
 * @package    Wpf_Recipe_Costing\Includes\Admin\Views
 *
 * http://api.yummly.com/v1/api/recipes?_app_id=app-id&_app_key=app-key&q=your_search_parameters
 * http://api.yummly.com/v1/api/recipes?_app_id=YOUR_ID&_app_key=YOUR_APP_KEY&q=onion+soup&requirePictures=true
 * 
 * Requires array $query to be passed:
 * $query['searchterms']
 * 
*/



use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class fromYumml($query) {

    public funtion init() {
        
        $this->getRecipesFromYummly($query);
        
    }
    
    protected function getRecipesFromYummly($query) {

        $query = $query['searchterms']; //'onions+soup';
        $requirePictures = true;
        $id='63edba96';
        $key='722923b6f9585fe072f9f1bc0d820d46';


        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://api.yummly.com/v1/api/',
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);

        $request = new Request('GET', 'http://api.yummly.com/v1/api/');
        $response = $client->send($request, ['timeout' => 2], [
            'query' => ['_app_id' => $id,
                       '_app_key' => $key,
                        'q' => $query,
                        'requirePictures' => $requirePictures
                       ]
            ]);

        // Check if a header exists.
        if ($response->hasHeader('Content-Length')) {
            // Get all of the response headers.
            foreach ($response->getHeaders() as $name => $values) {
                echo $name . ': ' . implode(', ', $values) . "\r\n";
            }

            //$response = json_decode($response);

            return $response;
        }
    }
    
}