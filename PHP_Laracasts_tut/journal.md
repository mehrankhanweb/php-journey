I wanna clean up this junk code in note.php

```php
$currentUserId = 1;

$note = $db->query("select * from notes where id=:id",[ 'id'=>$_GET['id']])->fetch();


if(!$note){
    abort(Response::NOT_FOUND);
}

if($note['user_id']!=$currentUserId){
    abort(Response::FORBIDDEN);
}
```
Look at this code first

```php
if(!$note){
abort(Response::NOT_FOUND);
}
```


It would be better if we could combine it with fetch() method, wouldn't it be cool if we could say fetchOrAbort() and we don't need this if(!note){}

If I had that method available to me, I no longer had to write this if statement across the entire codebase .

BUT the problem is we don't own that method, it is something PHP provides internally it is a PDO statement class.

```php
$note = $db->query('select * from notes where id = :id', [
'id' => $_GET['id']
])
dd($note);
```
This is what you get, a PDO statement object
> object(PDOStatement)#3 (1) { ["queryString"]=> string(34) "select * from notes where id = :id" }

so this Database class looks like at the moment

```php
class Database{
    public $connection;
	public function __construct($config, $username='root',$password=""){

		$dsn = "mysql:" . http_build_query($config, "", ';');
		$this->connection = new PDO($dsn,$username,$password,[PDO::ATTR_DEFAULT_FETCH_MODE =>PDO::FETCH_ASSOC]);
	}
	public function query($query, $params=[]){

	$statement = $this->connection->prepare($query);
	$statement->execute($params);
	return $statement;
	}
    }
```
Notice query method returns $statement, which is PDOStatement object,instead of that I could just return the object itself ($this). so we could add some helper methods

$statement is PDOStatement Object and you can only use What this object provides and it does provide fetch, but we want to modify it.

so i modified my class Database like so

```php
class Database{
    public funciton query($query, $params=[]){
        // other code here

        return $this;
    }
}
```

Now when I call query, I am not returning that PDO statement, I am returning the same instance of database

So right now everything is gonna blow up ofcourse, because now we are trying to call fetch on Database class and that does not exist.

```php
class Database{
    public $connection;
	public $statement;
	public function __construct($config, $username='root',$password=""){

		$dsn = "mysql:" . http_build_query($config, "", ';');
		$this->connection = new PDO($dsn,$username,$password,[PDO::ATTR_DEFAULT_FETCH_MODE =>PDO::FETCH_ASSOC]);
	}
	public function query($query, $params=[]){

	$this->statement = $this->connection->prepare($query);
	$this->statement->execute($params);
	return $this;
	}
    }
```
In the above code , what I have done is assigned PDOStatement object ($statement) to the class object, I think it is important step.

Next Let's start by adding a fetch method, This is basically going to do what we had earlier, should return something like $statement->fetch

```php
public function fetch($query, $params=[]){
		return $this->statement->fetch($query, $params);
    }
```
at this stage, everything should work as it was before but the key difference is that now I own this method we are not using someone's API, we have wrapped in something that we own, I can name whatever I want


Next thing is add a method to handle this piece of code.

```php
if(!$note){
abort();
 }
```
Lets create another version of fetch method called fetchOrFail(), this will call fetch() method that we just created but then we are gonna do a check here

```php
$result = $this->fetch();
if(!$result){
    abort();
}
return $result;
// // $result is generic name because it could be 'note' like in this case
// or it could be anything e.g 'user'
```
We have to update our notes.php to no longer call fetchAll() method. Let's call it simply get()
```php
public function get(){
  return $this->statement->fetchAll();
}
```
that's a very cool refactor.

Now Let's worry about this

```php
if($note['user_id']!= $currentUserId){
abort(Response::FORBIDDEN);
}
```

This code authorises that currentUser created this note. but the keyword authorises doesn't exist here, maybe it should..

for this piece of code, we can just create a helper function 'authorize' that I will write in functions.php file.

```php
function authorize($condition){
    if(!$condition){
        abort(Response::FORBIDDEN);
    }
}

// make it more reusable and add the second parameter
function authorize($condition, $status = Response::FORBIDDEN){
if(!$condition){
  abort($status);
}
}
// call it like this in note.php
authorize($note['user_id']===$currentUserId);
// not != $currentUserId
```

Now you might be thinking if we are performing authroization isn't 403 always proper status code?
Answer is YES and NO.
There will be situations where even though the user may not be authorized to view particular page or resource, you don't want to necessarily indicate to them, that provides the information to the state of database.
It gives them information "oh this record does infact exist" so maybe I could do something malicious knowing that.
Another example might be If you need to reset your password, you type in your password and it returns we couldn't find that email address
If you think about it, it reveals the information about your database and which you might not want to in certain situations. So in nutshell It could be useful to allow ourselves to override default status code.

We talked about some nitty gritty details and refactoring, may be it was not fun for you as we didn't see anything flashy on the page, but we will do it soon!
