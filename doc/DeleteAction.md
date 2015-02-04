# Delete action

This methods implements a simple, but usefull object delete action.

## Api

```{.php}
/**
 * @param  Request                   $request
 * @param  int                       $id
 * @return Response|RedirectResponse
 */
public function crudDeleteObject(Request $request, $id)
```

## Overwrites

### Facultative

#### Delete route

This method defines the delete route name

```{.php}
/**
 * @return string
 */
protected function crudDeleteRoute()
```

#### Delete load object

This method loads the object if not allready given.

```{.php}
/**
 * @param  object  $object
 * @param  Request $request
 * @return object
 */
protected function crudDeleteLoadObject($object, Request $request)
```

#### Delete is granted

This methods return if its allowed to call this object delete action.

```{.php}
/**
 * @param  object  $object
 * @param  Request $request
 * @return bool
 */
protected function crudDeleteIsGranted($object, Request $request)
```

#### Delete role

This method defines the delete role (for security check).

```{.php}
/**
 * @return string
 */
protected function crudDeleteRole()
```

#### Delete success flash message

This method add a success flash message to the response

```{.php}
/**
 * @param  object        $object
 * @param  FormInterface $form
 * @param  Request       $request
 * @return void
 */
protected function crudDeleteSuccessFlashMesssage($object, FormInterface $form, Request $request)
```

#### Delete success response

This method generates a success response.

```{.php}
/**
 * @param  object                         $object
 * @param  FormInterface                  $form
 * @param  Request                        $request
 * @return RedirectResponse|Response
 */
protected function crudDeleteSuccessResponse($object, FormInterface $form, Request $request)
```

## Hooks

#### Delete pre persist

This method allows to manipulate the object before remove

```{.php}
/**
 * @param  object  $object
 * @param  Request $request
 * @return void
 */
protected function crudDeletePreRemove($object, Request $request)
```

#### Delete post flush

This method allows to manipulate the object after flush

```{.php}
/**
 * @param  object  $object
 * @param  Request $request
 * @return void
 */
protected function crudDeletePostFlush($object, Request $request)
```