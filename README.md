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
 * knplabs/knp-components: ~1.3,>=1.3.1,
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

#### Base configuration

 * `crudName`: contains the lowercase name of the object, example: `sample`
 * `crudObjectClass`: contains the class name of the object, example: `Saxulum\Crud\Entity\Sample`

#### Advanced configuration

 * `crudRoutePattern`: contains a template pattern like: `%s_%s`
 * `crudRolePattern`: contains a template pattern like: `role_%s_%s`
 * `crudTemplatePattern`: contains a template pattern like: `@SaxulumCrud/%s/%s.html.twig`

#### Services

 * `crudAuthorizationChecker`: contains an instance of the symfony authorization checker
 * `crudDoctrine`: contains an instance of the doctrine manager registry
 * `crudPaginator`: contains an instance of a knp paginator
 * `crudFormFactory`: contais an instance of a symfony form factory
 * `crudUrlGenerator`: contains an instance of symfony routing url generator
 * ~~`crudSecurity`~~: deprecated by symfony 2.7, use `crudAuthorizationChecker`
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
```

#### Example

The form name is `day_edit`, there is a collection field `comestiblesWithinDay`
with a subfield called `comestible`. As you can see the `_` within the name will
be replaced by a `.`.

```{.yaml}
day:
    edit:
        label:
            comestibles_within_day: Consumption
            comestibles_within_day_collection.comestible: Comestible
```

*Advanced names:* If you want the underscore within the form name,
work with camel case. Which means `someFormName_edit` will be converted to
`some_form_name.edit`.

[1]: https://packagist.org/packages/saxulum/saxulum-crud
[2]: doc/ListAction.md
[3]: doc/CreateAction.md
[4]: doc/EditAction.md
[5]: doc/ViewAction.md
[6]: doc/DeleteAction.md
[7]: src/Controller/CrudTrait.php
[8]: src/Twig/FormLabelExtension.php