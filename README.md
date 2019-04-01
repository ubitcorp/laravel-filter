[![Latest Stable Version](https://poser.pugx.org/ubitcorp/laravel-filter/v/stable)](https://packagist.org/packages/ubitcorp/laravel-filter)
[![Total Downloads](https://poser.pugx.org/ubitcorp/laravel-filter/downloads)](https://packagist.org/packages/ubitcorp/laravel-filter)
[![License](https://poser.pugx.org/ubitcorp/laravel-filter/license)](https://packagist.org/packages/ubitcorp/laravel-filter)
[![Monthly Downloads](https://poser.pugx.org/ubitcorp/laravel-filter/d/monthly)](https://packagist.org/packages/ubitcorp/laravel-filter)
[![Daily Downloads](https://poser.pugx.org/ubitcorp/laravel-filter/d/daily)](https://packagist.org/packages/ubitcorp/laravel-filter)


# Laravel Filter
It provides a Eloquent Filter from request or an array. It can be used with trait on any model for Laravel project

# Installation
Via Composer

``` bash
composer require ubitcorp/laravel-filter
``` 

To change configuration file:

``` bash
php artisan vendor:publish --provider="ubitcorp\Filter\ServiceProvider" 
``` 
 

# Usage
This package provides the **filter** trait that can be included any model. After that the functions below can be used:


```` php
//In Project Model (example)
namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use ubitcorp\Filter\Traits\Filter; 

class Project extends Model
{
  use Filter;
  ...
}
````

```` php
//In a Controller
Project::with("customer")->filter()->paginate();
````



[ico-version]: https://img.shields.io/packagist/v/ubitcorp/filter.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ubitcorp/filter.svg?style=flat-square


[link-packagist]: https://packagist.org/packages/ubitcorp/laravel-filter
[link-downloads]: https://packagist.org/packages/ubitcorp/laravel-filter
[link-author]: https://github.com/ubitcorp 