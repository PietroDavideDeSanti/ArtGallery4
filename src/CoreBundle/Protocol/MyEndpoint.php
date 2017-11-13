<?php

namespace CoreBundle\Protocol;


class MyEndpoint
{
    private $srcDir;
    private $bundle;
    private $controllerName;
    private $functionName;
    private $endpointName;
    private $frontController;
    private $return;
    private $route;
    private $method;
    private $description;
    private $variables;
    private $check400;
    private $context = array();

    /**
     * @return mixed
     */
    public function getCheck400()
    {
        return $this->check400;
    }

    /**
     * @param mixed $check400
     */
    public function setCheck400($check400)
    {
        $this->check400 = $check400;
    }

    /**
     * @return mixed
     */
    public function getSrcDir()
    {
        return $this->srcDir;
    }

    /**
     * @param mixed $srcDir
     */
    public function setSrcDir($srcDir)
    {
        $this->srcDir = $srcDir;
    }

    /**
     * @return mixed
     */
    public function getBundle()
    {
        return $this->bundle;
    }

    /**
     * @param mixed $bundle
     */
    public function setBundle($bundle)
    {
        $this->bundle = $bundle;
    }

    /**
     * @return mixed
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * @param mixed $controllerName
     */
    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
    }

    /**
     * @return mixed
     */
    public function getFunctionName()
    {
        return $this->functionName;
    }

    /**
     * @param mixed $functionName
     */
    public function setFunctionName($functionName)
    {
        $this->functionName = $functionName;
    }

    /**
     * @return mixed
     */
    public function getEndpointName()
    {
        return $this->endpointName;
    }

    /**
     * @param mixed $endpointName
     */
    public function setEndpointName($endpointName)
    {
        $this->endpointName = $endpointName;
    }

    /**
     * @return mixed
     */
    public function getFrontController()
    {
        return $this->frontController;
    }

    /**
     * @param mixed $frontController
     */
    public function setFrontController($frontController)
    {
        $this->frontController = $frontController;
    }

    /**
     * @return mixed
     */
    public function getReturn()
    {
        return $this->return;
    }

    /**
     * @param mixed $return
     */
    public function setReturn($return)
    {
        $this->return = $return;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param mixed $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param mixed $variables
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param array $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    public function addContext($context){
        $this->context[] = $context;
    }

}

