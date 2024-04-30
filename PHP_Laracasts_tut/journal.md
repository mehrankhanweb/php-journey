we will learn about magic numbers which has special meaning or significance but it has not been declared yet in a variable

```php
if($note['user_id']!=1){
abort(403);
}
```
We know the answer of user_id = 1 , as we are hardcoding 1 as the current authorised user.
But in 6 months time, we will forget what does it referes to

One option would be to extract that to a variable
It provides more clarity, what the value is.

We can do the same thing with 403

```php
$forbidden = 403;
if($note['user_id']!= $forbidden){
abort(403);
}
```

That will work, but I don't like it in this case, I might want to abort with 403 all over the place. Do I have to assign this variable every single time?


Instead, there is nothing preventing you from creating another file and create constants for each of the common status code that we might want to return.

```php
//Response.php
class Response{
const NOT_FOUND = 404;
const FORBIDDEN = 403;
}
// Now you require this in index.php, keep router.php in the end.
// Otherwise you will get
// Fatal error: Uncaught Error: Class "Response" not found
```
now we learnt about class "constants" that can be useful if you need to define some constant data within a class.

A class constant is declared inside a class with the const keyword.

A constant cannot be changed once it is declared.

Class constants are case-sensitive. However, it is recommended to name the constants in all uppercase letters.

We can access a constant from outside the class by using the class name followed by the scope resolution operator (::) followed by the constant name

Or, we can access a constant from inside the class by using the self keyword followed by the scope resolution operator (::) followed by the constant name

we can update our code like so

```php
if(!$note){
abort(Response::NOT_FOUND);
}
if($note['user_id']!=$currentUserId){
abort(Response::FORBIDDEN);
}
```
