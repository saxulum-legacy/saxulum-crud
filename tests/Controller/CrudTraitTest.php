<?php

namespace Saxulum\Tests\Crud\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Saxulum\Tests\Crud\Model\Sample;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class CrudTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testList()
    {
        $request = new Request();
        $request->query->set('sample_list', array('title' => 't'));

        $controller = new SampleController(
            $this->getDoctrine(Sample::classname),
            $this->getPaginator('QueryBuilder', 1, 10, array()),
            $this->getFormFactory(
                'Saxulum\Tests\Crud\Form\SampleListType',
                array('title' => 't'),
                'query'
             ),
            $this->getDummyUrlGenerator(),
            $this->getSecurity('ROLE_SAMPLE_LIST'),
            $this->getTwig('@SaxulumCrud/Sample/list.html.twig', array(
                'request' => $request,
                'pagination' => $this->getPaginationMock(),
                'form' => null,
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
                'transPrefix' => 'sample'
            ))
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

        $sample = new Sample();

        $controller = new SampleController(
            $this->getDoctrine(Sample::classname),
            $this->getDummyPaginator(),
            $this->getFormFactory(
                'Saxulum\Tests\Crud\Form\SampleType',
                $sample,
                'request'
            ),
            $this->getDummyUrlGenerator(),
            $this->getSecurity('ROLE_SAMPLE_CREATE'),
            $this->getTwig('@SaxulumCrud/Sample/create.html.twig', array(
                'request' => $request,
                'object' => $sample,
                'form' => null,
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
                'transPrefix' => 'sample'
            ))
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

        $sample = new Sample();
        $sample->setTitle('title');

        $controller = new SampleController(
            $this->getDoctrine(Sample::classname),
            $this->getPaginator('QueryBuilder', 1, 10, array()),
            $this->getFormFactory(
                'Saxulum\Tests\Crud\Form\SampleType',
                $sample,
                'request'
            ),
            $this->getUrlGenerator(
                'sample_edit',
                array(
                    'id' => null,
                )
            ),
            $this->getSecurity('ROLE_SAMPLE_CREATE'),
            $this->getDummyTwig()
        );

        $response = $controller->crudCreateObject($request);

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
            ->will($this->returnCallback(function($givenClass) use($expectedClass) {
                $this->assertEquals($expectedClass, $givenClass);
                $objectManagerMock = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
                $objectManagerMock
                    ->expects($this->any())
                    ->method('getRepository')
                    ->will($this->returnCallback(function() {

                        $objectRepositoryMock = $this->getMock('Saxulum\Crud\Repository\QueryBuilderForFilterFormInterface');
                        $objectRepositoryMock
                            ->expects($this->any())
                            ->method('find')
                            ->will($this->returnCallback(function() {

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
                    }))
                ;
                $objectManagerMock
                    ->expects($this->any())
                    ->method('getClassMetadata')
                    ->will($this->returnCallback(function() {

                        $objectRepositoryMock = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
                        $objectRepositoryMock
                            ->expects($this->once())
                            ->method('getIdentifier')
                            ->willReturn(array('id'))
                        ;

                        return $objectRepositoryMock;
                    }));
                ;

                return $objectManagerMock;
            }))
        ;

        return $managerRegistyMock;
    }

    /**
     * @return PaginatorInterface
     */
    protected function getDummyPaginator()
    {
        return $this->getMock('Knp\Component\Pager\PaginatorInterface');
    }

    /**
     * @return PaginatorInterface
     */
    protected function getPaginator($expectedTarget, $expectedPage, $expectedLimit, $expectedOptions)
    {
        $mock = $this->getMock('Knp\Component\Pager\PaginatorInterface');

        $mock
            ->expects($this->any())
            ->method('paginate')
            ->will($this->returnCallback(function($givenTarget, $givenPage, $givenLimit, $givenOptions) use ($expectedTarget, $expectedPage, $expectedLimit, $expectedOptions) {
                $this->assertInstanceOf($expectedTarget, $givenTarget);
                $this->assertEquals($expectedPage, $givenPage);
                $this->assertEquals($expectedLimit, $givenLimit);
                $this->assertEquals($expectedOptions, $givenOptions);

                return $this->getPaginationMock();
            }));
        ;

        return $mock;
    }

    /**
     * @return PaginationInterface
     */
    protected function getPaginationMock()
    {
        $paginationMock = $this->getMock('Knp\Component\Pager\Pagination\PaginationInterface');

        return $paginationMock;
    }

    /**
     * @param FormTypeInterface $expectedType
     * @param mixed      $expectedData
     * @param string $requestProperty
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFormFactory($expectedType, $expectedData, $requestProperty)
    {
        $formFactoryMock = $this->getMock('Symfony\Component\Form\FormFactoryInterface');

        $formFactoryMock
            ->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(function(AbstractType $givenType, $givenData) use ($expectedType, $expectedData, $requestProperty){
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
                    ->will($this->returnCallback(function(Request $request) use($givenData, $expectedData, $requestProperty, $formName) {
                        /** @var array $requestData */
                        $requestData = $request->$requestProperty->get($formName);
                        $propertyAccessor = new PropertyAccessor();
                        $isObject = is_object($givenData);

                        foreach($requestData as $property => $value) {
                            if(!$isObject) {
                                $property = '[' . $property . ']';
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

                return $formMock;
            }))
        ;

        return $formFactoryMock;
    }

    /**
     * @return UrlGeneratorInterface
     */
    protected function getDummyUrlGenerator()
    {
        return $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
    }

    /**
     * @return UrlGeneratorInterface
     */
    protected function getUrlGenerator($expectedName, $expectedParameters)
    {
        $mock =  $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');

        $mock
            ->expects($this->any())
            ->method('generate')
            ->will($this->returnCallback(function($givenName, $givenParameters, $givenReferenceType) use ($expectedName, $expectedParameters){
                $this->assertEquals($expectedName, $givenName);
                $this->assertEquals($expectedParameters, $givenParameters);
                $this->assertEquals(UrlGeneratorInterface::ABSOLUTE_URL, $givenReferenceType);

                return 'http://test.lo';
            }))
        ;

        return $mock;
    }

    /**
     * @param string $expectedRole
     * @return SecurityContextInterface
     */
    protected function getSecurity($expectedRole)
    {
        $mock = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $mock
            ->expects($this->once())
            ->method('isGranted')
            ->will($this->returnCallback(function($givenRole) use($expectedRole) {
                return $givenRole === $expectedRole;
            }))
        ;

        return $mock;
    }

    protected function getDummyTwig()
    {
        return $this->getMock('\Twig_Environment');
    }

    /**
     * @param string      $expectedView
     * @param array $expectedParameters
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getTwig($expectedView, array $expectedParameters)
    {
        $mock = $this->getMock('\Twig_Environment');
        $mock
            ->expects($this->any())
            ->method('render')
            ->will($this->returnCallback(function($givenView, $givenParameters) use($expectedView, $expectedParameters){
                $this->assertEquals($expectedView, $givenView);
                $this->assertEquals($expectedParameters, $givenParameters);
                return 'renderedcontent';
            }))
        ;

        return $mock;
    }
}