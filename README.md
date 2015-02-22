# saxulum-crud

[![Build Status](https://api.travis-ci.org/saxulum/saxulum-crud.png?branch=master)](https://travis-ci.org/saxulum/saxulum-crud)
[![Total Downloads](https://poser.pugx.org/saxulum/saxulum-crud/downloads.png)](https://packagist.org/packages/saxulum/saxulum-crud)
[![Latest Stable Version](https://poser.pugx.org/saxulum/saxulum-crud/v/stable.png)](https://packagist.org/packages/saxulum/saxulum-crud)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/saxulum/saxulum-crud/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/saxulum/saxulum-crud/?branch=master)

## Features

 * [list action][2]
 * [create action][3]
 * [edit action][4]
 * [view action][5]
 * [delete action][6]

## Requirements

 * php: >=5.4,
 * doctrine/common: ~2.4,
 * symfony/form: ~2.3,
 * symfony/http-kernel: ~2.3,
 * symfony/routing: ~2.3,
 * symfony/security: ~2.3
 * twig/twig: ~1.2

## Installation

Through [Composer](http://getcomposer.org) as [saxulum/saxulum-crud][1].

## Usage

### Trait

Use the following trait within your controller: [Saxulum\Crud\Controller\CrudTrait][7].

### Implement

#### Base configuration

 * `crudName`: contains the lowercase name of the object, example: `sample`
 * `crudObjectClass`: contains the class name of the object, example: `Saxulum\Crud\Entity\Sample`

#### Advanced configuration

 * `crudRoutePattern`: contains a template pattern like: `%s_%s`
 * `crudRolePattern`: contains a template pattern like: `role_%s_%s`
 * `crudTemplatePattern`: contains a template pattern like: `@SaxulumCrud/%s/%s.html.twig`

#### Services

 * `crudDoctrine`: contains an instance of the doctrine manager registry
 * `crudPaginator`: contains an instance of a knp paginator
 * `crudFormFactory`: contais an instance of a symfony form factory
 * `crudUrlGenerator`: contains an instance of symfony routing url generator
 * `crudSecurity`: contains an instance of a symfony security context
 * `crudTwig`: contains an instance of the twig environment

### Twig: form label generation

Use the following extension to use label generation: [Saxulum\Crud\Twig\FormLabelExtension][8].

Within the form template you can use something like this:

```{.twig}
{% block form_label %}
    {% spaceless %}
        {% if label is empty %}
            {% set label = prepareFormLabel(form) %}
        {% endif %}
        {{ parent() }}
    {% endspaceless %}
{% endblock form_label %}


[1]: https://packagist.org/packages/saxulum/saxulum-crud
[2]: doc/ListAction.md
[3]: doc/CreateAction.md
[4]: doc/EditAction.md
[5]: doc/ViewAction.md
[6]: doc/DeleteAction.md
[7]: src/Controller/CrudTrait.php
[8]: src/Twig/FormLabelExtension.php