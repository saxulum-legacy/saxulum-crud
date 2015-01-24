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

#### Delete is granted

This methods return if its allowed to call this object delete action.

```{.php}
/**
 * @return bool
 */
protected function crudDeleteIsGranted()
```

#### Delete role

This method defines the delete role (for security check).

```{.php}
/**
 * @return string
 */
protected function crudDeleteRole()
```

#### Delete Redirect url

This method defines the redirect url after delete object

```{.php}
/**
 * @param object
 * @return string
 */
protected function crudDeleteRedirectUrl($object)
```

## Hooks

#### Delete pre persist

This method allows to manipulate the object before remove

```{.php}
/**
 * @param  object $object
 * @return void
 */
protected function crudDeletePreRemove($object)
```

#### Delete post flush

This method allows to manipulate the object after flush

```{.php}
/**
 * @param  object $object
 * @return void
 */
protected function crudDeletePostFlush($object)
```