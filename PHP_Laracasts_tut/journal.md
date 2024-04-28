I learnt how to interact with mysql, using TablePlus I created notes and users tables. Each note is created by some user, so learnt about foreign key concept. also learnt about id which is primary key.

I learnt PDO methods and features that it provides out of the box to interact with databases.
Connection Handling:

Constructor: Creates a new PDO instance representing a connection to a database.
setAttribute(): Sets attributes on the database handle, such as error handling mode, fetch mode, etc.
exec(): Executes an SQL statement and returns the number of affected rows.
query(): Executes an SQL statement and returns a PDOStatement object representing a result set. it combines the preparation and execution of an SQL statement into a single function call.

Prepared Statements:

prepare(): Prepares an SQL statement for execution and returns a PDOStatement object.
bindParam() / bindValue(): Binds a parameter to the specified variable name or value in a prepared statement.
execute(): Executes a prepared statement.
fetch() / fetchAll(): Fetches the next row from a result set as an associative array or returns an array containing all of the result set rows.

```php
$dsn = "mysql:host=localhost;port=3306;dbname=myapp;user=root;password=;charset=utf8mb4";
$pdo = new PDO($dsn);
$statement = $pdo->prepare("select * from notes");
$statement->execute();
$posts = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach($posts as $post){
    echo "<li>" . $post['body'] . "</li>";
}
```
I refactored above so it is more friendly to use and converted above into a custom class called Database.

```php
class Database{
	public function query($query){
	$dsn = "mysql:host=localhost;port=3306;dbname=myapp;user=root;password=;charset=utf8mb4";
	$pdo = new PDO($dsn);
    //  new keyword means an object will be returned from PDO class.
	$statement = $pdo->prepare($query);
	$statement->execute();
	return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}
```
I want to add this question in my notes
What is query method in Database class doing?
Answer is: It is used execute sql queries and return a PDOStatement object.


- it would be nice if I build up pdo instance one time because in real life you need to execute hundreds of queries. so I use one of the php magic methods called construct, it automatically runs when new instance is created. that is the perfect place to initialize the instance.

I rewrote Database class

```php
class Database{
	public function __construct(){

		$dsn = "mysql:host=localhost;port=3306;dbname=myapp;user=root;password=;charset=utf8mb4";
		$pdo = new PDO($dsn);
	}
	public function query($query){

	$statement = $pdo->prepare($query);
	$statement->execute();
	return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
    }
```

# Now I am facing issue in query method we are expected to have access to that pdo variable but it is moved to different scope now, we moved it out of query method into __construct function.

Solution is : right above construct lets define an instance property $connection and cool thing about that is anywhere in this class, I can access this property.



that brings me to learn about $this so I learnt that $this is a special variable used within class methods to refer to the current instance of the class. It acts as a pointer to the object on which the method is being called, allowing you to access the instance's properties and methods.
must add this example in notes.
Example

```php
class Person {
    public $name;

    function __construct( $name ) {
        $this->name = $name;
    }
};

$jack = new Person('Jack');
echo $jack->name;
```
must include this question and answer in notes
Question: why can't you just call $name from inside the class and need to use $this?

Answer: $this->name is saying grab the name property of this class. just $name is saying use a variable called $name. So that's why our __construct passes $name as a variable and sets $this->name = $name.
We're saying set the value of our name inside of this object equal to the value we passed in as $name

Since I don't have access to $pdo, I will not store pdo connection in $pdo variable, instead save pdo connection in a variable which you have access anywhere in the class, which is $connection

I need to refactor this piece of code return $statement->fetchAll(PDO::FETCH_ASSOC);
Here's Why??

What If I were trying to fetch a single post,
fetchAll gives all results and every result will be in its own array, so if you want the first result and 'title' property, that's how you would do it.

