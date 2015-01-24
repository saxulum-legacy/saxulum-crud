# saxulum-crud

[![Build Status](https://api.travis-ci.org/saxulum/saxulum-crud.png?branch=master)](https://travis-ci.org/saxulum/saxulum-crud)
[![Total Downloads](https://poser.pugx.org/saxulum/saxulum-crud/downloads.png)](https://packagist.org/packages/saxulum/saxulum-crud)
[![Latest Stable Version](https://poser.pugx.org/saxulum/saxulum-crud/v/stable.png)](https://packagist.org/packages/saxulum/saxulum-crud)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/saxulum/saxulum-crud/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/saxulum/saxulum-crud/?branch=master)

## Features

 * list action
 * create action
 * edit action
 * view action
 * delete action

## Requirements

 * php: >=5.4,
 * doctrine/common: ~2.4,
 * knplabs/knp-components: ~1.3,>=1.3.1,
 * symfony/form: ~2.3,
 * symfony/http-kernel: ~2.3,
 * symfony/routing: ~2.3,
 * symfony/security: ~2.3
 * twig/twig: ~1.2

## Installation

Through [Composer](http://getcomposer.org) as [saxulum/saxulum-crud][1].

## Usage

Use the trait `Saxulum\Crud\Controller\CrudTrait` and implement the following methods:

### Base configuration

 * `crudName`: contains the lowercase name of the object, example: `sample`
 * `crudObjectClass`: contains the class name of the object, example: `Saxulum\Crud\Entity\Sample`

### Services

 * `getDoctrine`: contains an instance of the doctrine manager registry
 * `getPaginator`: contains an instance of a knp paginator
 * `getFormFactory`: contais an instance of a symfony form factory
 * `getUrlGenerator`: contains an instance of symfony routing url generator
 * `getSecurity`: contains an instance of a symfony security context
 * `getTwig`: contains an instance of the twig environment


[1]: https://packagist.org/packages/saxulum/saxulum-crud