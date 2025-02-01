# Tusk

A simple opinionated web library to be used alongside nginx or apache.

## Installation
```bash
composer require r-odriguez/tusk
```

## How to use it
### Using tusk-init script
Create your project folder, open it in your terminal and run `tusk-init` on it.

### Setting up from scratch
Create a public folder with your `index.php` there and just require it:

```php
const BASE_PATH = __DIR__ . '/../';
require BASE_PATH . 'vendor/r-odriguez/tusk/init.php';
```

It ~is~ isn't necessary (anymore) to have a `config.php` so tusk knows where to look for things. Usually you would `define` where tusk should look for special global variables, like `COMPONENTS_DIR_NAME`, which tells tusk the name of the folder that has server components for a route. This is every global variable defined by tusk and you can change it by `define()`ing inside a `config.php` in the root folder:

| Variables             | Value                | Description                              |
|-----------------------|----------------------|------------------------------------------|
| `WEB_DIR`             | `"src/www"`          | Directory containing web pages           |
| `COMPONENTS_DIR_NAME` | `"partials"`         | Directory name for reusable components based on the route   |
| `GLOBALS_DIR`         | `"src/globals"`      | Directory for global components          |
| `ICONS_DIR`           | `"public/svg/icons"` | Directory containing icon SVG files      |
| `SVG_DIR`             | `"public/svg"`       | Directory containing general SVG files   |
| `ERROR_PAGES_DIR`     | `"src/www/errors"`   | Directory containing error pages         |


## Local Development
For development I like to use php built-in web server. Create a `Makefile` at the top level with the following:

```makefile
  all: run

  run:
      @echo "starting server..."
      @php -S localhost:8080 -t ./public/ --php-ini=./php.ini

  # add more stuff as you go
```

You'll probably need a .env file with the following too:

```env
# host name for PDO interface: sqlite, pgsql, mysql...
DB=sqlite

# used only for sqlite host name
DB_URL=db.sqlite

# common variables used with server based databases: postgresql, mysql, mariadb etc...
DB_HOST=localhost
DB_PORT=8080
DB_USER=admin
DB_PASSWORD=admin
DB_NAME=dbname
```

## Index.php example
You can use a `Route` class to define the paths.

```php
const BASE_PATH = __DIR__ . '/../';
require BASE_PATH . 'vendor/r-odriguez/tusk/init.php';

import('src/middlewares/'); // the import function can import an entire directory (only php files)

const route = new tusk\Route(URL['path']);
route->redirect(from: '/', to: '/home')
route->path('/home', '/home.php')
     ->path('/user/:age(number)', '/user/') // you can chain them

// use the Middleware abstract class to provide security. Look for the Middleware.php file so you
// know how it looks.
// MyAuthMiddle::class is defined inside ./src/middlewares/.
route->pathM('/user/:name(word)/profile', '/user/profile.php', MyAuthMiddle::class);
```

You can access `:name(word)` variable using `route->param("name")` function inside the controller. There are two options of creating a route: 1. the second parameter of `Route->path()` is a directory inside the `WEB_DIR` where there is a `main.php` and a `view.php` or 2. is a php file. If you choose the first option, use the `tusk\view()` function to return the view inside the `view.php`.

## Explore the code base
If you want to know more about the library, explore the code base, is super simple. This is basically a compilation of useful functions to deal with annoyances of vanilla php.

# Contributing
I don't plan on supporting this more than what I need. If you have something to add up, try your luck, I might not answer it. If I like it I'll accept probably. I accept bug reports tho.

# Fixes
* [ ] Find a way to fix this:
  - `/obj/:name(word)` would translate to `$obj_name` and `/prop/:name(word)` would become `$prop_name`.
  - How the `\tusk\view()` would handle `/obj/:name(word)/section`?
    ```php
    const route = new Route(URL['path']);
    route->path('/obj/:name(word)/section', 'web/obj/section/')
         ->path('/api/obj/:name(word)/props', 'api/obj/props.php')
         ->path('/api/obj/:name(word)/prop/:name(word)', 'api/obj/prop_name.php')
    ```
