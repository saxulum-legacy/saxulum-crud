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
 * `listRole`: contains the name of the delete role
 * `createRole`: contains the name of the delete role
 * `editRole`: contains the name of the delete role
 * `viewRole`: contains the name of the delete role
 * `deleteRole`: contains the name of the delete role
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
 * @param object
 * @return bool
 */
protected function crudEditIsGranted($object)
```

#### Edit role

This method defines the edit role (for security check).

```{.php}
/**
 * @return string
 */
protected function crudEditRole()
```

#### Edit Redirect url

This method defines the redirect url after edit object

```{.php}
/**
 * @param object
 * @return string
 */
protected function crudEditRedirectUrl($object)
```

#### Edit Template

This method defines the template path.

```{.php}
/**
 * @return string
 */
protected function crudEditTemplate()
```

## Hooks

#### Edit pre persist

This method allows to manipulate the object before persist

```{.php}
/**
 * @param  object $object
 * @return void
 */
protected function crudEditPrePersist($object)
```

#### Edit post flush

This method allows to manipulate the object after flush

```{.php}
/**
 * @param  object $object
 * @return void
 */
protected function crudEditPostFlush($object)
```