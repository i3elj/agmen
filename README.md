# Agmen
<p align="center">
  <img src="./assets/logo.png" alt="A logo of a battalion of soldiers with the name Agmen at the bottom" width="420"/>
</p>

<p align="center">A simple opinionated web library to be used alongside nginx or apache.</p>

## Installation
```bash
composer require i3elj/agmen
```

## How to use it
Look into the examples folder, is not hard to understand the code. Basically you configure the default folders for views, globals, partials, etc... using `define()`, require the autoload and use the Router class to add endpoints. Routes can have names, it uses classes and you can define the name of the method to be used for the endpoint, otherwise the Router will look into methods named after request methods (e.g. `static::get`, or `static::post`...). Use `snip()` for partials and `globals()` for... well, globals. There are some helper functions (just a few, look into the code).

The Database class needs a .env file with the following:

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

# Contributing
I don't plan on supporting this more than I need. If you have something to add up, and it makes sense, I'll be glad to add your contribution.
