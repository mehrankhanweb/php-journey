learnt about Authentication vs Authorization
so far we displayed all notes that were created by a specific user. Notes that we don't see are created by another user

It's good but it's actually not good we have introduced unwillingly a major security concern

Think about it, I can access the note that is not created by the user that we mentioned in our query, In the browser if you manually add the id number of note of another user, you can access it. This is a really big problem

In real life, notes you create should not be accessible to anyone that has access to the browser. Clearly this is a very big problem. This is where Authorization comes into play.

Where is the problem? It is the note.php controller that is responsible to handle the note.

```php
$note = $db->query("select * from notes where id = :id",['id'=>$_GET['id']])->fetch();
```
If you look at the code above, it just fetches the id and execute the query, there is no authorization in place.

we have few ways to deal with it,
First one might be to simply update the query.

```php
$note = $db->query("select * from notes where user_id=:user and id=:id",['user'=>1, 'id'=>$_GET['id']])->fetch();
```
It is gonna be hardcoded for now because Remember we haven't reviewed authentication yet, but we will cover it soon.

At the moment we are just going to assume that user with id of 1 is the currently authenticated user, until we learn it more.

Now if we manually try to change id, we get

Warning: Trying to access array offset on false

we load the view and we are interacting with false as If it were not an array that's why we get that error or warning in browser because we didn't get anything from the database.

How do we deal with situations like this when false is returned?
```php
if(!note){
    abort()
}
```
// in router.php we have already defined abort()

```php
function abort($code=404){
    http_response_code($code);
    require "views/{$code}.php";
    die();
}
```
If you think about, there is couple different ways $note would evaluate to false

You are trying to access $note with an id that does not exist, e.g 600000, There is no corresponding record in database, so we should say "Sorry Page not found", So abort() is fine here

But in another way, There is corresponding record exist in database But you didn't create it, or you are not authorized to see that page, that's a very different thing

So We have two reasons to abort

```php
if(!note){
    abort(404)
}
if($note['user_id']!=1){
    abort(403); // you need 403 page
}
```

If you add above condition , it means from query you can remove the check about user_id.


