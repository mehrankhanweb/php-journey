
$notes = $db->query("select * from notes where id=1")->fetch();
using fetch is appropriate as it is a single result and I don't need a list or collection.


As you can imagine in real life, we will not be hardcoding id instead the user will click on note with an id of 1 or other post with an id of 2,

A query string is part of the full query, or URL, which allows us to send information using parameters as key-value pairs. A query string typically follows a ? in the URL like http://localhost:3000/?id=2

Instead of creating a separate page for each note, you can have one page that fetches data based on the ID passed through the query string.

Consider a list of notes on a page. When a user clicks on one of the notes, they are redirected to a page with more information about that note. This is what we are going to achieve next.

Next step is to fetch all relevant notes from database.

$notes = $db->query("select * from notes where user_id=1")->fetchAll();

user_id =1 will be hardcoded for now because Remember we haven't reviewed authentication yet, but we will cover it soon.

Foreign key references to Primary key in Referenced Table Change user_id to 1 before creating foreign key constraint, as by default it is 0.
Create foreign key constraint on notes table, because every note should be assigned to some user, Also
What happens when user is deleted and he has bunch of notes in notes table.
Should these notes be deleted as well, or should the user be restricted from being deleted.
we got ON Update and On Delete hooks and these hooks have some options,
Cascade → delete those related records if user deleted.
Restrict → restrict the user to be deleted.
Different applications will have different requirement.
Test out by making few records in notes and then delete the user to see if all records in notes are cascaded by hitting command + r (refresh).
So foreign key and foreign key constraint maintain our database consistency.


we need this for now but we make them available throughout application soon.
$config = require "config.php";
$db = new Database($config['database'], 'root','');


once i run this in notes.php
$notes = $db->query("select * from notes where id=1")->fetchAll();

I can access $notes variable in notes.view.php file.

<?php foreach($notes as $note): ?>
        <li><a href="#" class="text-blue-500 hover:underline"><?= $note['body'] ?></li></a>
        <?php endforeach; ?>
I used shorthand syntax for foreach above. notice the : colon.

But NOW what Should Happen When user clicks on one of these notes? Where Should we direct them? (must include in wiki notes)

In the future we will learn how to do /notes/unique-slug-for-the-note in the address bar, which will look something like below.

<li><a href="/notes/unique-slug-for-the-note" class="text-blue-500 hover:underline"><?= $note['body'] ?></li></a>

But right now our router does not allow this, so we keep it simple.

we are opting for a simpler approach where you pass the note ID through the query string, like /note?id=1
It's nice and easy way to get started!

Inside foreach loop we make note id dynamic like below.
href="/note?id=<?= $note['id'] ?>"
I have new controller and view file for note page, make sure you register that endpoint with router.

```php
$routes = [
    '/'=>'controllers/index.php',
    '/about'=>'controllers/about.php',
    '/contact'=>'controllers/contact.php',
    '/notes'=>'controllers/notes.php',
    '/note'=>'controllers/note.php'
];
```
So Far We have listed all the notes and Every note has specific id that We have provided throug URL

Next Task clicking on that note should take me to /note?id=idOfTheNote

What does that tell you?
It is clear that In the note.php file, We need to retrieve the note ID from the query string using $_GET['id']

After retrieving the id , What Are We gonna do with that id? What Do You Think?

Just execute the sql query using that id so we can display it.


Remember You again need access to the database connection to execute SQL queries and fetch the details of the specific note, but this time you need to fetch that specific id that you just retrieved with `$_GET` So you require this code in note.

```php
$config = require('config.php');
$db = new Database($config['database'], 'root','' );
```

`$notes = $db->query('select * from notes where id = $_GET['id'])->fetch();`

With these changes, when a user clicks on a note, they will be directed to the note.php page with the corresponding note ID passed through the query string.

The note.php page will then fetch and display the details of that specific note based on the provided ID.

In note.view.php file
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
    <p class="mb-6">
    <a href="/notes" class="text-blue-500 underline">Go Back...</a>
    </p>
    <p>
    <?= $note['body'] ?>
    </p>
    </div>
</main>

- so far so good, but there is a problem as we are inlining id as part of sql query

- Inlining raw user inputs directly into SQL queries opens up the risk of SQL injection, where malicious users can manipulate SQL syntax to execute unintended commands. This could lead to unauthorized data access, data loss, or even full control of the database.


Using prepared statements with bound parameters is the best practice to avoid these issues. Prepared statements separate SQL code from data, ensuring that user inputs are handled safely and preventing SQL injection. This approach also improves code readability and maintainability.

so here is the solution, I am no longer going to inline it directly, instead I will show you two ways to format it. I will replace it with ? mark. This ? we will bind to the query.
$query = "select * from posts where id=?";
But Here's the key thing to understand, the query and bound parameter almost travel in two different boats, maybe that's the way to think of it.
You will send through the query to mysql and then in a separate boat you will send through the parameters, and when you take this approach you remove any possibility of improper formatting or sql injection.
How do I bind the parameters.
In our Database.php Class when we call the execute method
$statement->execute(); → This is where you can bind the parameters, and it will take the form of an array [], but this one needs to pass through here → from the following code like below.
$posts = $db->query($query,[$id])->fetch();
So to make it work, that [] should be dynamic. why don't we call it $params and then we accept it as part of query method signature, and we will default it to an empty []


```php
// this query function is inside Database custom class.
 public function query($query, $params=[])
    {
        $statement = $this->connection->prepare($query);
        $statement->execute($params);
        return $statement;
    }

// when I call query method, I can bind it as second parameter like below.
$posts = $db->query($query, [$id])->fetch();
```

Second Method
instead of ? mark,
$query = "select * from posts where id=?";
or I could use a key, starting with :(any name), in this case :id would make sense.
Now the only difference would be I pass as an associative array, where I reference the key and the value.
$query = "select * from posts where id=:id";
Now you have to pass an associative array

$posts = $db->query($query, ['id'=>$id])->fetch();

So whether you reach for ? or :id (keyed wild card parameter not sure what is it called) but it doesn't matter, choose the one you like best.
But never ever accept user input and inline in query parameter, drill it in your head, you don't wanna do it.
By using bound parameters, you ensure that user input is treated as data rather than executable code, making your application more secure against SQL injection vulnerabilities.

I am going to use :id approach.


so to fetch all notes on notes.php page I will do something like this
$notes = $db->query("select * from notes where user_id= :user_id",['user_id'=>1])->fetchAll();
And on note.php page when we fetch a specific note I will do something like this
$note = $db->query("select * from notes where id = :id",['id'=>$id])->fetch();

and in Database.php I refactored query method like this

public function query($query, $params=[]){

	$statement = $this->connection->prepare($query);
	$statement->execute($params);
	return $statement;
	}

    