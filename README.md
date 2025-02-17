# Routing
Simple request router for PHP.


## Installation

Use [Composer](http://getcomposer.org) to install Logger into your project:
```bash
composer require evolutionphp/routing
```


## Usage

### Basic Routing
Basic example for a GET request for home page.
```php
\EvolutionPHP\Routing\Route::get('/', function (){
    return 'This is the home page.'
});

//Or you can use a controller class.
\EvolutionPHP\Routing\Route::get('/home', [HomeController::class, 'index']);

//Dispatch routes
\EvolutionPHP\Routing\Route::dispatch();
```
### Available Router Methods
The router allows you to register routes that respond to any HTTP verb:
```php
\EvolutionPHP\Routing\Route::get($uri, $callback);
\EvolutionPHP\Routing\Route::post($uri, $callback);
\EvolutionPHP\Routing\Route::put($uri, $callback);
\EvolutionPHP\Routing\Route::patch($uri, $callback);
\EvolutionPHP\Routing\Route::delete($uri, $callback);
\EvolutionPHP\Routing\Route::options($uri, $callback);
```
If you need to register a router that responds to multiple HTTP verbs:
```php
\EvolutionPHP\Routing\Route::match(['get','post'], function (){
    // ...
})
```
## Redirect Routes
If you are defining a route that redirects to another URI, you may use the **Route::redirect** method
```php
\EvolutionPHP\Routing\Route::redirect('/home', '/home-page');
```
Or, you may use the **Route::permanentRedirect** method to return a **301** status code:
```php
\EvolutionPHP\Routing\Route::permanentRedirect('/home','/home-page');
```

## Route parameters
### Required Parameters
For example, you may need to capture a user's ID from the URL. You may do so by defining route parameters:
```php
\EvolutionPHP\Routing\Route::get('/user/{id}',function ($id){
    return 'User ID: '.$id;
});

\EvolutionPHP\Routing\Route::get('/post/{post}/comment/{comment}',function ($post_id, $comment_id){
    return 'Post ID: '.$post_id.' | Comment ID: '.$comment_id;
});
```
### Regular Expression Constraints
```php
\EvolutionPHP\Routing\Route::get('/user/{id}',function ($id){
    return 'User ID: '.$id;
})->where('id', '[0-9]+')
```
Or you can use helpers:
```php
\EvolutionPHP\Routing\Route::get('/user/{id}',function ($id){
    return 'User ID: '.$id;
})->whereNumber('id')

\EvolutionPHP\Routing\Route::get('/post/{title}',function ($title){
    return $title;
})->whereAlphaNumeric('title')
```

## Router names
You can assign names to routes
```php
\EvolutionPHP\Routing\Route::get('/user/profile', function (){
    // ...
})->name('user_profile');
```
### Generating URL for a route using its name
```php
$url = \EvolutionPHP\Routing\Routing::generateURL('user_profile');
//Redirect route
\EvolutionPHP\Routing\Routing::redirect($url);
```
If the named route defines parameters, you may pass the parameters as the second argument to the route function.
```php
\EvolutionPHP\Routing\Route::get('/user/{id}', function ($id){
    // ...
})->name('user_profile');

$url = \EvolutionPHP\Routing\Routing::generateURL('user_profile', ['id' => 5]);
```

### Get current route name
```php
\EvolutionPHP\Routing\Routing::routeName();
```

## Route Groups
### Controllers
If a group of routes all utilize the same controller, you may use the controller method to define the common controller for all of the routes within the group. Then, when defining the routes, you only need to provide the controller method that they invoke:
```php
\EvolutionPHP\Routing\Route::controller(UserController::class)->group(function (){
    \EvolutionPHP\Routing\Route::get('/user/dashboard', 'dashboard');
    \EvolutionPHP\Routing\Route::get('/user/profile', 'profile');
});
```

### Route Prefixes
The prefix method may be used to prefix each route in the group with a given URI. For example, you may want to prefix all route URIs within the group with admin:
```php
\EvolutionPHP\Routing\Route::prefix('admin')->group(function (){
    \EvolutionPHP\Routing\Route::get('/users', function (){
        // Matches the "/admin/users" URL
    })
});
```
### Route Name Prefixes
The name method may be used to prefix each route name in the group with a given string.
```php
\EvolutionPHP\Routing\Route::namePrefix('admin')->group(function (){
    \EvolutionPHP\Routing\Route::get('/admin/users', function (){
        // Route assigned name "admin.users"
    })->name('users');
});
```

### Route Middleware
Example of a middleware
```php
class UserAuth(){
    public function handle(\EvolutionPHP\HTTP\Request $request, Closure $next) {
        if(!$request->post('token')){
            \EvolutionPHP\Routing\Routing::redirect('/');
        }    
        return $next($request);
    }
}
```
To use the middleware:
```php
\EvolutionPHP\Routing\Route::middleware(UserAuth::class)->group(function (){
    \EvolutionPHP\Routing\Route::get('/user/dashboard', function (){
        return 'User dashboard.';
    });
\EvolutionPHP\Routing\Route::get('/user/profile', function (){
        return 'User user.';
    });
});
//or define a middleware for a single route
\EvolutionPHP\Routing\Route::get('/user/comments', function (){
    return 'User comments.'
})->withMiddleware(UserAuth::class);
```