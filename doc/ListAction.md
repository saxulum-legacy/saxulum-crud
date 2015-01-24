# List action

This methods implements a simple, but usefull object list action with pagination.

*IMPORTANT*: An object using object list action needs a own `Repository` which implements
the interface `Saxulum\Crud\Repository\QueryBuilderForFilterFormInterface`.

## Api

```{.php}
/**
 * @param Request $request
 * @param array   $templateVars
 * @return Response
 * @throws \Exception
 */
public function crudListObjects(Request $request, array $templateVars = array())
```

## Template Vars

 * `request`: contains the Request object
 * `pagination`: contains the objects within a pagination object
 * `form`: contains a form view or null
 * `listRoute`: contains the name of the list route
 * `createRoute`: contains the name of the create route
 * `editRoute`: contains the name of the edit route
 * `viewRoute`: contains the name of the view route
 * `deleteRoute`: contains the name of the delete route
 * `identifier`: contains property name of the id of the object (`id` in most cases)
 * `transPrefix`: contains the translation prefix (`Controller::crudName()`)

## Overwrites

### Facultative

#### List per page

This method defines the amount of objects per page

*IMPORTANT*: Get overwritten by `$request->query->get('perPage')`

```{.php}
/**
 * @return int
 */
protected function crudListPerPage()
```

#### List route

This method defines the list route name

```{.php}
/**
 * @return string
 */
protected function crudListRoute()
```

#### List is granted

This methods return if its allowed to call this object list action.

```{.php}
/**
 * @return bool
 */
protected function crudListIsGranted()
```

#### List role

This method defines the list role (for security check).

```{.php}
/**
 * @return string
 */
protected function crudListRole()
```

#### List form type

This method defines a form type used for filtering objects (default: no filter).

*IMPORTANT* Works only if you use those filter values within your repository.

```{.php}
/**
 * @return FormTypeInterface|null
 */
protected function crudListFormType()
```

#### List form data enrich

This method enrich and overrides (by key) the submitted form data.

```{.php}
/**
 * @return array
 */
protected function crudListFormDataEnrich()
```

#### List Template

This method defines the template path.

```{.php}
/**
 * @return string
 */
protected function crudListTemplate()
```