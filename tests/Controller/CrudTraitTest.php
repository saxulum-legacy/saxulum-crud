<?php

namespace Saxulum\Tests\Crud\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Knp\Component\Pager\PaginatorInterface;
use Saxulum\Crud\Listing\ListingFactory;
use Saxulum\Crud\Listing\Type\ArrayType;
use Saxulum\Crud\Listing\Type\FloatType;
use Saxulum\Crud\Listing\Type\IntegerType;
use Saxulum\Crud\Listing\Type\StringType;
use Saxulum\Crud\Repository\QueryBuilderForFilterFormInterface;
use Saxulum\Tests\Crud\Data\Controller\SampleController;
use Saxulum\Tests\Crud\Data\Model\Sample;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class CrudTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testList()
    {
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));
        $request->query->set('sample_list', array('title' => 't'));

        $listFactory = $this->getListingFactory();

        $controller = new SampleController(
            $this->getAuthorizationChecker('ROLE_SAMPLE_LIST'),
            $this->getDoctrine(Sample::classname),
            $this->getFormFactory(
                'Saxulum\Tests\Crud\Data\Form\SampleListType',
                array('title' => 't'),
                'query'
            ),
            $this->getPaginator('QueryBuilder', 1, 10, array()),
            null,
            $this->getTwig('@SaxulumCrud/Sample/list.html.twig', array(
                'request' => $request,
                'pagination' => $this->getPagination(),
                'form' => $this->getFormView(),
                'listing' => $listFactory->create('Saxulum\Tests\Crud\Data\Model\Sample')->add('id', 'integer')->add('title'),
                'listRoute' => 'sample_list',
                'createRoute' => 'sample_create',
                'editRoute' => 'sample_edit',
                'viewRoute' => 'sample_view',
                'deleteRoute' => 'sample_delete',
                'listRole' => 'ROLE_SAMPLE_LIST',
                'createRole' => 'ROLE_SAMPLE_CREATE',
                'editRole' => 'ROLE_SAMPLE_EDIT',
                'viewRole' => 'ROLE_SAMPLE_VIEW',
                'deleteRole' => 'ROLE_SAMPLE_DELETE',
                'identifier' => 'id',
                'transPrefix' => 'sample',
                'transDomain' => 'messages',
                'objectClass' => 'Saxulum\Tests\Crud\Data\Model\Sample',
            )),
            $listFactory
        );

        $response = $controller->crudListObjects($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('renderedcontent', $response->getContent());
    }

    public function testCreateGet()
    {
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $model = new Sample();

        $controller = new SampleController(
            $this->getAuthorizationChecker('ROLE_SAMPLE_CREATE'),
            $this->getDoctrine(Sample::classname),
            $this->getFormFactory(
                'Saxulum\Tests\Crud\Data\Form\SampleType',
                $model
            ),
            null,
            null,
            $this->getTwig('@SaxulumCrud/Sample/create.html.twig', array(
                'request' => $request,
                'object' => $model,
                'form' => $this->getFormView(),
                'listRoute' => 'sample_list',
                'createRoute' => 'sample_create',
                'editRoute' => 'sample_edit',
                'viewRoute' => 'sample_view',
                'deleteRoute' => 'sample_delete',
                'listRole' => 'ROLE_SAMPLE_LIST',
                'createRole' => 'ROLE_SAMPLE_CREATE',
                'editRole' => 'ROLE_SAMPLE_EDIT',
                'viewRole' => 'ROLE_SAMPLE_VIEW',
                'deleteRole' => 'ROLE_SAMPLE_DELETE',
                'identifier' => 'id',
                'transPrefix' => 'sample',
                'transDomain' => 'messages',
                'objectClass' => 'Saxulum\Tests\Crud\Data\Model\Sample',
            )),
            null
        );

        $response = $controller->crudCreateObject($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('renderedcontent', $response->getContent());
    }

    public function testCreatePost()
    {
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));
        $request->setMethod('POST');
        $request->request->set('sample_edit', array('title' => 'title'));

        $model = new Sample();
        $model->setTitle('title');

        $controller = new SampleController(
            $this->getAuthorizationChecker('ROLE_SAMPLE_CREATE'),
            $this->getDoctrine(Sample::classname),
            $this->getFormFactory(
                'Saxulum\Tests\Crud\Data\Form\SampleType',
                $model,
                'request'
            ),
            null,
            $this->getUrlGenerator(
                'sample_edit',
                array(
                    'id' => 1,
                )
            ),
            null,
            null
        );

        $response = $controller->crudCreateObject($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('http://test.lo', $response->headers->get('location'));
    }

    public function testEditGet()
    {
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $model = new Sample();
        $this->setModelId($model, 1);

        $controller = new SampleController(
            $this->getAuthorizationChecker('ROLE_SAMPLE_EDIT'),
            $this->getDoctrine(Sample::classname),
            $this->getFormFactory(
                'Saxulum\Tests\Crud\Data\Form\SampleType',
                $model
            ),
            null,
            null,
            $this->getTwig('@SaxulumCrud/Sample/edit.html.twig', array(
                'request' => $request,
                'object' => $model,
                'form' => $this->getFormView(),
                'listRoute' => 'sample_list',
                'createRoute' => 'sample_create',
                'editRoute' => 'sample_edit',
                'viewRoute' => 'sample_view',
                'deleteRoute' => 'sample_delete',
                'listRole' => 'ROLE_SAMPLE_LIST',
                'createRole' => 'ROLE_SAMPLE_CREATE',
                'editRole' => 'ROLE_SAMPLE_EDIT',
                'viewRole' => 'ROLE_SAMPLE_VIEW',
                'deleteRole' => 'ROLE_SAMPLE_DELETE',
                'identifier' => 'id',
                'transPrefix' => 'sample',
                'transDomain' => 'messages',
                'objectClass' => 'Saxulum\Tests\Crud\Data\Model\Sample',
            )),
            null
        );

        $response = $controller->crudEditObject($request, 1);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('renderedcontent', $response->getContent());
    }

    public function testEditPost()
    {
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));
        $request->setMethod('POST');
        $request->request->set('sample_edit', array('title' => 'title'));

        $model = new Sample();
        $model->setTitle('title');
        $this->setModelId($model, 1);

        $controller = new SampleController(
            $this->getAuthorizationChecker('ROLE_SAMPLE_EDIT'),
            $this->getDoctrine(Sample::classname),
            $this->getFormFactory(
                'Saxulum\Tests\Crud\Data\Form\SampleType',
                $model,
                'request'
            ),
            null,
            $this->getUrlGenerator(
                'sample_edit',
                array(
                    'id' => 1,
                )
            ),
            null,
            null
        );

        $response = $controller->crudEditObject($request, 1);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('http://test.lo', $response->headers->get('location'));
    }

    public function testView()
    {
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $model = new Sample();
        $this->setModelId($model, 1);

        $controller = new SampleController(
            $this->getAuthorizationChecker('ROLE_SAMPLE_VIEW'),
            $this->getDoctrine(Sample::classname),
            null,
            null,
            null,
            $this->getTwig('@SaxulumCrud/Sample/view.html.twig', array(
                'request' => $request,
                'object' => $model,
                'listRoute' => 'sample_list',
                'createRoute' => 'sample_create',
                'editRoute' => 'sample_edit',
                'viewRoute' => 'sample_view',
                'deleteRoute' => 'sample_delete',
                'listRole' => 'ROLE_SAMPLE_LIST',
                'createRole' => 'ROLE_SAMPLE_CREATE',
                'editRole' => 'ROLE_SAMPLE_EDIT',
                'viewRole' => 'ROLE_SAMPLE_VIEW',
                'deleteRole' => 'ROLE_SAMPLE_DELETE',
                'identifier' => 'id',
                'transPrefix' => 'sample',
                'transDomain' => 'messages',
                'objectClass' => 'Saxulum\Tests\Crud\Data\Model\Sample',
            )),
            null
        );

        $response = $controller->crudViewObject($request, 1);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('renderedcontent', $response->getContent());
    }

    public function testDelete()
    {
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $model = new Sample();
        $this->setModelId($model, 1);

        $controller = new SampleController(
            $this->getAuthorizationChecker('ROLE_SAMPLE_DELETE'),
            $this->getDoctrine(Sample::classname),
            null,
            null,
            $this->getUrlGenerator('sample_list'),
            null,
            null
        );

        $response = $controller->crudDeleteObject($request, 1);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('http://test.lo', $response->headers->get('location'));
    }

    /**
     * @return ManagerRegistry
     */
    protected function getDoctrine($expectedClass)
    {
        $managerRegistyMock = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $managerRegistyMock
            ->expects($this->any())
            ->method('getManagerForClass')
            ->will($this->returnCallback(function ($givenClass) use ($expectedClass) {
                $this->assertEquals($expectedClass, $givenClass);
                $objectManagerMock = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
                $objectManagerMock
                    ->expects($this->any())
                    ->method('getRepository')
                    ->will($this->returnCallback(function () {
                        return $this->getRepository();
                    }))
                ;
                $objectManagerMock
                    ->expects($this->any())
                    ->method('persist')
                    ->will($this->returnCallback(function (Sample $model) {
                        $this->setModelId($model, 1);
                    }));
                $objectManagerMock
                    ->expects($this->any())
                    ->method('getClassMetadata')
                    ->will($this->returnCallback(function () {
                        return $this->getClassMetadata();
                    }));

                return $objectManagerMock;
            }))
        ;

        return $managerRegistyMock;
    }

    /**
     * @return QueryBuilderForFilterFormInterface
     */
    protected function getRepository()
    {
        $objectRepositoryMock = $this->getMock('Saxulum\Crud\Repository\QueryBuilderForFilterFormInterface');
        $objectRepositoryMock
            ->expects($this->any())
            ->method('find')
            ->will($this->returnCallback(function () {

                $reflectionClass = new \ReflectionClass(Sample::classname);
                $model = $reflectionClass->newInstanceWithoutConstructor();

                $reflectionProperty = $reflectionClass->getProperty('id');
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($model, 1);
                $reflectionProperty->setAccessible(false);

                return $model;
            }))
        ;
        $objectRepositoryMock
            ->expects($this->any())
            ->method('getQueryBuilderForFilterForm')
            ->willReturn($this->getMock('QueryBuilder'))
        ;

        return $objectRepositoryMock;
    }

    /**
     * @return ClassMetadata
     */
    protected function getClassMetadata()
    {
        $objectRepositoryMock = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $objectRepositoryMock
            ->expects($this->once())
            ->method('getIdentifier')
            ->willReturn(array('id'))
        ;

        return $objectRepositoryMock;
    }

    /**
     * @return PaginatorInterface
     */
    protected function getPaginator($expectedTarget, $expectedPage, $expectedLimit, $expectedOptions)
    {
        $mock = $this->getMock('Saxulum\Crud\Pagination\PaginatorInterface');

        $mock
            ->expects($this->any())
            ->method('paginate')
            ->will($this->returnCallback(function ($givenTarget, $givenPage, $givenLimit, $givenOptions) use ($expectedTarget, $expectedPage, $expectedLimit, $expectedOptions) {
                $this->assertInstanceOf($expectedTarget, $givenTarget);
                $this->assertEquals($expectedPage, $givenPage);
                $this->assertEquals($expectedLimit, $givenLimit);
                $this->assertEquals($expectedOptions, $givenOptions);

                return $this->getPagination();
            }));

        return $mock;
    }

    /**
     * @return \stdClass
     */
    protected function getPagination()
    {
        return new \stdClass();
    }

    /**
     * @param  FormTypeInterface    $expectedType
     * @param  mixed                $expectedData
     * @param  string               $requestProperty
     * @return FormFactoryInterface
     */
    protected function getFormFactory($expectedType, $expectedData, $requestProperty = null)
    {
        $formFactoryMock = $this->getMock('Symfony\Component\Form\FormFactoryInterface');

        $formFactoryMock
            ->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(function (AbstractType $givenType, $givenData) use ($expectedType, $expectedData, $requestProperty) {
                $this->assertInstanceOf($expectedType, $givenType);

                $formName = $givenType->getName();

                $formMock = $this->getMock('Symfony\Component\Form\FormInterface');
                $formMock
                    ->expects($this->any())
                    ->method('getData')
                    ->willReturn($givenData)
                ;

                $formMock
                    ->expects($this->any())
                    ->method('handleRequest')
                    ->will($this->returnCallback(function (Request $request) use ($givenData, $expectedData, $requestProperty, $formName) {

                        if (null === $requestProperty) {
                            return $givenData;
                        }

                        /** @var array $requestData */
                        $requestData = $request->$requestProperty->get($formName);
                        $propertyAccessor = new PropertyAccessor();
                        $isObject = is_object($givenData);

                        foreach ($requestData as $property => $value) {
                            if (!$isObject) {
                                $property = '['.$property.']';
                            }
                            $propertyAccessor->setValue($givenData, $property, $value);
                        }

                        return $givenData;
                    }))
                ;

                $formMock
                    ->expects($this->any())
                    ->method('isValid')
                    ->willReturn(true)
                ;

                $formMock
                    ->expects($this->any())
                    ->method('createView')
                    ->willReturn($this->getFormView())
                ;

                return $formMock;
            }))
        ;

        return $formFactoryMock;
    }

    /**
     * @return FormView
     */
    protected function getFormView()
    {
        return $this->getMock('Symfony\Component\Form\FormView');
    }

    /**
     * @param  string                $expectedName
     * @param  array                 $expectedParameters
     * @return UrlGeneratorInterface
     */
    protected function getUrlGenerator($expectedName, array $expectedParameters = array())
    {
        $mock =  $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');

        $mock
            ->expects($this->any())
            ->method('generate')
            ->will($this->returnCallback(function ($givenName, $givenParameters, $givenReferenceType) use ($expectedName, $expectedParameters) {
                $this->assertEquals($expectedName, $givenName);
                $this->assertEquals($expectedParameters, $givenParameters);
                $this->assertEquals(UrlGeneratorInterface::ABSOLUTE_URL, $givenReferenceType);

                return 'http://test.lo';
            }))
        ;

        return $mock;
    }

    /**
     * @param  string                   $expectedRole
     * @return AuthorizationCheckerInterface
     */
    protected function getAuthorizationChecker($expectedRole)
    {
        $mock = $this->getMock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $mock
            ->expects($this->once())
            ->method('isGranted')
            ->will($this->returnCallback(function ($givenRole) use ($expectedRole) {
                return $givenRole === $expectedRole;
            }))
        ;

        return $mock;
    }

    /**
     * @param  string            $expectedView
     * @param  array             $expectedParameters
     * @return \Twig_Environment
     */
    protected function getTwig($expectedView, array $expectedParameters)
    {
        $mock = $this->getMock('\Twig_Environment');
        $mock
            ->expects($this->any())
            ->method('render')
            ->will($this->returnCallback(function ($givenView, $givenParameters) use ($expectedView, $expectedParameters) {
                $this->assertEquals($expectedView, $givenView);
                $this->assertEquals($expectedParameters, $givenParameters);

                return 'renderedcontent';
            }))
        ;

        return $mock;
    }

    /**
     * @return ListingFactory
     */
    protected function getListingFactory()
    {
        return new ListingFactory(
            array(
                new ArrayType(),
                new FloatType(),
                new IntegerType(),
                new StringType()
            )
        );
    }

    /**
     * @param Sample $model
     * @param int    $id
     */
    protected function setModelId(Sample $model, $id)
    {
        $reflectionClass = new \ReflectionClass(Sample::classname);

        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($model, $id);
        $reflectionProperty->setAccessible(false);
    }
}
