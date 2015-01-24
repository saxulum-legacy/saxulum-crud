# Create action

This methods implements a simple, but usefull object create action.

## Api

```{.php}
/**
 * @param  Request                   $request
 * @param  array                     $templateVars
 * @return Response|RedirectResponse
 */
public function crudCreateObject(Request $request, array $templateVars = array())
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

#### Create form type

This method defines a form type.

```{.php}
/**
 * @return FormTypeInterface
 */
protected function crudCreateFormType()
```

### Facultative

#### Create route

This method defines the create route name

```{.php}
/**
 * @return string
 */
protected function crudCreateRoute()
```

#### Create is granted

This methods return if its allowed to call this object create action.

```{.php}
/**
 * @return bool
 */
protected function crudCreateIsGranted()
```

#### Create role

This method defines the create role (for security check).

```{.php}
/**
 * @return string
 */
protected function crudCreateRole()
```

#### Create factory

This method creates a new object.

```{.php}
/**
 * @return object
 */
protected function crudCreateFactory()
```

#### Redirect url

This method defines the redirect url after create object

```{.php}
/**
 * @param object
 * @return string
 */
protected function crudCreateRedirectUrl($object)
```

#### Create Template

This method defines the template path.

```{.php}
/**
 * @return string
 */
protected function crudCreateTemplate()
```