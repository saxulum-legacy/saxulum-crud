<?php

namespace Saxulum\Crud\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Saxulum\Crud\Listing\Listing;
use Saxulum\Crud\Listing\ListingFactory;
use Saxulum\Crud\Pagination\PaginatorInterface;
use Saxulum\Crud\Repository\QueryBuilderForFilterFormInterface;
use Saxulum\Crud\Util\Helper;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

trait CrudTrait
{
    /**
     * @param  Request    $request
     * @param  array      $templateVars
     * @return Response
     * @throws \Exception
     */
    public function crudListObjects(Request $request, array $templateVars = array())
    {
        $crudListRole = $this->crudListRole();
        if (!$this->crudSecurity()->isGranted($crudListRole)) {
            throw new AccessDeniedException(sprintf('You need the permission to list entities, role: %s!', $crudListRole));
        }

        $form = $this->crudListForm($request);
        if (null !== $form) {
            $form->handleRequest($request);
            $formData = $form->getData();
        } else {
            $formData = array();
        }

        $formData = $this->crudListFormDataEnrich($request, $formData);

        $repo = $this->crudRepositoryForClass($this->crudObjectClass());
        if (!$repo instanceof QueryBuilderForFilterFormInterface) {
            throw new \Exception(sprintf('A repo used for crudListObjects needs to implement: %s', QueryBuilderForFilterFormInterface::interfacename));
        }

        $qb = $repo->getQueryBuilderForFilterForm($formData);

        $pagination = $this->crudPaginate($qb, $request);

        $baseTemplateVars = array(
            'request' => $request,
            'pagination' => $pagination,
            'form' => isset($form) ? $form->createView() : null,
            'listing' => $this->crudListing(),
            'listRoute' => $this->crudListRoute(),
            'createRoute' => $this->crudCreateRoute(),
            'editRoute' => $this->crudEditRoute(),
            'viewRoute' => $this->crudViewRoute(),
            'deleteRoute' => $this->crudDeleteRoute(),
            'listRole' => $this->crudListRole(),
            'createRole' => $this->crudCreateRole(),
            'editRole' => $this->crudEditRole(),
            'viewRole' => $this->crudViewRole(),
            'deleteRole' => $this->crudDeleteRole(),
            'identifier' => $this->crudIdentifier(),
            'transPrefix' => $this->crudTransPrefix(),
            'transDomain' => $this->crudTransDomain(),
            'objectClass' => $this->crudObjectClass(),
        );

        return $this->crudListRenderTemplateResponse(
            $request,
            $baseTemplateVars,
            $templateVars
        );
    }

    /**
     * @param  Request                   $request
     * @param  array                     $templateVars
     * @return Response|RedirectResponse
     */
    public function crudCreateObject(Request $request, array $templateVars = array())
    {
        $crudCreateRole = $this->crudCreateRole();
        if (!$this->crudSecurity()->isGranted($crudCreateRole)) {
            throw new AccessDeniedException(sprintf('You need the permission to create an object, role: %s!', $crudCreateRole));
        }

        $object = $this->crudCreateFactory($request);
        $form = $this->crudCreateForm($object, $request);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($this->crudCreateIsSubmitted($object, $form, $request)) {
                if ($form->isValid()) {
                    $this->crudCreatePrePersist($object, $form, $request);

                    $em = $this->crudManagerForClass($this->crudObjectClass());
                    $em->persist($object);
                    $em->flush();

                    $this->crudCreatePostFlush($object, $form, $request);
                    $this->crudCreateSuccessFlashMesssage($object, $form, $request);
                    $response = $this->crudCreateSuccessResponse($object, $form, $request);
                } else {
                    $this->crudCreateErrorFlashMesssage($object, $form, $request);
                    $response = $this->crudCreateErrorResponse($object, $form, $request);
                }

                if (null !== $response) {
                    return $response;
                }
            }
        }

