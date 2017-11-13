<?php
/**
 * Created by PhpStorm.
 * User: a.salvati
 * Date: 23/06/2017
 * Time: 15:00
 */

namespace CoreBundle\Services;

// Serializzatore e Deserializzatore
use JMS\Serializer\SerializationContext;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use JMS\Serializer\Serializer as jmsSerializer;

class MySerializer
{

    /**
     * @var Serializer
     */
    private $serializer;
    private $deserializer;

    public function __construct(jmsSerializer $serializer){
        $this->serializer = $serializer;
        $this->deserializer = new Serializer(array(new GetSetMethodNormalizer()), array(new JsonEncoder()));
    }

    public function setJsonResponse($data, $rule=null) {
        // Adeguo i data
        $context = new SerializationContext();

        $context->setSerializeNull(true);
        if(!empty($rule)) {
            $context->setGroups($rule);
        }
        $response = $this->serializer->serialize($data, 'json', $context);
        if(empty($data)) {
            if(is_array($data)){
                return array();
            } else {
                return new \stdClass();
            }
        } else if(is_object($data)){
            return json_decode($response);
        } else {
            return json_decode($response, TRUE);
        }
    }

    public function deserialize($jsonstring, $classname, $format) {
        return $this->deserializer->deserialize($jsonstring, $classname, $format);
    }

}