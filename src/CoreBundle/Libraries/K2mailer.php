<?php

namespace CoreBundle\Libraries;

class K2mailer {
    
    public static $headers = array();
    
    public static function setSwitfMessage($title, $from, $to, $body, $attachment = "", $format = "text/html") {
        $message = \Swift_Message::newInstance()
                ->setSubject($title)
                ->setFrom($from)
                ->setTo($to)
                ->setBody($body, $format);
        if(!empty($attachment)){
            $message->attach(\Swift_Attachment::fromPath($attachment));
        }
        
        $messageHeaders = $message->getHeaders();
        foreach (K2mailer::$headers as $name => $value) {
            $messageHeaders->addTextHeader($name, $value);
        }
        
        return $message;
    }
    
    public static function setHeaders($headers) {
        K2mailer::$headers = $headers;
    }
    
}

