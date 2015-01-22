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
    public function listObjects(Request $request, array $templateVars = array())
    {
        if(!$this->getListIsGranted()) {
            throw new AccessDeniedException("You need the permission to list entities!");
        }

        if(null !== $formType = $this->getListFormType()) {
            $form = $this->createForm($formType);
            $form->handleRequest($request);
            $formData = $form->getData();
        }

        if(!isset($formData) || null === $formData) {
            $formData = array();
        }

        $formData = array_replace_recursive($formData, $this->getListDefaultData());

        /** @var QueryBuilderForFilterFormInterface $repo */
        $repo = $this->getRepositoryForClass($this->getObjectClass());
        $qb = $repo->getQueryBuilderForFilterForm($formData);

        $pagination = $this->paginate($qb, $request);

        $baseTemplateVars = array(
            'request' => $request,
            'form' => isset($form) ? $form->createView() : null,
            'pagination' => $pagination,
            'listRoute' => $this->getListRoute(),
            'createRoute' => $this->getCreateRoute(),
            'editRoute' => $this->getEditRoute(),
            'viewRoute' => $this->getViewRoute(),
            'deleteRoute' => $this->getDeleteRoute(),
            'identifier' => $this->getIdentifier(),
            'transPrefix' => $this->getName(),
        );

        return $this->render(
            $this->getListTemplate(),
            array_replace_recursive($baseTemplateVars, $templateVars)
        );
    }

    /**
     * @param Request $request
     * @param array   $templateVars
     * @return Response|RedirectResponse
     */
    protected function createObject(Request $request, array $templateVars = array())
    {
        if(!$this->getCreateIsGranted()) {
            throw new AccessDeniedException("You need the permission to create an object!");
        }

        $object = $this->getCreateObject();
        $form = $this->createForm($this->getCreateFormType(), $object);

        if('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if($form->isValid()) {
                $this->prePersist($object);

                $em = $this->getManagerForClass($this->getObjectClass());
                $em->persist($object);
                $em->flush();

                $this->postPersist($object);

                $this->addFlashMessage($request, 'success', $this->getName() . '.create.flash.success');

                return new RedirectResponse($this->getCreateRedirectUrl($object), 302);
            } else {
                $this->addFlashMessage($request, 'error', $this->getName() . '.create.flash.error');
            }
        }

        $baseTemplateVars = array(
            'request' => $request,
            'object' => $object,
            'form' => $form->createView(),
            'createRoute' => $this->getCreateRoute(),
            'listRoute' => $this->getListRoute(),
            'editRoute' => $this->getEditRoute(),
            'viewRoute' => $this->getViewRoute(),
            'deleteRoute' => $this->getDeleteRoute(),
            'identifier' => $this->getIdentifier(),
            'transPrefix' => $this->getName(),
        );

        return $this->render(
            $this->getCreateTemplate(),
            array_replace_recursive($baseTemplateVars, $templateVars)
        );
    }

    /**
     * @param Request $request
     * @param int $id
     * @param array   $templateVars
     * @return Response|RedirectResponse
     */
    protected function editObject(Request $request, $id, array $templateVars = array())
    {
        /** @var ObjectRepository $repo */
        $repo = $this->getRepositoryForClass($this->getObjectClass());
        $object = $repo->find($id);

        if(null === $object) {
            throw new NotFoundHttpException("There is no object with this id");
        }

        if(!$this->getEditIsGranted($object)) {
            throw new AccessDeniedException("You need the permission to edit this object!");
        }

        $form = $this->createForm($this->getEditFormType(), $object);

        if('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if($form->isValid()) {
                $this->preUpdate($object);

                $em = $this->getManagerForClass($this->getObjectClass());
                $em->persist($object);
                $em->flush();

                $this->postUpdate($object);

                $this->addFlashMessage($request, 'success', $this->getName() . '.edit.flash.success');
            } else {
                $this->addFlashMessage($request, 'error', $this->getName() . '.edit.flash.error');
            }
        }

        $baseTemplateVars = array(
            'request' => $request,
            'object' => $object,
            'form' => $form->createView(),
            'createRoute' => $this->getCreateRoute(),
            'listRoute' => $this->getListRoute(),
            'editRoute' => $this->getEditRoute(),
            'viewRoute' => $this->getViewRoute(),
            'deleteRoute' => $this->getDeleteRoute(),
            'identifier' => $this->getIdentifier(),
            'transPrefix' => $this->getName(),
        );

        return $this->render(
            $this->getEditTemplate(),
            array_replace_recursive($baseTemplateVars, $templateVars)
        );
    }

    /**
     * @param Request $request
     * @param int $id
     * @param array   $templateVars
     * @return Response|RedirectResponse
     */
    protected function viewObject(Request $request, $id, array $templateVars = array())
    {
        /** @var ObjectRepository $repo */
        $repo = $this->getRepositoryForClass($this->getObjectClass());
        $object = $repo->find($id);

        if(null === $object) {
            throw new NotFoundHttpException("There is no object with this id");
        }

        if(!$this->getViewIsGranted($object)) {
            throw new AccessDeniedException("You need the permission to view this object!");
        }

        $baseTemplateVars = array(
            'request' => $request,
            'object' => $object,
            'createRoute' => $this->getCreateRoute(),
            'listRoute' => $this->getListRoute(),
            'editRoute' => $this->getEditRoute(),
            'viewRoute' => $this->getViewRoute(),
            'deleteRoute' => $this->getDeleteRoute(),
            'identifier' => $this->getIdentifier(),
            'transPrefix' => $this->getName(),
        );

        return $this->render(
            $this->getViewTemplate(),
            array_replace_recursive($baseTemplateVars, $templateVars)
        );
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response|RedirectResponse
     */
    protected function deleteObject(Request $request, $id)
    {
        /** @var ObjectRepository $repo */
        $repo = $this->getRepositoryForClass($this->getObjectClass());
        $object = $repo->find($id);

        if(null === $object) {
            throw new NotFoundHttpException("There is no object with this id");
        }

        if(!$this->getDeleteIsGranted($object)) {
            throw new AccessDeniedException("You need the permission to delete this object!");
        }

        $this->preRemove($object);

        $em = $this->getManagerForClass($this->getObjectClass());
        $em->remove($object);
        $em->flush();

        $this->postRemove($object);

        $this->addFlashMessage($request, 'success', $this->getName() . '.delete.flash.success');

        return new RedirectResponse($this->getListRedirectUrl(), 302);
    }

    /**
     * @return FormFactory
     */
    abstract protected function getFormFactory();

    /**
     * @param  string               $type
     * @param  null                 $data
     * @param  array                $options
     * @param  FormBuilderInterface $parent
     * @return Form
     */
    protected function createForm($type = 'form', $data = null, array $options = array(), FormBuilderInterface $parent = null)
    {
        return $this->getFormFactory()->createBuilder($type, $data, $options, $parent)->getForm();
    }

    /**
     * @return ManagerRegistry
     */
    abstract protected function getDoctrine();

    /**
     * @param string $class
     * @return ObjectManager|null
     */
    protected function getManagerForClass($class)
    {
        return $this->getDoctrine()->getManagerForClass($class);
    }

    /**
     * @param string $class
     * @return ObjectRepository
     */
    protected function getRepositoryForClass($class)
    {
        return $this->getManagerForClass($class)->getRepository($class);
    }

    /**
     * @return Paginator
     */
    abstract protected function getPaginator();

    /**
     * @param object  $qb
     * @param Request $request
     * @return AbstractPagination
     */
    protected function paginate($qb, Request $request)
    {
        return $this->getPaginator()->paginate(
            $qb,
            $request->query->get('page', 1),
            $request->query->get('perPage', $this->getPerPage())
        );
    }

    /**
     * @return \Twig_Environment
     */
    abstract protected function getTwig();

    /**
     * @param string $view
     * @param  array  $parameters
     * @return Response
     */
    protected function render($view, array $parameters = array())
    {
        return new Response($this->getTwig()->render($view, $parameters));
    }

    /**
     * @return UrlGeneratorInterface
     */
    abstract protected function getUrlGenerator();

    /**
     * @param string $name
     * @param array  $parameters
     * @return string
     */
    protected function generateRoute($name, array $parameters = array())
    {
        return $this->getUrlGenerator()->generate($name, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @return AuthorizationCheckerInterface
     */
    abstract protected function getSecurity();

    /**
     * @param Request $request
     * @param string  $type
     * @param string  $message
     */
    protected function addFlashMessage(Request $request, $type, $message)
    {
        /** @var Session $session */
        $session = $request->getSession();
        $session->getFlashBag()->add($type, $message);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getIdentifier()
    {
        $em = $this->getManagerForClass($this->getObjectClass());
        $meta = $em->getClassMetadata($this->getObjectClass());

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
    protected function getIdentifierMethod()
    {
        $identifier = $this->getIdentifier();

        return 'get'. ucfirst($identifier);
    }

    /**
     * @return string
     */
    protected function getTwigTemplatePattern()
    {
        return '@SaxulumCrud/%s/%s.html.twig';
    }

    /**
     * @return int
     */
    protected function getPerPage()
    {
        return 10;
    }

    /**
     * @return string
     */
    protected function getListRoute()
    {
        return $this->getName() . '_list';
    }

    /**
     * @return bool
     */
    protected function getListIsGranted()
    {
        return $this->getSecurity()->isGranted($this->getListRole());
    }

    /**
     * @return string
     */
    protected function getListRole()
    {
        return 'ROLE_' . strtoupper($this->getName()) . '_LIST';
    }

    /**
     * @return FormTypeInterface|null
     */
    protected function getListFormType()
    {
        return null;
    }

    /**
     * @return array
     */
    protected function getListDefaultData()
    {
        return array();
    }

    /**
     * @return string
     */
    protected function getListTemplate()
    {
        return sprintf($this->getTwigTemplatePattern(), ucfirst($this->getName()), 'list');
    }

    /**
     * @return string
     */
    protected function getListRedirectUrl()
    {
        return $this->generateRoute($this->getListRoute());
    }

    /**
     * @return string
     */
    protected function getCreateRoute()
    {
        return $this->getName() . '_create';
    }

    /**
     * @return bool
     */
    protected function getCreateIsGranted()
    {
        return $this->getSecurity()->isGranted($this->getCreateRole());
    }

    /**
     * @return string
     */
    protected function getCreateRole()
    {
        return 'ROLE_' . strtoupper($this->getName()) . '_CREATE';
    }

    /**
     * @return object
     */
    protected function getCreateObject()
    {
        $objectClass = $this->getObjectClass();

        return new $objectClass;
    }

    /**
     * @return FormTypeInterface
     * @throws \Exception
     */
    protected function getCreateFormType()
    {
        throw new \Exception('You need to implement this method, if you use the createObject method!');
    }

    /**
     * @param object
     * @return string
     */
    protected function getCreateRedirectUrl($object)
    {
        $identifierMethod = $this->getIdentifierMethod();

        return $this->generateRoute($this->getEditRoute(), array('id' => $object->$identifierMethod()));
    }

    /**
     * @return string
     */
    protected function getCreateTemplate()
    {
        return sprintf($this->getTwigTemplatePattern(), ucfirst($this->getName()), 'create');
    }

    /**
     * @return string
     */
    protected function getEditRoute()
    {
        return $this->getName() . '_edit';
    }

    /**
     * @param object
     * @return bool
     */
    protected function getEditIsGranted($object)
    {
        return $this->getSecurity()->isGranted($this->getEditRole(), $object);
    }

    /**
     * @return string
     */
    protected function getEditRole()
    {
        return 'ROLE_' . strtoupper($this->getName()) . '_EDIT';
    }

    /**
     * @return FormTypeInterface
     * @throws \Exception
     */
    protected function getEditFormType()
    {
        throw new \Exception('You need to implement this method, if you use the editObject method!');
    }

    /**
     * @return string
     */
    protected function getEditTemplate()
    {
        return sprintf($this->getTwigTemplatePattern(), ucfirst($this->getName()), 'edit');
    }

    /**
     * @return string
     */
    protected function getViewRoute()
    {
        return $this->getName() . '_view';
    }

    /**
     * @param object
     * @return bool
     */
    protected function getViewIsGranted($object)
    {
        return $this->getSecurity()->isGranted($this->getViewRole(), $object);
    }

    /**
     * @return string
     */
    protected function getViewRole()
    {
        return 'ROLE_' . strtoupper($this->getName()) . '_VIEW';
    }

    /**
     * @return string
     */
    protected function getViewTemplate()
    {
        return sprintf($this->getTwigTemplatePattern(), ucfirst($this->getName()), 'view');
    }

    /**
     * @return string
     */
    protected function getDeleteRoute()
    {
        return $this->getName() . '_delete';
    }

    /**
     * @param $object
     * @return bool
     */
    protected function getDeleteIsGranted($object)
    {
        return $this->getSecurity()->isGranted($this->getDeleteRole(), $object);
    }

    /**
     * @return string
     */
    protected function getDeleteRole()
    {
        return 'ROLE_' . strtoupper($this->getName()) . '_DELETE';
    }

    /**
     * @param object $object
     * @return void
     */
    protected function prePersist($object) {}

    /**
     * @param object $object
     * @return void
     */
    protected function postPersist($object) {}

    /**
     * @param object $object
     * @return void
     */
    protected function preUpdate($object) {}

    /**
     * @param object $object
     * @return void
     */
    protected function postUpdate($object) {}

    /**
     * @param object $object
     * @return void
     */
    protected function preRemove($object) {}

    /**
     * @param object $object
     * @return void
     */
    protected function postRemove($object) {}

    /**
     * @return string
     */
    abstract protected function getName();

    /**
     * @return string
     */
    abstract protected function getObjectClass();
}
