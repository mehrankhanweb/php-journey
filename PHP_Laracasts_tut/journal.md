continuing 8.md...
==================


I have created a button underneath all notes to create a new note, clicking on button leads me to a new page `/notes/create` and corresponding controller file is note-create.php and there is html file for that controller which will have a form inside it.

when user submitted form, and we make sure with this conditional.

```php
if($_SERVER['REQUEST_METHOD']==='POST'){

}
```
we have also learnt that to access data user submitted , we use $_POST superglobal.

Now the next thing is insert data into database and for that we use INSERT query.

Remember we need to instantiate Database class again as we are interacting with database while using INSERT query.

Values will be wildcards to avoid any sql injection, we are leveraging prepare statements.

```php
// note-create.php

$config = require('config.php');
$db = new database($config['database']);

// I am copying above last time and then we will learn cleaner way

$heading = 'create note';
if($_server['request_method']==='post'){
    $db->query('INSERT INTO notes(body,user_id) VALUES (:body, :user_id)',[
    'body'=> $_POST['body'],
    'user_id' => 1
    ]);
}
```
now we always assume user is guilty, we have avoided sql injection by leveraging prepare statments but user can type whatever message he wants in text area, and submit it.

Insert this text in textarea → Work on <strong class="text-red-500">something</strong>  → submit What do you think is gonna happen here?
In my list of notes It is written bold and red.
This could be really big problem, here's why
What about that <h1 style="font-size: 100px"> ah ah ah...</h1><script>alert('hi from js')</script>
Go to all notes and every single person will see this alert message and very big text, imagine you are building something like stackoverflow thousands and thousands of people are gonna see this.
None of this is desirable, we can't give user the control.
How can we fix it?
We got couple of options here,
One method would be to sanitize the body of the note before It enters the database, that would work.
Another option that is very common is simply allow it in the database and then escape it when you display it, and I show you what it looks like.
Let's go to our list of notes notes.view.php, right up here, thats where we list our notes

```php
<li>
<a href="/note?id=<?= $note['id'] ?>" class="text-blue-500 hover:underline">
    <?= $note['body'] ?>
    </a>
</li>
```

Here instead of blindly echoing whatever user has typed into the form, we can put a built in php function


htmlspecialchars()

```php
<li>
<a href="/note?id=<?= $note['id'] ?>" class="text-blue-500 hover:underline">
    <?= htmlspecialchars($note['body']) ?>

    </a>
</li>
```

Inspect the source to see how htmlspecialchars converted the angle brackets and the quotes to their html entity equivalent.
It is no longer treated as html tags , It is treated as long string.
That's exactly what we want.
Good deal, seems like we have solved the problem, we are no longer displaying javascript alert for every single user who loads this page


We have also restricted the author ability to control the presentation of this notes list, which is good.


But as I click on that note, alert pop up comes back We are right back in the same boat.

This is where we get this idea, user is guilty until proven innocent, we don't even need to say until proven innocent, just assume they are trying to do something bad or malicious, thats why we run any user provided input or values through functions like this htmlspecialchars.
It is gonna convert all applicable characters to html entities.
After we applied htmlspecialchars on notes.view.php, and on inspection or view source, this is what I see, It is just a string
&lt;h1 style="font-size: 100px"&gt; ah ah ah...&lt;/h1&gt;&lt;script&gt;alert('hi from js')&lt;/script&gt;

htmlspecialchars converted the angle brackets and the quotes to their html entity equivalent. It is no longer treated as html tags , It is treated as long string. That's exactly what we want.

After clicking on that note, on inspect or view page source it is still showing h1 and script tag

That's why we need to do same htmlspecialchars where we display the note.

In note.view.php → <?= htmlspecialchars($note['body']) ?>

That should solve the problem.

Validating User Input
There is still more things to consider

What if user does not provide any text and just click on submit

it, It creates empty record in the database, and empty notes will be rendered where we displaying our all notes. You can't see but you can inspect and see empty lis we should not allow that.
It can be overwhelming at sometimes, there are so many things you have to think about.
So far we leverage prepared statements to avoid the risk of sql injections.
We used the htmlspecialchars function to convert any potential html

Now it seems like we also need a layer of validation to ensure the user when submit the form is providing us the data or values that we expect

I know what you are thinking about..use of required attribute
You might think, problem solved! and you didn't need to write a single line of code.
It's useful because it provides immediate feedback to the user to help them but it doesn't actually solve the problem. you can think of it as layer of browser validation or client side validation.
But there is nothing preventing user from bypassing this validation.
There is nothing preventing me from manually submitting this post request → go to terminal and use curl for that:

curl -x → that declares the request so curl -x post

url is localhost:3000 so curl -x post http://localhost:3000

