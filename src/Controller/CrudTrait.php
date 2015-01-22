<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Saxulum\Crud\Repository\QueryBuilderForFilterFormInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Translation\Translator;

trait CrudTrait
{
    /**
     * @param Request $request
     * @param array $templateVars
     * @return Response
     */
    public function crudListObjects(Request $request, array $templateVars = array())
    {
        if(!$this->crudListIsGranted()) {
            throw new AccessDeniedException("You need the permission to list entities!");
        }

        if(null !== $formType = $this->crudListFormType()) {
            $form = $this->crudForm($formType);
            $form->handleRequest($request);
            $formData = $form->getData();
        }

        if(!isset($formData) || null === $formData) {
            $formData = array();
        }

        $formData = array_replace_recursive($formData, $this->crudListDefaultData());

        /** @var QueryBuilderForFilterFormInterface $repo */
        $repo = $this->crudRepositoryForClass($this->crudObjectClass());
        $qb = $repo->getQueryBuilderForFilterForm($formData);

        $pagination = $this->crudPaginate($qb, $request);

        $baseTemplateVars = array(
            'request' => $request,
            'form' => isset($form) ? $form->createView() : null,
            'pagination' => $pagination,
            'listRoute' => $this->crudListRoute(),
            'createRoute' => $this->crudCreateRoute(),
            'editRoute' => $this->crudEditRoute(),
            'viewRoute' => $this->crudViewRoute(),
            'deleteRoute' => $this->crudDeleteRoute(),
            'identifier' => $this->crudIdentifier(),
            'transPrefix' => $this->crudName(),
        );

        return $this->crudRender(
            $this->crudListTemplate(),
            array_replace_recursive($baseTemplateVars, $templateVars)
        );
    }

    /**
     * @param Request $request
     * @param array   $templateVars
     * @return Response|RedirectResponse
     */
    protected function crudCreateObject(Request $request, array $templateVars = array())
    {
        if(!$this->crudCreateIsGranted()) {
            throw new AccessDeniedException("You need the permission to create an object!");
        }

        $object = $this->crudCreateFactory();
        $form = $this->crudForm($this->crudCreateFormType(), $object);

        if('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if($form->isValid()) {
                $this->crudPrePersist($object);

                $em = $this->crudManagerForClass($this->crudObjectClass());
                $em->persist($object);
                $em->flush();

                $this->crudPostPersist($object);

                $this->crudFlashMessage($request, 'success', $this->crudName() . '.create.flash.success');

                return new RedirectResponse($this->crudCreateRedirectUrl($object), 302);
            } else {
                $this->crudFlashMessage($request, 'error', $this->crudName() . '.create.flash.error');
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
            'identifier' => $this->crudIdentifier(),
            'transPrefix' => $this->crudName(),
        );

        return $this->crudRender(
            $this->crudCreateTemplate(),
            array_replace_recursive($baseTemplateVars, $templateVars)
        );
    }

    /**
     * @param Request $request
     * @param int $id
     * @param array   $templateVars
     * @return Response|RedirectResponse
     */
    protected function crudEditObject(Request $request, $id, array $templateVars = array())
    {
        /** @var ObjectRepository $repo */
        $repo = $this->crudRepositoryForClass($this->crudObjectClass());
        $object = $repo->find($id);

        if(null === $object) {
            throw new NotFoundHttpException("There is no object with this id");
        }

        if(!$this->crudEditIsGranted($object)) {
            throw new AccessDeniedException("You need the permission to edit this object!");
        }

        $form = $this->crudForm($this->crudEditFormType(), $object);

        if('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if($form->isValid()) {
                $this->crudPreUpdate($object);

                $em = $this->crudManagerForClass($this->crudObjectClass());
                $em->persist($object);
                $em->flush();

                $this->crudPostUpdate($object);

                $this->crudFlashMessage($request, 'success', $this->crudName() . '.edit.flash.success');
            } else {
                $this->crudFlashMessage($request, 'error', $this->crudName() . '.edit.flash.error');
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
            'identifier' => $this->crudIdentifier(),
            'transPrefix' => $this->crudName(),
        );

        return $this->crudRender(
            $this->crudEditTemplate(),
            array_replace_recursive($baseTemplateVars, $templateVars)
        );
    }

