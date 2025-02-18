<img src="articulate.png" width="100%">

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
