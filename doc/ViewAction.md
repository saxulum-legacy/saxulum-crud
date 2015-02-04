# View action

This methods implements a simple, but usefull object view action.

## Api

```{.php}
/**
 * @param  Request                   $request
 * @param  int                       $id
 * @param  array                     $templateVars
 * @return Response|RedirectResponse
 */
public function crudViewObject(Request $request, $id, array $templateVars = array())
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

### Facultative

#### View route

This method defines the view route name

```{.php}
/**
 * @return string
 */
protected function crudViewRoute()
```

#### View is granted

This methods return if its allowed to call this object view action.

```{.php}
/**
 * @param object
 * @return bool
 */
protected function crudViewIsGranted($object)
```

#### View role

This method defines the view role (for security check).

```{.php}
/**
 * @return string
 */
protected function crudViewRole()
```

#### View Template

This method defines the template path.

```{.php}
/**
 * @return string
 */
protected function crudViewTemplate()
```