    /**
     * @param Request $request
     * @param int $id
     * @param array   $templateVars
     * @return Response|RedirectResponse
     */
    protected function crudViewObject(Request $request, $id, array $templateVars = array())
    {
        /** @var ObjectRepository $repo */
        $repo = $this->crudRepositoryForClass($this->crudObjectClass());
        $object = $repo->find($id);

        if(null === $object) {
            throw new NotFoundHttpException("There is no object with this id");
        }

        if(!$this->crudViewIsGranted($object)) {
            throw new AccessDeniedException("You need the permission to view this object!");
        }

        $baseTemplateVars = array(
            'request' => $request,
            'object' => $object,
            'createRoute' => $this->crudCreateRoute(),
            'listRoute' => $this->crudListRoute(),
            'editRoute' => $this->crudEditRoute(),
            'viewRoute' => $this->crudViewRoute(),
            'deleteRoute' => $this->crudDeleteRoute(),
            'identifier' => $this->crudIdentifier(),
            'transPrefix' => $this->crudName(),
        );

        return $this->crudRender(
            $this->crudViewTemplate(),
            array_replace_recursive($baseTemplateVars, $templateVars)
        );
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response|RedirectResponse
     */
    protected function crudDeleteObject(Request $request, $id)
    {
        /** @var ObjectRepository $repo */
        $repo = $this->crudRepositoryForClass($this->crudObjectClass());
        $object = $repo->find($id);

        if(null === $object) {
            throw new NotFoundHttpException("There is no object with this id");
        }

        if(!$this->crudDeleteIsGranted($object)) {
            throw new AccessDeniedException("You need the permission to delete this object!");
        }

        $this->crudPreRemove($object);

        $em = $this->crudManagerForClass($this->crudObjectClass());
        $em->remove($object);
        $em->flush();

        $this->crudPostRemove($object);

        $this->crudFlashMessage($request, 'success', $this->crudName() . '.delete.flash.success');

        return new RedirectResponse($this->crudListRedirectUrl(), 302);
    }

    /**
     * @param  string               $type
     * @param  null                 $data
     * @param  array                $options
     * @param  FormBuilderInterface $parent
     * @return Form
     */
    protected function crudForm($type = 'form', $data = null, array $options = array(), FormBuilderInterface $parent = null)
    {
        return $this->getFormFactory()->createBuilder($type, $data, $options, $parent)->getForm();
    }

    /**
     * @param string $class
     * @return ObjectManager|null
     */
    protected function crudManagerForClass($class)
    {
        return $this->getDoctrine()->getManagerForClass($class);
    }

    /**
     * @param string $class
     * @return ObjectRepository
     */
    protected function crudRepositoryForClass($class)
    {
        return $this->crudManagerForClass($class)->getRepository($class);
    }

    /**
     * @param object  $qb
     * @param Request $request
     * @return AbstractPagination
     */
    protected function crudPaginate($qb, Request $request)
    {
        return $this->getPaginator()->paginate(
            $qb,
            $request->query->get('page', 1),
            $request->query->get('perPage', $this->crudPaginatePerPage())
        );
    }

    /**
     * @param string $view
     * @param  array  $parameters
     * @return Response
     */
    protected function crudRender($view, array $parameters = array())
    {
        return new Response($this->getTwig()->render($view, $parameters));
    }

    /**
     * @param string $name
     * @param array  $parameters
     * @return string
     */
    protected function crudGenerateRoute($name, array $parameters = array())
    {
        return $this->getUrlGenerator()->generate($name, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
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

    /**
     * @return string
     * @throws \Exception
     */
    protected function crudIdentifier()
    {
        $em = $this->crudManagerForClass($this->crudObjectClass());
        $meta = $em->getClassMetadata($this->crudObjectClass());

        $identifier = $meta->getIdentifier();

        if(1 !== count($identifier)) {
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

        return 'get'. ucfirst($identifier);
    }

    /**
     * @return string
     */
    protected function crudTwigTemplatePattern()
    {
        return '@SaxulumCrud/%s/%s.html.twig';
    }

    /**
     * @return int
     */
    protected function crudPaginatePerPage()
    {
        return 10;
    }

    /**
     * @return string
     */
    protected function crudListRoute()
    {
        return $this->crudName() . '_list';
    }

    /**
     * @return bool
     */
    protected function crudListIsGranted()
    {
        return $this->getSecurity()->isGranted($this->crudListRole());
    }

    /**
     * @return string
     */
    protected function crudListRole()
    {
        return 'ROLE_' . strtoupper($this->crudName()) . '_LIST';
    }

    /**
     * @return FormTypeInterface|null
     */
    protected function crudListFormType()
    {
        return null;
    }

    /**
     * @return array
     */
    protected function crudListDefaultData()
    {
        return array();
    }

    /**
     * @return string
     */
    protected function crudListTemplate()
    {
        return sprintf($this->crudTwigTemplatePattern(), ucfirst($this->crudName()), 'list');
    }

    /**
     * @return string
     */
    protected function crudListRedirectUrl()
    {
        return $this->crudGenerateRoute($this->crudListRoute());
    }

    /**
     * @return string
     */
    protected function crudCreateRoute()
    {
        return $this->crudName() . '_create';
    }

    /**
     * @return bool
     */
    protected function crudCreateIsGranted()
    {
        return $this->getSecurity()->isGranted($this->crudCreateRole());
    }

    /**
     * @return string
     */
    protected function crudCreateRole()
    {
        return 'ROLE_' . strtoupper($this->crudName()) . '_CREATE';
    }

    /**
     * @return object
     */
    protected function crudCreateFactory()
    {
        $objectClass = $this->crudObjectClass();

        return new $objectClass;
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
     * @param object
     * @return string
     */
    protected function crudCreateRedirectUrl($object)
    {
        $identifierMethod = $this->crudIdentifierMethod();

        return $this->crudGenerateRoute($this->crudEditRoute(), array('id' => $object->$identifierMethod()));
    }

    /**
     * @return string
     */
    protected function crudCreateTemplate()
    {
        return sprintf($this->crudTwigTemplatePattern(), ucfirst($this->crudName()), 'create');
    }

    /**
     * @return string
     */
    protected function crudEditRoute()
    {
        return $this->crudName() . '_edit';
    }

    /**
     * @param object
     * @return bool
     */
    protected function crudEditIsGranted($object)
    {
        return $this->getSecurity()->isGranted($this->crudEditRole(), $object);
    }

    /**
     * @return string
     */
    protected function crudEditRole()
    {
        return 'ROLE_' . strtoupper($this->crudName()) . '_EDIT';
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
     * @return string
     */
    protected function crudEditTemplate()
    {
        return sprintf($this->crudTwigTemplatePattern(), ucfirst($this->crudName()), 'edit');
    }

    /**
     * @return string
     */
    protected function crudViewRoute()
    {
        return $this->crudName() . '_view';
    }

    /**
     * @param object
     * @return bool
     */
    protected function crudViewIsGranted($object)
    {
        return $this->getSecurity()->isGranted($this->crudViewRole(), $object);
    }

    /**
     * @return string
     */
    protected function crudViewRole()
    {
        return 'ROLE_' . strtoupper($this->crudName()) . '_VIEW';
    }

    /**
     * @return string
     */
    protected function crudViewTemplate()
    {
        return sprintf($this->crudTwigTemplatePattern(), ucfirst($this->crudName()), 'view');
    }

    /**
     * @return string
     */
    protected function crudDeleteRoute()
    {
        return $this->crudName() . '_delete';
    }

    /**
     * @param $object
     * @return bool
     */
    protected function crudDeleteIsGranted($object)
    {
        return $this->getSecurity()->isGranted($this->crudDeleteRole(), $object);
    }

    /**
     * @return string
     */
    protected function crudDeleteRole()
    {
        return 'ROLE_' . strtoupper($this->crudName()) . '_DELETE';
    }

    /**
     * @param object $object
     * @return void
     */
    protected function crudPrePersist($object) {}

    /**
     * @param object $object
     * @return void
     */
    protected function crudPostPersist($object) {}

    /**
     * @param object $object
     * @return void
     */
    protected function crudPreUpdate($object) {}

    /**
     * @param object $object
     * @return void
     */
    protected function crudPostUpdate($object) {}

    /**
     * @param object $object
     * @return void
     */
    protected function crudPreRemove($object) {}

    /**
     * @param object $object
     * @return void
     */
    protected function crudPostRemove($object) {}

    /**
     * @return FormFactory
     */
    abstract protected function getFormFactory();

    /**
     * @return ManagerRegistry
     */
    abstract protected function getDoctrine();

    /**
     * @return Paginator
     */
    abstract protected function getPaginator();

    /**
     * @return \Twig_Environment
     */
    abstract protected function getTwig();

    /**
     * @return UrlGeneratorInterface
     */
    abstract protected function getUrlGenerator();

    /**
     * @return AuthorizationCheckerInterface
     */
    abstract protected function getSecurity();

    /**
     * @return string
     */
    abstract protected function crudName();

    /**
     * @return string
     */
    abstract protected function crudObjectClass();
}