uri is /notes/create so curl -x post http://localhost:3000/notes/create

-d to provide the data to go along with this post request curl -x post http://localhost:3000/notes/create -d

I can say body should be nothing or empty curl -x post http://localhost:3000/notes/create -d 'body='

It should persist in database table now, see we bypassed this required attribute | client side or browser side validation

I can use curl over and over and we are right back in the same boat.

don't get me wrong , it is useful because it provides instant feedback to the user but we can never exclusively depend on it.

Even though client-side validation can improve user experience by providing instant feedback, it's essential to perform server-side validation as well to ensure data integrity. In your PHP script that handles the form submission, check if the note field is empty. If it is, prevent the insertion of the empty note into the database and provide feedback to the user.


Go back to our controller note-create.php, it sounds like and I want you to know it is very important that before we run our query we should first confirm that body needs our criteria.

```php
if($_SERVER['REQUEST_METHOD']==='POST'){
    // lets define our criteria before we run our query
    $errors = [];
    if(strlen($_POST['body'])===0){
        // if it failed .. append it to array
        $errors['body'] = 'a body is required';
    }
     /*
     think about it.. if $errors is not empty, should we still run
     our query to update the database? definitely **NOT**
     We should effectively abort and return to the form
     and then notify the user about what they did wrong.
     or what validation error occured.
     We can use empty() function for that.
     */
    if(empty($errors)){
    $db->query('insert into notes(body, user_id) values(:body, :user_id)',[
        'body'=>$_POST['body'],
        'user_id' => 1
    ]);
    }
    // Now it was **safe** to proceed to query to database.
    // Otherwise we skip the query and proceed to view file.
}
require 'views/note-create.view.php';
```


But now when we require the view, we have $errors variable

Try inserting empty records in database, and if everything was done correctly, we no longer have those records in our database table. so our validation is infact working.

Next Issue is we are not providing feedback to the user so user has no clue what the problem is..


Let's go into our note-create.view.php file, and right below our <textarea>, we say check the $errors array and see if you have anything for ['body'] and if we do we should display validation message to the user.

<?php if(isset($errors['body'])): ?>
  <p class="text-red-500 text-xs mt-2"><?= $errors['body'] ?></p>
  <?php endif; ?>


What If Someone Writes Massive Amount of Text? Which We Don't Want To Allow.
Looks like we need to allow maximum amount of words that we want to allow, you want to set a minimum and maximum number of characters. Ok Let's run another check in our controller file.

```php
 if($_SERVER['REQUEST_METHOD']==='POST'){
    // lets define our criteria before we run our query
    $errors = [];
    if(strlen($_POST['body'])===0){
        // if it failed .. append it to array
        $errors['body'] = 'a body is required';
    }
     /*
     think about it.. if $errors is not empty, should we still run
     our query to update the database? definitely **NOT**
     We should effectively abort and return to the form
     and then notify the user about what they did wrong.
     or what validation error occured.
     We can use empty() function for that.
     */
    // lets run another check
    if(strlen($_POST['body']) > 1000){
        $errors['body'] = 'the body cannot be more than 1,000 characters.';
    }


    if(empty($errors)){
    $db->query('insert into notes(body, user_id) values(:body, :user_id)',[
        'body'=>$_POST['body'],
        'user_id' => 1
    ]);
    }
    // now it was **safe** to proceed to query to database.
    // otherwise we skip the query and proceed to view file.
}
require 'views/note-create.view.php';
```


Sure Enough, I get the error If I exceed the Limit but I don't wanna Lose all that text that I typed, and write from scratch again that would be bad user experience.

Maybe as a user I would have preferred to fix the mistake rather than write it from scratch.
How do we do this?



Go to our note-create.view.php file, and we need to insert code inside <textarea>echo out here</textarea>

how about this? <?= $_POST['body'] ?> This might be your first thought.

In html textarea, there is no text by default, It is written like this <textarea class=""></textarea>

But This is what we gonna get undefined array key "body" inside textarea

Can You Tell Me Why?

Because when this page loads for the first time, It is get request and at that stage post superglobal will be empty and there will be no body key.

One solution Would be to do something like this using isset()

isset() → The isset() function is a built-in function of php, which is used to determine that a variable is set or not. if a variable is considered set, means the variable is declared and has a different value from the null. In short, It checks that the variable is declared and not null.

```php
<textarea>
<?= isset($_POST['body']) ? $_POST['body'] : '' ?>
</textarea>
```


My editor is Squawking that there is an easier way to do that, using null coalescing operator


null coalescing operator explain it in wiki notes with examples


  <textarea> <?= $_post['body'] ?? '' ?> </textarea>

  Next we will refactor all of this validation code as you can imagine it could get very very messy very quickly.