dd([$post[0]['title']);

If I change it to return $statement->fetch(PDO::FETCH_ASSOC); (changed fetchAll to fetch).

I will get only one result and will have single level of array, and I can access it like this.

dd([$post['title']);

Whether we call fetch or fetchAll, it needs to be dynamic, I should be incharge of what I want to call. We just return $statement instead of $statement->fetchAll(PDO::FETCH_ASSOC)

A lot of programming consists of taking code you have that already works but you don't stop there, you keep iterating on that same code over and over until it doesn't just work but it is also beautiful and flexible to work with.
With that in mind, we look at the Database class , are there any ways to make it more flexible?
I think answer is YES, Have a look at this $dsn here
$dsn = mysql:host=localhost;port=3306;dbname=myapp;user=root;password=;
We have hardcoded many of the options here like host, port name and they will certainly change depending upon environment, like production environment, these are sort of things we need to think about, We are focuing on this code

__construct method of PDO class accepts $dsn, but we can also pass $username, $password and $options.

we can now Remove PDO::FETCH_ASSOC and declare as an $option, when we instantiate PDO class.

$this->connection = new PDO($dsn, 'root','',[
	PDO::ATTR_DEFAULT_FETCH_MODE =>PDO::FETCH_ASSOC
])

//Now I can remove root and password from $dsn

$dsn = mysql:host=localhost;port=3306;dbname=myapp;charset=utf8mb4";

that leaves the host, port and dbname, that also needs to be dynamic. We can put that in a $config array.
$config = [
	'host'=>'localhost',
	'port'=> 3306,
	'dbname'=>'myapp',
	'charset'=>'utf8mb4'
];

To clean up little bit more I am going to use http_build_query($data);
 //Returns or generates a URL-encoded query string from associative array. so my dsn looks like this

 $dsn = 'mysql:' . http_build_query($config, '', ';');

 In a new file config.php, paste it in but instead of declaring variable $config, we will put keyword return. return is not exclusive for function calls, it can also be used in a regular file.
 // Database.php
class Database
{
    public $connection;
    public function __construct($config)
    {
    $dsn = "mysql:" . http_build_query($config, '', ';');
    $this->connection = new PDO($dsn, 'root', '', [PDO::ATTR_DEFAULT_FETCH_MODE =>PDO::FETCH_ASSOC]);
    }

    public function query($query)
    {
        $statement = $this->connection->prepare($query);
        $statement->execute();
        return $statement;
    }
}
// config.php
 return [
    'host' => 'localhost',
    'port' => 3306,
    'dbname' => 'myapp',
    'charset' => 'utf8mb4'
 ];
 // index.php
 require "functions.php";
require "Database.php";
$config = require "config.php";
// require "router.php";

$db = new Database($config);
$posts = $db->query("select * from posts")->fetchAll();

foreach($posts as $post){
echo "<li>" . $post['title'] . "</li>";
}

set default values if you want , because it is so common
public function __construct($config, $username='root', $password='',[PDO::ATTR_DEFAULT_FETCH_MODE =>PDO::FETCH_ASSOC])


Now everything is dynamic other than 'mysql:' in $dsn.   $dsn = 'mysql:' . http_build_query($config, '',';');


If I ever wanna change port or any other information, just go to config file and edit those.

Remember this configuration file is not exclusive to database credentials, You can use for the entire application. With that in mind, may be we should 'key' this, like this

if we add key 'database' then we will use like this
  $db = new Database($config['database']);


  From the above discussion and code refactoring, I learned several important concepts and practices in PHP development

  encapsulated the database connection logic within a class (Database), abstracting away the details of connecting to the database. This promotes cleaner and more maintainable code.

  utilized the constructor (__construct) method to initialize the database connection when an instance of the Database class is created. This ensures that the connection is established automatically whenever the class is instantiated.

  passed the database configuration as an argument to the constructor, allowing for greater flexibility and reusability.

  ---
push the code in __construct method to initialize the connection.
Don't store PDO connection in variable, instead save in public property and use $this keyword.
  ---