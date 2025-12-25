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
- Composer
- SQLite, MySQL, or PostgreSQL (for Doctrine ORM)

## Getting started

```bash
composer create-project holkerveen/hi-framework-skeleton my-new-website
cd my-new-website
composer run serve
```

This creates a new project and starts the development server.

Visit [http://localhost:8000](http://localhost:8000) to access your new application

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
