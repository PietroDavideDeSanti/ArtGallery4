<?php

namespace CoreBundle\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Tools\SchemaTool;

class MyUpdateCommand extends \Doctrine\Bundle\DoctrineBundle\Command\Proxy\UpdateSchemaDoctrineCommand{//    \Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand{

    protected $name = 'orm:schema-tool:myupdate';

    protected function executeSchemaCommand(InputInterface $input, OutputInterface $output, SchemaTool $schemaTool, array $metadatas){
        $newMetadatas = array();
        foreach($metadatas as $metadata) {
            //verifico se sono presenti delle view e le salto
            if(strpos(strtolower($metadata->getName()), "view") === false){
                array_push($newMetadatas, $metadata);
            }
        }
        parent::executeSchemaCommand($input, $output, $schemaTool, $newMetadatas);
    }
}