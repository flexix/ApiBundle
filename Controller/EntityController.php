<?php

namespace Flexix\ApiBundle\Controller;

use Flexix\PrototypeControllerBundle\Controller\PrototypeController;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Entity controller.
 * 
 */
class EntityController extends PrototypeController {


    
       /**
     * @ApiDoc(
     *  description="Delete object",
     *  input="Your\Namespace\Form\Type\YourType",
     *  output="Your\Namespace\Class"
     * )
     */
    public function deleteAction(Request $request, $action, $module, $alias, $id) {

        $content = json_decode($request->getContent(), true);
        $driver = $this->getDriver($action, $module, $alias, $id);
        $this->isActionAllowed($driver, $request);
        $adapter = $this->getAdapter($driver);
        $adapter->setRequest($request);
        $entity = $this->invokeServiceMethod($driver, self::_GET, [$driver->getEntityClass(), $id, $adapter->getRequest()]);
        $adapter->setObject($entity);
        $this->checkEntityExists($driver, $adapter);
        $this->denyAccessUnlessGranted(self::_DELETE, $this->getSecurityTicket($driver, $adapter->getData()));

        
        $form = $this->container->get('form.factory')->createNamedBuilder(sprintf('%s_%s_delete',$module,$alias))
                ->setAction($this->getFormActionUrl($driver, ['id' => $adapter->getData()->getId()],$adapter))
                ->setMethod('DELETE')
                ->getForm();


        $form->submit($content);
        if ( $form->isValid()) {

            $result = $this->invokeServiceMethod($driver, self::_DELETE, [$entity,$adapter->getRequest()]);
            $view = $this->redirectView($this->getUrlToRedirect($driver, $adapter->getRedirectionData($result)), 301);
            return $this->handleView($view);
        }
        
        if(!$form->isValid())
        {
            $httpCode=400;
        }
        else
        {
            $httpCode=200;
        }

        $view = $this->view($adapter->getData(), $httpCode)
                ->setTemplateVar($this->getTemplateVar($driver))
                ->setTemplateData($adapter->getTemplateData([
                            'driver' => $driver,
                            'form' => $form->createView(),
                            'is_xml_http_request' => $adapter->getRequest()->isXmlHttpRequest(),
                            'is_sub_request' => (boolean) $this->requestStack->getParentRequest()
                ]))
                ->setTemplate($driver->getTemplate());
        return $this->handleView($view);
    }
    
    
    /**
     * @ApiDoc(
     *  description="List items",
     *  input="Your\Namespace\Form\Type\YourType",
     *  output="Your\Namespace\Class"
     * )
     */
    public function listAction(Request $request, $action, $module, $alias) {
//
//        $content = json_decode($request->getContent(), true);
        $driver = $this->getDriver($action, $module, $alias);
        $this->isActionAllowed($driver, $request);
        $entityClass = $driver->getEntityClass();
        $adapter = $this->getAdapter($driver);
        $adapter->setRequest($request);
        $entity = $this->createEntity($entityClass);
        $formTypeClass = $this->getFormTypeClass($driver, false);
        $form = null;

        if ($formTypeClass) {

            $form = $this->createForm($this->getFormTypeClass($driver), [], ['method' => $this->getFormMethod($driver), 'action' => $this->getFormActionUrl($driver,[],$adapter)]);
            $form->submit($adapter->getContent());
       
        }
  
        $result = $this->invokeServiceMethod($driver, self::_LIST, [$driver->getEntityClass(), $adapter->getRequest(), $form]);
        $adapter->setObject($result);
        $this->denyAccessUnlessGranted(self::_LIST, $this->getSecurityTicket($driver, $adapter->getData()));
        $view = $this->view($adapter->getData(), 200)
                ->setTemplateVar($this->getTemplateVar($driver))
                ->setTemplateData($adapter->getTemplateData([
                            'driver' => $driver,
                            'is_xml_http_request' => $adapter->getRequest()->isXmlHttpRequest(),
                            'is_sub_request' => (boolean) $this->requestStack->getParentRequest()
                ]))
                ->setTemplate($driver->getTemplate());

        return $this->handleView($view);
    }

  
    /**
     * @ApiDoc(
     *  description="Get object by Id",
     *  input="Your\Namespace\Form\Type\YourType",
     *  output="Your\Namespace\Class"
     * )
     */
    public function getAction(Request $request, $action, $module, $alias, $id) {


        $driver = $this->getDriver($action, $module, $alias, $id);
        $this->isActionAllowed($driver, $request);
        $adapter = $this->getAdapter($driver);
        $adapter->setRequest($request);
        $entity = $this->invokeServiceMethod($driver, self::_GET, [$driver->getEntityClass(), $id, $adapter->getRequest()]);
        $adapter->setObject($entity);
        
        $this->checkEntityExists($driver, $adapter);
        $this->denyAccessUnlessGranted(self::_GET, $this->getSecurityTicket($driver, $adapter->getData()));

        $view = $this->view($adapter->getData(), 200)
                ->setTemplateVar($this->getTemplateVar($driver))
                ->setTemplateData($adapter->getTemplateData([
                            'driver' => $driver,
                            'is_xml_http_request' => $adapter->getRequest()->isXmlHttpRequest(),
                            'is_sub_request' => (boolean) $this->requestStack->getParentRequest()
                ]))
                ->setTemplate($driver->getTemplate());

        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *  description="Edit Object",
     *  input="Your\Namespace\Form\Type\YourType",
     *  output="Your\Namespace\Class"
     * )
     */
    public function editAction(Request $request, $action, $module, $alias, $id) {

        //$content = json_decode($request->getContent(), true);
        $driver = $this->getDriver($action, $module, $alias, $id);
        $this->isActionAllowed($driver, $request);
        $adapter = $this->getAdapter($driver);
        $adapter->setRequest($request);
        $entity = $this->invokeServiceMethod($driver, self::_GET, [$driver->getEntityClass(), $id, $adapter->getRequest()]);
        $adapter->setObject($entity);
        $this->checkEntityExists($driver, $adapter);
        $this->denyAccessUnlessGranted(self::_UPDATE, $this->getSecurityTicket($driver, $adapter->getData()));

        $form = $this->createForm($this->getFormTypeClass($driver), $entity, ['method' => $this->getFormMethod($driver), 'action' => $this->getFormActionUrl($driver, ['id' => $adapter->getData()->getId()],$adapter)]);
        $form->submit($adapter->getContent());

        if ( $form->isValid()) {

            $result = $this->invokeServiceMethod($driver, self::_UPDATE, [$entity, $adapter->getRequest(), $form]);
        }
        
        if( !$form->isValid())
        {
            $httpCode=400;
        }
        else
        {
            $httpCode=200;
        }
        
        $view = $this->view($adapter->getData(), $httpCode)
                ->setTemplateVar($this->getTemplateVar($driver))
                ->setTemplateData($adapter->getTemplateData([
                            'form' => $form->createView(),
                            'driver' => $driver,
                            'is_xml_http_request' => $adapter->getRequest()->isXmlHttpRequest(),
                            'is_sub_request' => (boolean) $this->requestStack->getParentRequest()
                ]))
                ->setTemplate($driver->getTemplate());

        return $this->handleView($view);
    }
    
    
    /**
     * @ApiDoc(
     *  description="Create a new Object",
     *  input="Your\Namespace\Form\Type\YourType",
     *  output="Your\Namespace\Class"
     * )
     */
    public function newAction(Request $request, $action, $module, $alias) {

       // $content = json_decode($request->getContent(), true);
        $driver = $this->getDriver($action, $module, $alias);
        $this->isActionAllowed($driver, $request);
        $entityClass = $driver->getEntityClass();
        $adapter = $this->getAdapter($driver);
        $entity = $this->createEntity($entityClass);
        $adapter->setObject($entity);
        $adapter->setRequest($request);
        $this->denyAccessUnlessGranted(self::_NEW, $this->getSecurityTicket($driver, $adapter->getData()));
        $form = $this->createForm($this->getFormTypeClass($driver), $entity, ['method' => $this->getFormMethod($driver), 'action' => $this->getFormActionUrl($driver,[],$adapter)]);
        $form->submit($adapter->getContent());

        if ($form->isValid()) {

            $result = $this->invokeServiceMethod($driver, self::_CREATE, [$entity, $adapter->getRequest(), $form]);
            
        }
         
        if($form->isSubmitted() && !$form->isValid())
        {
            $httpCode=400;
        }
        else
        {
            $httpCode=200;
        }

        $view = $this->view($adapter->getData(), $httpCode)
                ->setTemplateVar($this->getTemplateVar($driver))
                ->setTemplateData($adapter->getTemplateData([
                            'form' => $form->createView(),
                            'driver' => $driver,
                            'is_xml_http_request' => $adapter->getRequest()->isXmlHttpRequest(),
                            'is_sub_request' => (boolean) $this->requestStack->getParentRequest()
                ]))
                ->setTemplate($driver->getTemplate());

        return $this->handleView($view);
    }


 

}
