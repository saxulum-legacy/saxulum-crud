# Edit action

This methods implements a simple, but usefull object edit action.

## Api

```{.php}
/**
 * @param  Request                   $request
 * @param  int                       $id
 * @param  array                     $templateVars
 * @return Response|RedirectResponse
 */
public function crudEditObject(Request $request, $id, array $templateVars = array())
```

## Template Vars

 * `request`: contains the Request object
 * `object`: contains the object
 * `form`: contains a form view
 * `listRoute`: contains the name of the list route
 * `createRoute`: contains the name of the create route
 * `editRoute`: contains the name of the edit route
 * `viewRoute`: contains the name of the view route
 * `deleteRoute`: contains the name of the delete route
 * `identifier`: contains property name of the id of the object (`id` in most cases)
 * `transPrefix`: contains the translation prefix (`Controller::crudName()`)

## Overwrites

### Mandatory

#### Edit form type

This method defines a form type.

```{.php}
/**
 * @return FormTypeInterface
 */
protected function crudEditFormType()
```

### Facultative

#### Edit route

This method defines the edit route name

```{.php}
/**
 * @return string
 */
protected function crudEditRoute()
```

#### Edit is granted

This methods return if its allowed to call this object edit action.

```{.php}
/**
 * @return bool
 */
protected function crudEditIsGranted()
```

#### Edit role

This method defines the edit role (for security check).

```{.php}
/**
 * @return string
 */
protected function crudEditRole()
```

#### Redirect url

This method defines the redirect url after edit object

```{.php}
/**
 * @param object
 * @return string
 */
protected function crudEditRedirectUrl($object)
```

#### Template

This method defines the template path.

```{.php}
/**
 * @return string
 */
protected function crudEditTemplate()
```