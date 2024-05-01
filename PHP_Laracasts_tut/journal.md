user will create brand new notes that are persisted in database.
to create a new note, there should be a button, so user can click on it

clicking on this button should take us where?where should we go? so in my notes.php where I list all my notes I would create a button to make new note.

Let's add route where create note button should take us.

`'/notes/create' => 'controllers/note-create.php';`

before I add route in my routes.php file, Have a look at router.php

```php
<?php
$uri = parse_url($_server['request_uri'])['path'];


$routes = [
    '/'=>'controllers/index.php',
    '/about'=>'controllers/about.php',
    '/contact'=>'controllers/contact.php',
    '/notes'=>'controllers/notes.php',
    '/note'=>'controllers/note.php'
];

function urlis($uri){
    return $_server['request_uri'] ===$uri;
    }


    function routetocontroller($uri, $routes){
        if(array_key_exists($uri, $routes)){
            require $routes[$uri];
        }else{
           abort();
        }
        }

    function abort($code=404){
        http_response_code($code);
        require "views/{$code}.php";
        die();
    }
routetocontroller($uri, $routes);
```
First Thing I Notice is all our routes declaration are jumbled up against all our logic in router.php, If We Could Extract These routes in their own file , It Would be cleaner. So We don't have this junk every single time, when you need to add or tweak routes.


So Create a New File routes.php and take all these routes out and move it into its own file.
Instead of creating new variable, Why don't we just return directly just like we did in config.php file. see below.

// routes.php
return [
    '/'=>'controllers/index.php',
    '/about'=>'controllers/about.php',
    '/contact'=>'controllers/contact.php',
    '/notes'=>'controllers/notes.php',
    '/note'=>'controllers/note.php'
];

So create this controller note-create.php and template(html) file view note-create.view.php

// routes.php
return [
    '/'=>'controllers/index.php',
    '/about'=>'controllers/about.php',
    '/contact'=>'controllers/contact.php',
    '/notes'=>'controllers/notes.php',
    '/note'=>'controllers/note.php',
    "/notes/create" => "controllers/note-create.php"
];
// router.php
$routes = require('routes.php');

so far what I have done is ..create a "new note" button underneath all notes, that button href is set to /notes/create and I created controller and view file. I extracted code from router.php to routes.php

while implementing html and css for form and buttons, i learned button vs anchor tag, button does not have href. but we can style anchor just like button with css.


Before I Start Working On forms, Why Don't We Have Little Conversations About naming conventions for routes.

RESTful routing is a way to map URLs to specific resources and operations in a consistent, standardized manner. It uses HTTP methods to indicate what kind of action is being taken on a resource.

RESTful routing follows a set of conventions for structuring URLs (routes) and associating HTTP methods with CRUD (Create, Read, Update, Delete) operations on resources.

In note-create.view.php i created form so user can type message to send. I have textarea and I would emphasize on "name" attribute , you can't exclude it otherwise form data is not going to be part of query string. so make sure all of your form inputs include corresponding names.

So that is get request, by default form will submit a get request. so clicking on different page links is also a get request, get me that page, get me that page.

But It is not the only type of request you can make, there is many more. one of other is post request, so why don't we tweak the form and change It to method="post".

so we submit the form again with some data, It refreshes and notice this time It did not include form data as part of query string.

With the post request you are doing It in a sort of shadows a little bit as part of the message body and later we will learn how to inspect that.


get requests are considered idempotent, It means no matter how many times you make the request whether I do It 1 time or 10,000 times, I am not doing anything destructive, I am still going to get same result. e.g clicking on page link will fetch me same page everytime i click.

But that is not true for things like submitting the form, once I submit the form, It will persist in the database, so that means If I did It 10,000 times I will have as many records, that is not idempotent, so we should not use get request for this form submission.


Now I have changed the method to post, I submitted the form where did it go?

In the form I changed the request type to POST, that's why when you submit the form It is POST but It is GET when I visited the form page.

// at this stage, we know we are responding to
// the submission of form

``php
if($_SERVER['REQUEST_METHOD']==='POST'){
    dd('you submitted the form');
}
```
Another thing is form action attribute , When you don't specify the action attribute in an HTML form, the form data is submitted to the same URL that rendered the form.

In both cases, you can access the form data using the $_POST superglobal if the form method is POST, or the $_GET superglobal if the form method is GET.


Next thing is how do I get the information from the form? As It turns out we have new superglobal $_POST


Think about it, We added a form and we figured out how we can respond to the submission of form

We learned how we can grab all of the data or the attributes of the form

So the next step would be perform any validations that is necessary

and then persist it or save it to the database.