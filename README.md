# Repos and Models Generator for Domain-Driven Design

This package provides a convenient way to generate and manage repositories following DDD principles with a clean domain separation.

## Directory Structure

The package organizes code into domains, each with its own repositories, contracts, and models:

```
app
└── Domain
    └── YourDomain (e.g., User, Subscription, etc.)
        ├── Models
        │   └── YourModel.php
        ├── Contracts
        │   └── Repositories
        │       ├── Read
        │       │   └── YourModel.php
        │       └── Write
        │           └── YourModel.php
        └── Repositories
            └── MySql (or other drivers)
                ├── Read
                │   └── YourModel.php
                └── Write
                    └── YourModel.php
```

## Creating New Repositories

Generate all necessary files for a new model in a specific domain:

```bash
php artisan make:repos YourDomain YourModel
```

For example:

```bash
php artisan make:repos Subscription Plan
```

This will create:
- Model class
- Read and Write repository contracts
- MySql implementations for Read and Write repositories

## Registering Repositories

After creating repositories, register them in the service provider:

```bash
php artisan repo:register
```

This command:
1. Refreshes the class autoloader
2. Generates a repository service provider that binds all contracts to their implementations
3. Places the provider in `app/Domain/Providers/RepositoryServiceProvider.php`
4. Registers the provider in your application

## Using Repositories

Inject repository contracts into your services:

```php
namespace App\Domain\User\Services;

use App\Domain\User\Contracts\Repositories\Read\User as UserReadRepo;
use App\Domain\User\Contracts\Repositories\Write\User as UserWriteRepo;

class UserService
{
    public function __construct(
        private UserReadRepo $userReadRepo,
        private UserWriteRepo $userWriteRepo
    ) {}
    
    public function findUserById(string $id)
    {
        return $this->userReadRepo->findById($id);
    }
    
    public function createUser(array $data)
    {
        return $this->userWriteRepo->create($data);
    }
}
```

## Why Use Domain-Driven Repositories?

1. **Domain Separation**: Each domain has its own models and repositories, following DDD principles
2. **Implementation Flexibility**: Easily swap between different database drivers (MySQL, DynamoDB, etc.)
3. **Testability**: Contracts make it easy to mock repositories for testing
4. **Maintainability**: Clear separation of read and write operations

## Guidelines

1. **Avoid Eloquent Relationships**: Instead, use repositories to fetch related data to maintain database independence
2. **Repository Method Ownership**: All database logic should live inside repository methods
3. **Return Complete Data**: Repository methods should return complete data, not queryable objects
4. **Domain Boundaries**: Each domain should be self-contained with its own models and repositories

## Example Usage Pattern

Instead of using Eloquent relationships:

```php
// DON'T DO THIS:
$plan = Plan::with('subscriptions.user')->find($id);
```

Use repositories from each domain:

```php
// DO THIS:
$plan = app(PlanReadRepo::class)->findById($id);
$subscriptions = app(SubscriptionReadRepo::class)->findByPlanId($id);
$userIds = $subscriptions->pluck('user_id')->unique()->toArray();
$users = app(UserReadRepo::class)->findByManyIds($userIds);

// Associate the data in-memory
$subscriptionsWithUsers = $subscriptions->map(function ($subscription) use ($users) {
    $subscription->user = $users->firstWhere('id', $subscription->user_id);
    return $subscription;
});
```

## Available Commands

- `php artisan make:repos {domain} {name}` - Create a model and repositories in a domain
- `php artisan make:model {domain} {name}` - Create just a model in a domain
- `php artisan make:readrepocontract {domain} {name}` - Create just a read repository contract
- `php artisan make:writerepocontract {domain} {name}` - Create just a write repository contract
- `php artisan make:readrepos {domain} {name}` - Create just read repository implementations
- `php artisan make:writerepos {domain} {name}` - Create just write repository implementations
- `php artisan repo:provider` - Generate the repository service provider
- `php artisan repo:register` - Refresh autoloader and register repositories