        $baseTemplateVars = array(
            'request' => $request,
            'object' => $object,
            'form' => $form->createView(),
            'createRoute' => $this->crudCreateRoute(),
            'listRoute' => $this->crudListRoute(),
            'editRoute' => $this->crudEditRoute(),
            'viewRoute' => $this->crudViewRoute(),
            'deleteRoute' => $this->crudDeleteRoute(),
            'listRole' => $this->crudListRole(),
            'createRole' => $this->crudCreateRole(),
            'editRole' => $this->crudEditRole(),
            'viewRole' => $this->crudViewRole(),
            'deleteRole' => $this->crudDeleteRole(),
            'identifier' => $this->crudIdentifier(),
            'transPrefix' => $this->crudTransPrefix(),
            'transDomain' => $this->crudTransDomain(),
            'objectClass' => $this->crudObjectClass(),
        );

        return $this->crudCreateRenderTemplateResponse(
            $request,
            $baseTemplateVars,
            $templateVars
        );
    }

    /**
     * @param  Request                   $request
     * @param  object|string|int         $object
     * @param  array                     $templateVars
     * @return Response|RedirectResponse
     */
    public function crudEditObject(Request $request, $object, array $templateVars = array())
    {
        $object = $this->crudEditLoadObject($object, $request);

        $crudEditRole = $this->crudEditRole();
        if (!$this->crudSecurity()->isGranted($crudEditRole, $object)) {
            throw new AccessDeniedException(sprintf('You need the permission to edit this object, role: %s!', $crudEditRole));
        }

        $form = $this->crudEditForm($object, $request);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($this->crudEditIsSubmitted($object, $form, $request)) {
                if ($form->isValid()) {
                    $this->crudEditPrePersist($object, $form, $request);

                    $em = $this->crudManagerForClass($this->crudObjectClass());
                    $em->persist($object);
                    $em->flush();

                    $this->crudEditPostFlush($object, $form, $request);
                    $this->crudEditSuccessFlashMesssage($object, $form, $request);
                    $response = $this->crudEditSuccessResponse($object, $form, $request);
                } else {
                    $this->crudEditErrorFlashMesssage($object, $form, $request);
                    $response = $this->crudEditErrorResponse($object, $form, $request);
                }

                if (null !== $response) {
                    return $response;
                }
            }
        }

        $baseTemplateVars = array(
            'request' => $request,
            'object' => $object,
            'form' => $form->createView(),
            'createRoute' => $this->crudCreateRoute(),
            'listRoute' => $this->crudListRoute(),
            'editRoute' => $this->crudEditRoute(),
            'viewRoute' => $this->crudViewRoute(),
            'deleteRoute' => $this->crudDeleteRoute(),
            'listRole' => $this->crudListRole(),
            'createRole' => $this->crudCreateRole(),
            'editRole' => $this->crudEditRole(),
            'viewRole' => $this->crudViewRole(),
            'deleteRole' => $this->crudDeleteRole(),
            'identifier' => $this->crudIdentifier(),
            'transPrefix' => $this->crudTransPrefix(),
            'transDomain' => $this->crudTransDomain(),
            'objectClass' => $this->crudObjectClass(),
        );

        return $this->crudEditRenderTemplateResponse(
            $request,
            $baseTemplateVars,
            $templateVars
        );
    }

    /**
     * @param  Request                   $request
     * @param  object|string|int         $object
     * @param  array                     $templateVars
     * @return Response|RedirectResponse
     */
    public function crudViewObject(Request $request, $object, array $templateVars = array())
    {
        $object = $this->crudViewLoadObject($object, $request);

        $crudViewRole = $this->crudViewRole();
        if (!$this->crudSecurity()->isGranted($crudViewRole, $object)) {
            throw new AccessDeniedException(sprintf('You need the permission to view this object, role: %s!', $crudViewRole));
        }

        $baseTemplateVars = array(
            'request' => $request,
            'object' => $object,
            'createRoute' => $this->crudCreateRoute(),
            'listRoute' => $this->crudListRoute(),
            'editRoute' => $this->crudEditRoute(),
            'viewRoute' => $this->crudViewRoute(),
            'deleteRoute' => $this->crudDeleteRoute(),
            'listRole' => $this->crudListRole(),
            'createRole' => $this->crudCreateRole(),
            'editRole' => $this->crudEditRole(),
            'viewRole' => $this->crudViewRole(),
            'deleteRole' => $this->crudDeleteRole(),
            'identifier' => $this->crudIdentifier(),
            'transPrefix' => $this->crudTransPrefix(),
            'transDomain' => $this->crudTransDomain(),
            'objectClass' => $this->crudObjectClass(),
        );

        return $this->crudViewRenderTemplateResponse(
            $request,
            $baseTemplateVars,
            $templateVars
        );
    }

    /**
     * @param  Request                   $request
     * @param  object|string|int         $object
     * @return Response|RedirectResponse
     */
    public function crudDeleteObject(Request $request, $object)
    {
        $object = $this->crudDeleteLoadObject($object, $request);

        $crudDeleteRole = $this->crudDeleteRole();
        if (!$this->crudSecurity()->isGranted($crudDeleteRole, $object)) {
            throw new AccessDeniedException(sprintf('You need the permission to delete this object, role: %s!', $crudDeleteRole));
        }

        $this->crudDeletePreRemove($object, $request);

        $em = $this->crudManagerForClass($this->crudObjectClass());
        $em->remove($object);
        $em->flush();

        $this->crudDeletePostFlush($object, $request);
        $this->crudDeleteSuccessFlashMesssage($object, $request);

        return $this->crudDeleteSuccessResponse($object, $request);
    }

    /**
     * @return int
     */
    protected function crudListPerPage()
    {
        return 10;
    }

    /**
     * @return Listing
     */
    protected function crudListing()
    {
        return $this->crudListingFactory()->createByClass($this->crudObjectClass());
    }

    /**
     * @return string
     */
    protected function crudListRoute()
    {
        return strtolower(sprintf($this->crudRoutePattern(), $this->crudName(), 'list'));
    }

    /**
     * @return string
     */
    protected function crudListRole()
    {
        return strtoupper(sprintf($this->crudRolePattern(), $this->crudName(), 'list'));
    }

    /**
     * @param  Request            $request
     * @return FormInterface|null
     */
    protected function crudListForm(Request $request)
    {
        if (null === $formType = $this->crudListFormType()) {
            return null;
        }

        return $this->crudForm($formType, array());
    }

    /**
     * @return FormTypeInterface|null
     */
    protected function crudListFormType()
    {
        return null;
    }

    /**
     * @param  Request $request
     * @param  array   $formData
     * @return array
     */
    protected function crudListFormDataEnrich(Request $request, array $formData)
    {
        return array_replace_recursive($formData, array());
    }

    /**
     * @param  Request  $request
     * @param  array    $baseTemplateVars
     * @param  array    $templateVars
     * @return Response
     */
    protected function crudListRenderTemplateResponse(Request $request, array $baseTemplateVars, array $templateVars)
    {
        return $this->crudRender(
            $this->crudListTemplate(),
            array_replace_recursive($baseTemplateVars, $templateVars)
        );
    }

    /**
     * @return string
     */
    protected function crudListTemplate()
    {
        return sprintf($this->crudTemplatePattern(), ucfirst($this->crudName()), 'list');
    }

    /**
     * @return string
     */
    protected function crudCreateRoute()
    {
        return strtolower(sprintf($this->crudRoutePattern(), $this->crudName(), 'create'));
    }

    /**
     * @return string
     */
    protected function crudCreateRole()
    {
        return strtoupper(sprintf($this->crudRolePattern(), $this->crudName(), 'create'));
    }

    /**
     * @param  Request $request
     * @return object
     */
    protected function crudCreateFactory(Request $request)
    {
        $objectClass = $this->crudObjectClass();

        return new $objectClass();
    }

    /**
     * @param  object        $object
     * @param  Request       $request
     * @return FormInterface
     */
    protected function crudCreateForm($object, Request $request)
    {
        return $this->crudForm($this->crudCreateFormType(), $object);
    }

    /**
     * @return FormTypeInterface
     * @throws \Exception
     */
    protected function crudCreateFormType()
    {
        throw new \Exception('You need to implement this method, if you use the createObject method!');
    }

    /**
     * @param  object        $object
     * @param  FormInterface $form
     * @param  Request       $request
     * @return bool
     */
    protected function crudCreateIsSubmitted($object, FormInterface $form, Request $request)
    {
        $buttonName = $this->crudCreateButtonName();
        if (null !== $buttonName && !$form->get($buttonName)->isClicked()) {
            return false;
        }

        return true;
    }

    /**
     * @return string|null
     */
    protected function crudCreateButtonName()
    {
        return null;
    }

    /**
     * @param  object        $object
     * @param  FormInterface $form
     * @param  Request       $request
     * @return void
     */
    protected function crudCreateSuccessFlashMesssage($object, FormInterface $form, Request $request)
    {
        $this->crudFlashMessage($request, 'success', sprintf('%s.create.flash.success', $this->crudTransPrefix()));
    }

    /**
     * @param  object        $object
     * @param  FormInterface $form
     * @param  Request       $request
     * @return void
     */
    protected function crudCreateErrorFlashMesssage($object, FormInterface $form, Request $request)
    {
        $this->crudFlashMessage($request, 'success', sprintf('%s.create.flash.error', $this->crudTransPrefix()));
    }

    /**
     * @param  object                    $object
     * @param  FormInterface             $form
     * @param  Request                   $request
     * @return RedirectResponse|Response
     */
    protected function crudCreateSuccessResponse($object, FormInterface $form, Request $request)
    {
        $identifierMethod = $this->crudIdentifierMethod();
        $url = $this->crudGenerateRoute($this->crudEditRoute(), array('id' => $object->$identifierMethod()));

        return new RedirectResponse($url, 302);
    }

    /**
     * @param  object                         $object
     * @param  FormInterface                  $form
     * @param  Request                        $request
     * @return RedirectResponse|Response|null
     */
    protected function crudCreateErrorResponse($object, FormInterface $form, Request $request)
    {
        return null;
    }

    /**
     * @param  Request  $request
     * @param  array    $baseTemplateVars
     * @param  array    $templateVars
     * @return Response
     */
    protected function crudCreateRenderTemplateResponse(Request $request, array $baseTemplateVars, array $templateVars)
    {
        return $this->crudRender(
            $this->crudCreateTemplate(),
            array_replace_recursive($baseTemplateVars, $templateVars)
        );
    }

    /**
     * @return string
     */
    protected function crudCreateTemplate()
    {
        return sprintf($this->crudTemplatePattern(), ucfirst($this->crudName()), 'create');
    }

    /**
     * @param  object        $object
     * @param  FormInterface $form
     * @param  Request       $request
     * @return void
     */
    protected function crudCreatePrePersist($object, FormInterface $form, Request $request)
    {
    }

    /**
     * @param  object        $object
     * @param  FormInterface $form
     * @param  Request       $request
     * @return void
     */
    protected function crudCreatePostFlush($object, FormInterface $form, Request $request)
    {
    }

    /**
     * @return string
     */
    protected function crudEditRoute()
    {
        return strtolower(sprintf($this->crudRoutePattern(), $this->crudName(), 'edit'));
    }

    /**
     * @param  object|string|int $object
     * @param  Request           $request
     * @return object
     */
    protected function crudEditLoadObject($object, Request $request)
    {
        return $this->crudLoadObject($object, $request);
    }

    /**
     * @return string
     */
    protected function crudEditRole()
    {
        return strtoupper(sprintf($this->crudRolePattern(), $this->crudName(), 'edit'));
    }

    /**
     * @param  object        $object
     * @param  Request       $request
     * @return FormInterface
     */
    protected function crudEditForm($object, Request $request)
    {
        return $this->crudForm($this->crudEditFormType(), $object);
    }

    /**
     * @return FormTypeInterface
     * @throws \Exception
     */
    protected function crudEditFormType()
    {
        throw new \Exception('You need to implement this method, if you use the editObject method!');
    }

    /**
     * @param  object        $object
     * @param  FormInterface $form
     * @param  Request       $request
     * @return bool
     */
    protected function crudEditIsSubmitted($object, FormInterface $form, Request $request)
    {
        $buttonName = $this->crudEditButtonName();
        if (null !== $buttonName && !$form->get($buttonName)->isClicked()) {
            return false;
        }

        return true;
    }

    /**
     * @return string|null
     */
    protected function crudEditButtonName()
    {
        return null;
    }

    /**
     * @param  object        $object
     * @param  FormInterface $form
     * @param  Request       $request
     * @return void
     */
    protected function crudEditSuccessFlashMesssage($object, FormInterface $form, Request $request)
    {
        $this->crudFlashMessage($request, 'success', sprintf('%s.edit.flash.success', $this->crudTransPrefix()));
    }

    /**
     * @param  object        $object
     * @param  FormInterface $form
     * @param  Request       $request
     * @return void
     */
    protected function crudEditErrorFlashMesssage($object, FormInterface $form, Request $request)
    {
        $this->crudFlashMessage($request, 'success', sprintf('%s.edit.flash.error', $this->crudTransPrefix()));
    }

    /**
     * @param  object                    $object
     * @param  FormInterface             $form
     * @param  Request                   $request
     * @return RedirectResponse|Response
     */
    protected function crudEditSuccessResponse($object, FormInterface $form, Request $request)
    {
        $identifierMethod = $this->crudIdentifierMethod();
        $url = $this->crudGenerateRoute($this->crudEditRoute(), array('id' => $object->$identifierMethod()));

        return new RedirectResponse($url, 302);
    }

    /**
     * @param  object                         $object
     * @param  FormInterface                  $form
     * @param  Request                        $request
     * @return RedirectResponse|Response|null
     */
    protected function crudEditErrorResponse($object, FormInterface $form, Request $request)
    {
        return null;
    }

    /**
     * @param  Request  $request
     * @param  array    $baseTemplateVars
     * @param  array    $templateVars
     * @return Response
     */
    protected function crudEditRenderTemplateResponse(Request $request, array $baseTemplateVars, array $templateVars)
    {
        return $this->crudRender(
            $this->crudEditTemplate(),
            array_replace_recursive($baseTemplateVars, $templateVars)
        );
    }

    /**
     * @return string
     */
    protected function crudEditTemplate()
    {
        return sprintf($this->crudTemplatePattern(), ucfirst($this->crudName()), 'edit');
    }

    /**
     * @param  object        $object
     * @param  FormInterface $form
     * @param  Request       $request
     * @return void
     */
    protected function crudEditPrePersist($object, FormInterface $form, Request $request)
    {
    }

    /**
     * @param  object        $object
     * @param  FormInterface $form
     * @param  Request       $request
     * @return void
     */
    protected function crudEditPostFlush($object, FormInterface $form, Request $request)
    {
    }

    /**
     * @return string
     */
    protected function crudViewRoute()
    {
        return strtolower(sprintf($this->crudRoutePattern(), $this->crudName(), 'view'));
    }

    /**
     * @param  object|string|int $object
     * @param  Request           $request
     * @return object
     */
    protected function crudViewLoadObject($object, Request $request)
    {
        return $this->crudLoadObject($object, $request);
    }

    /**
     * @return string
     */
    protected function crudViewRole()
    {
        return strtoupper(sprintf($this->crudRolePattern(), $this->crudName(), 'view'));
    }

    /**
     * @param  Request  $request
     * @param  array    $baseTemplateVars
     * @param  array    $templateVars
     * @return Response
     */
    protected function crudViewRenderTemplateResponse(Request $request, array $baseTemplateVars, array $templateVars)
    {
        return $this->crudRender(
            $this->crudViewTemplate(),
            array_replace_recursive($baseTemplateVars, $templateVars)
        );
    }

    /**
     * @return string
     */
    protected function crudViewTemplate()
    {
        return sprintf($this->crudTemplatePattern(), ucfirst($this->crudName()), 'view');
    }

    /**
     * @return string
     */
    protected function crudDeleteRoute()
    {
        return strtolower(sprintf($this->crudRoutePattern(), $this->crudName(), 'delete'));
    }

    /**
     * @param  object|string|int $object
     * @param  Request           $request
     * @return object
     */
    protected function crudDeleteLoadObject($object, Request $request)
    {
        return $this->crudLoadObject($object, $request);
    }

    /**
     * @return string
     */
    protected function crudDeleteRole()
    {
        return strtoupper(sprintf($this->crudRolePattern(), $this->crudName(), 'delete'));
    }

    /**
     * @param  object  $object
     * @param  Request $request
     * @return void
     */
    protected function crudDeleteSuccessFlashMesssage($object, Request $request)
    {
        $this->crudFlashMessage($request, 'success', sprintf('%s.delete.flash.success', $this->crudTransPrefix()));
    }

    /**
     * @param  object                    $object
     * @param  Request                   $request
     * @return RedirectResponse|Response
     */
    protected function crudDeleteSuccessResponse($object, Request $request)
    {
        return new RedirectResponse($this->crudGenerateRoute($this->crudListRoute()), 302);
    }

    /**
     * @param  object  $object
     * @param  Request $request
     * @return void
     */
    protected function crudDeletePreRemove($object, Request $request)
    {
    }

    /**
     * @param  object  $object
     * @param  Request $request
     * @return void
     */
    protected function crudDeletePostFlush($object, Request $request)
    {
    }

    /**
     * @return string
     */
    protected function crudRoutePattern()
    {
        return '%s_%s';
    }

    /**
     * @return string
     */
    protected function crudRolePattern()
    {
        return 'role_%s_%s';
    }

    /**
     * @return string
     */
    protected function crudTransPrefix()
    {
        return Helper::camelCaseToUnderscore($this->crudName());
    }

    /**
     * @return string
     */
    protected function crudTransDomain()
    {
        return 'messages';
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function crudTemplatePattern()
    {
        throw new \Exception(sprintf(
            'For actions using a template you need to define the template pattern like this: %s',
            '@SaxulumCrud/%s/%s.html.twig'
        ));
    }

    /**
     * @return string
     */
    abstract protected function crudName();

    /**
     * @return string
     */
    abstract protected function crudObjectClass();

    /**
     * @return SecurityContextInterface
     * @throws \Exception
     */
    protected function crudSecurity()
    {
        throw new \Exception(sprintf(
            'For actions using security you need: %s',
            'Symfony\Component\Security\Core\SecurityContextInterface'
        ));
    }

    /**
     * @return ManagerRegistry
     * @throws \Exception
     */
    protected function crudDoctrine()
    {
        throw new \Exception(sprintf(
            'For actions using doctrine you need: %s',
            'Doctrine\Common\Persistence\ManagerRegistry'
        ));
    }

    /**
     * @return FormFactoryInterface
     * @throws \Exception
     */
    protected function crudFormFactory()
    {
        throw new \Exception(sprintf(
            'For actions using form you need: %s',
            'Symfony\Component\Form\FormFactoryInterface'
        ));
    }

    /**
     * @return PaginatorInterface
     * @throws \Exception
     */
    protected function crudPaginator()
    {
        throw new \Exception(sprintf(
            'For actions using pagination you need: %s',
            'Saxulum\Crud\Pagination\PaginatorInterface'
        ));
    }

    /**
     * @return UrlGeneratorInterface
     * @throws \Exception
     */
    protected function crudUrlGenerator()
    {
        throw new \Exception(sprintf(
            'For actions using url generation you need: %s',
            'Symfony\Component\Routing\Generator\UrlGeneratorInterface'
        ));
    }

    /**
     * @return \Twig_Environment
     * @throws \Exception
     */
    protected function crudTwig()
    {
        throw new \Exception(sprintf(
            'For actions using twig you need: %s',
            '\Twig_Environment'
        ));
    }

    /**
     * @return ListingFactory
     * @throws \Exception
     */
    protected function crudListingFactory()
    {
        throw new \Exception(sprintf(
            'For actions using listing factory you need: %s',
            'Saxulum\Crud\Listing\ListingFactory'
        ));
    }

    /**
     * @param  object|string|int $object
     * @param  Request           $request
     * @return object            object
     */
    protected function crudLoadObject($object, Request $request)
    {
        if (is_object($object)) {
            return $object;
        }

        /** @var ObjectRepository $repo */
        $repo = $this->crudRepositoryForClass($this->crudObjectClass());
        $object = $repo->find($object);

        if (null === $object) {
            throw new NotFoundHttpException("There is no object with this id");
        }

        return $object;
    }

    /**
     * @param  string        $class
     * @return ObjectManager
     * @throws \Exception
     */
    protected function crudManagerForClass($class)
    {
        $om = $this->crudDoctrine()->getManagerForClass($class);

        if (null === $om) {
            throw new \Exception(sprintf('There is no object manager for class: %s', $class));
        }

        return $om;
    }

    /**
     * @param  string           $class
     * @return ObjectRepository
     */
    protected function crudRepositoryForClass($class)
    {
        return $this->crudManagerForClass($class)->getRepository($class);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function crudIdentifier()
    {
        $em = $this->crudManagerForClass($this->crudObjectClass());
        $meta = $em->getClassMetadata($this->crudObjectClass());

        $identifier = $meta->getIdentifier();

        if (1 !== count($identifier)) {
            throw new \Exception('There are multiple fields define the identifier, which is not supported!');
        }

        return reset($identifier);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function crudIdentifierMethod()
    {
        $identifier = $this->crudIdentifier();

        return 'get'.ucfirst($identifier);
    }

    /**
     * @param  FormTypeInterface $type
     * @param  mixed             $data
     * @param  array             $options
     * @return FormInterface
     */
    protected function crudForm(FormTypeInterface $type, $data = null, array $options = array())
    {
        return $this->crudFormFactory()->create($type, $data, $options);
    }

    /**
     * @param  object       $target
     * @param  Request      $request
     * @return \Traversable
     */
    protected function crudPaginate($target, Request $request)
    {
        return $this->crudPaginator()->paginate(
            $target,
            $request->query->get('page', 1),
            $request->query->get('perPage', $this->crudListPerPage())
        );
    }

    /**
     * @param  string $name
     * @param  array  $parameters
     * @return string
     */
    protected function crudGenerateRoute($name, array $parameters = array())
    {
        return $this->crudUrlGenerator()->generate($name, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param  string   $view
     * @param  array    $parameters
     * @return Response
     */
    protected function crudRender($view, array $parameters = array())
    {
        return new Response($this->crudTwig()->render($view, $parameters));
    }

    /**
     * @param Request $request
     * @param string  $type
     * @param string  $message
     */
    protected function crudFlashMessage(Request $request, $type, $message)
    {
        /** @var Session $session */
        $session = $request->getSession();
        $session->getFlashBag()->add($type, $message);
    }
}
