# HiFramework

This is an education yet completely useful PHP web application framework

## Features

- **Attribute-Based Routing** - Clean, declarative routing using PHP 8 attributes
- **Dependency Injection** - Automatic constructor injection with PSR-11 container
- **Access Control** - Secure by default with role-based access control
- **Doctrine ORM** - Full ORM support with entity management
- **Twig Templates** - Powerful templating with custom extensions
- **PSR Standards** - PSR-7 (HTTP), PSR-3 (Logging), PSR-11 (Container)
- **Authentication** - Built-in user authentication with session management

## Requirements

- PHP 8.4 or higher
  - with php8.4-intl extension
  - with php8.4-sqlite3 extension (should you choose to use the default db setup)
- Composer
- SQLite, MySQL, or PostgreSQL (for Doctrine ORM)

## Getting started

```bash
composer create-project holkerveen/hi-framework-skeleton my-new-website
cd my-new-website
composer hi orm:schema-tool:create
composer hi user:create
composer serve
```

This creates a new project, sets up the database, creates your first user, and starts the development server.

You'll be prompted to enter an email and password for the first user.

Visit [http://localhost:8000](http://localhost:8000) to access your new application

## Database

HiFramework uses Doctrine ORM for database management. By default, it uses SQLite for easy setup, but you can configure it to use MySQL or PostgreSQL.

### Creating the Database Schema

```bash
composer run doctrine orm:schema-tool:create
```

### Adding Your First User

Since user management requires authentication, you'll need to create your first user via the command line:

```bash
composer run hi:user:create
```

You'll be prompted to enter an email and password for the new user.

### Other Useful Database Commands

```bash
# Update schema after entity changes
composer run doctrine orm:schema-tool:update --force

# Drop the schema
composer run doctrine orm:schema-tool:drop --force

# Validate schema
composer run doctrine orm:validate-schema
```

## Quick Example

Once installation is finished you can start creating your application. Here are some basic examples to help you get
started.

### Controller

```php
<?php
namespace App\Controllers;

use Hi\Attributes\Route;
use Hi\Attributes\AllowAccess;
use Hi\Enums\Role;
use Twig\Environment;

class HomeController
{
    #[Route('/')]
    #[AllowAccess(Role::Unauthenticated)]
    public function index(Environment $twig): string
    {
        return $twig->render('home.html.twig');
    }

    #[Route('/admin')]
    #[AllowAccess(Role::Authenticated)]
    public function admin(Environment $twig): string
    {
        return $twig->render('admin.html.twig');
    }
}
```

### Template

```twig
<!DOCTYPE html>
<html>
<head>
    <title>My Website</title>
</head>
<body>
    <h1>Welcome</h1>

    {% if allowed('/admin') %}
        <a href="/admin">Admin Panel</a>
    {% endif %}
</body>
</html>
```

## Credits

Built with:
- [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html)
- [Twig](https://twig.symfony.com/)
- [PSR Standards](https://www.php-fig.org/psr/)
