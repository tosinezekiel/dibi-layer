# CLH Repos and Models Generator

## Creating new repositories
Below generates all the necessary files needed to implement the pattern. 
```php
php artisan make:repos User
```
The command will create an implementation for each driver listed in `config('repomodel.order')`. See below example assuming MySql and DynamoDb

```text
App  
-─── Contracts
    -─── Repositories
        -─── Read
            │   User.php // Read Contract   
           
        ─── Write
            │   User.php // Write Contract

-─── Models
    │ User.php // The eloquent model 

-─── Repositories
    -─── Read
        -───DynamoDb
            │   User.php   
        -───MySql
            │   User.php
       
    
    ─── Write
        -───DynamoDb
            │   User.php   
        -───MySql
            │   User.php
```

## Using repositories
Using dependency injection is one way. Global helper functions `readRepo()` and `writeRepo()` are also available to help instantiate repos.
Examples:
```php
// instantiate a read repo with helper methods
$repo = readRepo(App\Contracts\Repositories\Read\User::class);
$repo->find(1);

// inject a read repo in the consuming class
class LoginService {
    public function __construct(private App\Contracts\Repositories\Read\User $repo) {}
    
    public function loginUsingId(int $id) {
        $user = $this->repo->findOrFail($id);
        
        Auth::login($user);
    }
}
```

## Generating the repository provider
Once we've created repositories, we need to bind the contracts to implementations in a service provider. The command below will register all contracts and their bindings. At the time this is written we're only concerned with the MySql implementation, so it'll only register MySql repos. This will change soon.

```php
php artisan repo:provider
```

## Making mass changes
Assuming we want to recreate all repos, or all ReadContracts because a stub has changed, then we'd run one of the commands below. After the recreations, developers should carefully review git changes before committing as they may need to restore some of the changes overwritten by the command.

```php
// recreate all "types"
php artisan force-recreate:repos

// recreate only one "type" 
php artisan force-recreate:repos --create=ReadRepoContract
php artisan force-recreate:repos --create=WriteRepoContract
php artisan force-recreate:repos --create=ReadRepos
php artisan force-recreate:repos --create=WriteRepos
php artisan force-recreate:repos --create=Model
```

## Repository pattern at CLH
At CLH the repository pattern is a class where we put any of the code that talks to the DB, organized per resource. In the context of Laravel a resource will most likely be a DB table. There are 2 types of repositories:

1. Read: Contains logic that fetches data from the DB.
2. Write: Contains logic that writes data to the DB.

### Examples:

```
namespace App\Repositories\Read;

use App\Models\Patient as PatientModel;

class Patient {
    public function findById(int $id): PatientModel {
        return PatientModel::find($id);
    }
    
    public function nurseId(int $patientId): int {
        return PatientModel::where('id', $patientId)->value('nurse_id');
    }
}
```


```
namespace App\Repositories\Write;

class Call {
    public function scheduleForPatient(int $nurseId, int $patientId) : \stdClass {
        // create and return call
    }
}
```

## Why are we doing this?
We want the maximum flexibility when it comes to which data stores we are using. As we scale, we may need to migrate some of our data to a NoSql DB such as DynamoDB. We may even migrate all of our data to NoSql. To keep all possibilities open and easily accessible, we will be putting all our DB logic behind interfaces, classes, and methods. This way, whenever we want to migrate a resource to a different DB, all we have to do is create a new implementation of our repository.

## Guidelines
1. We will not use relationships in any form. Relationships in eloquent are tightly coupled to Sql DBs so that will limit our flexibility when it comes to migrating resources to NoSql. In addition, using the `with` method to load relationships would mean that we bypass other repositories.
2. All DB logic should live inside a method in a repository. Whatever that method returns is final, and should not be queried further in the class that consumes the repository. For example, we should not do this: `app(UserReadRepo::class)->findBy($id)->where('name', 'John Doe')->count()`
3. Some packages such as "Livewire Datatables" will require EloquentBuilders. In these cases we should create a method in the ReadRepository to return a Builder object **only for that particular use case**. In other words, we should not perform further queries on these Builders in consuming classes.
4. There should be no nesting similar to what the `with` method does. Repositories should return data from one resource only. Consider the example where we want to retrieve a patient's care team with the care team member's user, their specialty and phone number. Using the standard eloquent approach our code would look something like below: 

```php
// Standard Eloquent approach. We should NOT do this!

$patient = Patient::with('careTeam.memberUser.phoneNumbers', 'careTeam.memberUser.providerInfo')->first();

foreach ($patient->careTeam as $carePerson) {
    echo $carePerson->name();
    echo $carePerson->providerInfo->specialty;
    echo optional($carePerson->phoneNumbers->first())->number;
}
```
Instead, we should call each repository individually to fetch collections of data while avoiding the N+1 problem.
```php
// Repository approach

$careTeamUserIds = readRepo(CareTeamRepo::class)->forPatient($id)->pluck('member_user_id')->all();         
$careTeamUsers = readRepo(UserRepo::class)->findMany($careTeamUserIds);
$careTeamUsersProviderInfo = readRepo(ProviderInfo::class)->findMany($careTeamUserIds);
$providerPhones = readRepo(PhoneRepo::class)->forUser($careTeamUserIds);

foreach ($careTeamUsers as $carePerson) {
    echo $carePerson->name();
    echo $careTeamUsersProviderInfo->where('user_id', $u->id)->specialty;
    echo optional($providerPhones->where('user_id', $u->id)->first())->number;
}
```
The methodology is similar to having a front end app (eg. Vue or React app) and hitting multiple APIs. In this case the front end app is the Livewire component, and the APIs are the Repositories.

