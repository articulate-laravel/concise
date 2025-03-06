<img src="concise.png">

![Packagist Version](https://img.shields.io/packagist/v/articulate/concise)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/articulate/concise)

![GitHub](https://img.shields.io/github/license/articulate-laravel/concise)
![Laravel](https://img.shields.io/badge/laravel-11.x-red.svg)
[![codecov](https://codecov.io/gh/articulate-laravel/concise/branch/main/graph/badge.svg?token=FHJ41NQMTA)](https://codecov.io/gh/articulate-laravel/concise)

![Unit Tests](https://github.com/articulate-laravel/concise/actions/workflows/tests.yml/badge.svg)
![Static Analysis](https://github.com/articulate-laravel/concise/actions/workflows/static-analysis.yml/badge.svg)

# Articulate: Concise
Articulate: Concise is a super lightweight data mapper ORM for Laravel.
It exists as a package entirely because I wondered how lightweight of a solution I can build, that gives me the 
benefit of a data mapper ORM, without using a full ORM.

Concise is extensible and flexible, and will most likely serve as the basis for Articulate in the future, but it comes
with many sensible defaults, and a sprinkle of magic that'll simplify using it based on certain conventions.
It also comes with a driver for Laravel's auth functionality,
and route entity binding (route model binding but for concise entities).

## Getting Started

First things first, create yourself an entity.

```shell
php artisan make:entity Blog
```

By default, it is assumed that the entity...

- Is using an `id` field/column as its identifier
- Is using the default connection
- Has a table name that can be determined from the model name (`blogs` in this example)

To change any of these, you can provide the options `--identity`, `--connection` and `--table` respectively.
This command will create two files...

- `app/Entities/Blog.php`
- `app/Mappers/Entities/BlogMapper.php`

Next you'll want to...

- Open up your new `Blog` class, add the fields as properties, and add some getters and setters, if you want.
- Open up your new `BlogMapper` class and update the `toObject()` and `toData()` methods.

Concise entities can also make use of implicit route "model" binding, so there's no need to worry about manually doing
that.
To get a repository for a given entity, you can use the `EntityRepository` contextual attribute, like so.

```php
use Articulate\Concise\Attributes\EntityRepository;
use Articulate\Concise\Contracts\Repository;

class ExampleController {
    public function __construct(
        #[EntityRepository(Blog::class)] private Repository $blogRepository
    ) {}
}
```
