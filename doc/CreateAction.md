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
 * `listRole`: contains the name of the delete role
 * `createRole`: contains the name of the delete role
 * `editRole`: contains the name of the delete role
 * `viewRole`: contains the name of the delete role
 * `deleteRole`: contains the name of the delete role
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
 * @param  Request $request
 * @return object
 */
protected function crudCreateFactory(Request $request)
```

#### Create form

This method creates a form.

```{.php}
/**
 * @param  object        $object
 * @param  Request       $request
 * @return FormInterface
 */
protected function crudCreateForm($object, Request $request)
```

#### Create success flash message

This method add a success flash message to the response

```{.php}
/**
 * @param  object        $object
 * @param  FormInterface $form
 * @param  Request       $request
 * @return void
 */
protected function crudCreateSuccessFlashMesssage($object, FormInterface $form, Request $request)
```

#### Create error flash message

This method add a error flash message to the response

```{.php}
/**
 * @param  object        $object
 * @param  FormInterface $form
 * @param  Request       $request
 * @return void
 */
protected function crudCreateErrorFlashMesssage($object, FormInterface $form, Request $request)
```

#### Create success response

This method generates a success response.

```{.php}
/**
 * @param  object                         $object
 * @param  FormInterface                  $form
 * @param  Request                        $request
 * @return RedirectResponse|Response
 */
protected function crudCreateSuccessResponse($object, FormInterface $form, Request $request)
```

#### Create error response

This method generates a error response.

```{.php}
/**
 * @param  object                         $object
 * @param  FormInterface                  $form
 * @param  Request                        $request
 * @return RedirectResponse|Response|null
 */
protected function crudCreateErrorResponse($object, FormInterface $form, Request $request)
```

#### Create render response

This method return a rendered template as response.

```{.php}
/**
 * @param  array    $baseTemplateVars
 * @param  array    $templateVars
 * @return Response
 */
protected function crudCreateRenderTemplateResponse(array $baseTemplateVars, array $templateVars)
```

#### Create Template

This method defines the template path.

```{.php}
/**
 * @return string
 */
protected function crudCreateTemplate()
```

## Hooks

#### Create pre persist

This method allows to manipulate the object before persist

```{.php}
/**
 * @param  object        $object
 * @param  FormInterface $form
 * @param  Request       $request
 * @return void
 */
protected function crudCreatePrePersist($object, FormInterface $form, Request $request)
```

#### Create post flush

This method allows to manipulate the object after flush

```{.php}
/**
 * @param  object        $object
 * @param  FormInterface $form
 * @param  Request       $request
 * @return void
 */
protected function crudCreatePostFlush($object, FormInterface $form, Request $request)
```