<?php

namespace CoreBundle\Services;
//libraries
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Parameters
{
    
    public function getUriParams(Request $request) {
        return $request->get('_route_params');
    }
    
    public function getHeaderParams(Request $request) {
        return apache_request_headers();
        //return $request->headers->all();
    }    
    
    public function getQuerystringParams(Request $request) {
        return $request->query->all();
    } 
    
    public function getPostParams(Request $request) {
        return $request->request->all();
    }     
    
    public function getBodyParams(Request $request) {
        return $request->getContent();
    }    
    
    public function getFileParams(Request $request) {
        return $request->files->all();
    }

    /**
     * @param Request $request
     * @return SessionInterface
     */
    public function getSessionParams(Request $request) {
        return $request->getSession();
    }
            
}
