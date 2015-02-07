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

#### View load object

This method loads the object if not allready given.

```{.php}
/**
 * @param  object  $object
 * @param  Request $request
 * @return object
 */
protected function crudViewLoadObject($object, Request $request)
```

#### View role

This method defines the view role (for security check).

```{.php}
/**
 * @return string
 */
protected function crudViewRole()
```

#### View render response

This method return a rendered template as response.

```{.php}
/**
 * @param  Request  $request
 * @param  array    $baseTemplateVars
 * @param  array    $templateVars
 * @return Response
 */
protected function crudViewRenderTemplateResponse(Request $request, array $baseTemplateVars, array $templateVars)
```

#### View Template

This method defines the template path.

```{.php}
/**
 * @return string
 */
protected function crudViewTemplate()
```