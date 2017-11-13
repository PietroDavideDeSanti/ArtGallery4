<?php

namespace CoreBundle\Command;

use Sensio\Bundle\GeneratorBundle\Generator\DoctrineEntityGenerator;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Console\Question\Question;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Doctrine\DBAL\Types\Type;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;

/*Necessario per inserire i vari Input/Output tra i parametri di ingresso del comando*/
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/* Servizio MyEntityEditor*/
use CoreBundle\Services\MyEntityEditor;


class GenerateEntityCommand extends GenerateDoctrineCommand
{
    protected function configure()
    {
        $this
            ->setName('k2:generate:entity')
            ->setDescription('Generazione di entity')
            ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'Qual e\' il nome della Entity?')
            ->addOption('fields', null, InputOption::VALUE_REQUIRED, 'Quali sono le sue proprieta?');
    }

    protected function interact(InputInterface $input, OutputInterface $output){

        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        /* - 1. inizialize()                                                               */
        /* - 2. INTERACT()                                                                 */
        /* - 3. execute()                                                                  */
        /*                                                                                 */
        /* Its purpose is to check if some of the options/arguments are missing and        */
        /* interactively ask the user for those values. This is the last place where       */
        /* you can ask for missing options/arguments. After this command, missing          */
        /* options/arguments will result in an error.                                      */
        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Benvenuto nel generatore di Entity K2:');

        // namespace
        $output->writeln(array(
            'Inserire il nome della Entity',
            'seguendo questa notazione: <comment>nomeBundle:Entity</comment>.',
            '',
        ));

        $bundleNames = array_keys($this->getContainer()->get('kernel')->getBundles());

        while (true) {
            $question = new Question($questionHelper->getQuestion('Nome shortcut della Entity', $input->getOption('entity')), $input->getOption('entity'));
            $question->setValidator(array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateEntityName'));
            $question->setAutocompleterValues($bundleNames);
            $entity = $questionHelper->ask($input, $output, $question);

            list($bundle, $entity) = $this->parseShortcutNotation($entity);

            // check reserved words
            if ($this->getGenerator()->isReservedKeyword($entity)) {
                $output->writeln(sprintf('<bg=red> "%s" : parola riservata</>.', $entity));
                continue;
            }

            try {
                $b = $this->getContainer()->get('kernel')->getBundle($bundle);

                if (!file_exists($b->getPath().'/Entity/'.str_replace('\\', '/', $entity).'.php')) {
                    break;
                }

                $output->writeln(sprintf('<bg=red>La Entity "%s:%s" esiste gia</>.', $bundle, $entity));
            } catch (\Exception $e) {
                $output->writeln(sprintf('<bg=red>Il Bundle "%s" non esiste.</>', $bundle));
            }
        }
        $input->setOption('entity', $bundle.':'.$entity);

        /* *     C h i a m a t a      a      A d d  F i e l d s        * * * * * * * * */
        $input->setOption('fields', $this->addFields($input, $output, $questionHelper));
        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    }

    /**
     * @throws \InvalidArgumentException When the bundle doesn't end with Bundle (Example: "Bundle/MySampleBundle")
     */
    protected function execute(InputInterface $input, OutputInterface $output){

        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        /* - 1. inizialize()                                                           */
        /* - 2. interact()                                                             */
        /* - 3. EXECUTE()                                                              */
        /*                                                                             */
        /* This method is executed after interact() and initialize(). It contains      */
        /* the logic you want the command to execute.                                  */
        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

        $entity = Validators::validateEntityName($input->getOption('entity'));
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        // Chiamata a parseFields per recuperare i fields:
        $fields = $this->parseFields($input->getOption('fields'));

        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        /* *     Q u i    s i    g e n e r a    l a     E n t i t y      * * * * * * * */
        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

        $fields = array_values($fields);

        $normalfield = array();
        $onetomany = array();
        $manytoone = array();
        $manytomany = array();

        foreach($fields as $field){
            if(array_key_exists('join', $field)){
                if($field["relation"]=='M'){
                    $field["inversedBy"] = strtolower($entity);
                    $manytoone[] = $field;
                } else if($field["relation"]=='O'){
                    $field["mappedBy"] = lcfirst($entity)."Id";
                    $onetomany[] = $field;
                } else if($field["relation"]=='MM'){
                    $field["inversedBy"] = strtolower($entity);
                    $manytomany[] = $field;
                }
            } else {
                $normalfield[] = $field;
            }
        }

        $tableName = $this->getContainer()->get('myEntityEditor')->camelCaseToUnderscore($entity);

        $baseDir = $this->getContainer()->get('kernel')->getRootDir();

        $this->getContainer()->get('myEntityEditor')->generateEntity($baseDir, $bundle, $entity, $tableName, $normalfield, $manytoone, $onetomany, $manytomany);

        $this->getContainer()->get('myEntityEditor')->generateRepository($baseDir, $bundle, $entity, $manytoone);


        echo "\n\n";
        echo "/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */\n";
        echo "/* * * * *        Entity creata e rigenerata         * * * * * */\n";
        echo "/* * * * * * *        Repository creato              * * * * * */\n";
        echo "/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */\n";


    }

    private function parseFields($input){

        if (is_array($input)) {
            return $input;
        }

        $fields = array();
        foreach (preg_split('{(?:\([^\(]*\))(*SKIP)(*F)|\s+}', $input) as $value) {
            $elements = explode(':', $value);
            $name = $elements[0];
            $fieldAttributes = array();
            if (strlen($name)) {
                $fieldAttributes['fieldName'] = $name;
                $type = isset($elements[1]) ? $elements[1] : 'string';
                preg_match_all('{(.*)\((.*)\)}', $type, $matches);
                $fieldAttributes['type'] = isset($matches[1][0]) ? $matches[1][0] : $type;
                $length = null;
                if ('string' === $fieldAttributes['type']) {
                    $fieldAttributes['length'] = $length;
                }
                if (isset($matches[2][0]) && $length = $matches[2][0]) {
                    $attributesFound = array();
                    if (false !== strpos($length, '=')) {
                        preg_match_all('{([^,= ]+)=([^,= ]+)}', $length, $result);
                        $attributesFound = array_combine($result[1], $result[2]);
                    } else {
                        $fieldAttributes['length'] = $length;
                    }
                    $fieldAttributes = array_merge($fieldAttributes, $attributesFound);
                    foreach (array('length', 'precision', 'scale') as $intAttribute) {
                        if (isset($fieldAttributes[$intAttribute])) {
                            $fieldAttributes[$intAttribute] = (int) $fieldAttributes[$intAttribute];
                        }
                    }
                    foreach (array('nullable', 'unique') as $boolAttribute) {
                        if (isset($fieldAttributes[$boolAttribute])) {
                            $fieldAttributes[$boolAttribute] = filter_var($fieldAttributes[$boolAttribute], FILTER_VALIDATE_BOOLEAN);
                        }
                    }
                }

                $fields[$name] = $fieldAttributes;
            }
        }

        return $fields;
    }

    private function addFields(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper){

        $fields = $this->parseFields($input->getOption('fields'));

        $output->writeln(array(
            '',
            'Aggiungi i campi della Entity.',
            'Nota bene: la primary key (named <comment>id</comment>) viene aggiunta automaticamente.',
            '',
        ));
        $output->write('<info>Tipi disponibili:</info> ');

        $types = array_keys(Type::getTypesMap());
        $count = 20;
        foreach ($types as $i => $type) {
            if ($count > 50) {
                $count = 0;
                $output->writeln('');
            }
            $count += strlen($type);
            $output->write(sprintf('<comment>%s</comment>', $type));
            if (count($types) != $i + 1) {
                $output->write(', ');
            } else {
                $output->write('.');
            }
        }
        $output->writeln('');

        $fieldValidator = function ($type) use ($types) {
            if (!in_array($type, $types)) {
                throw new \InvalidArgumentException(sprintf('Tipo di dato non valido "%s".', $type));
            }

            return $type;
        };

        $lengthValidator = function ($length) {
            if (!$length) {
                return $length;
            }

            $result = filter_var($length, FILTER_VALIDATE_INT, array(
                'options' => array('min_range' => 1),
            ));

            if (false === $result) {
                throw new \InvalidArgumentException(sprintf('Lunghezza non valida: "%s".', $length));
            }

            return $length;
        };

        $boolValidator = function ($value) {
            if (null === $valueAsBool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
                throw new \InvalidArgumentException(sprintf('Valore booleano non valido: "%s".', $value));
            }

            return $valueAsBool;
        };

        $precisionValidator = function ($precision) {
            if (!$precision) {
                return $precision;
            }

            $result = filter_var($precision, FILTER_VALIDATE_INT, array(
                'options' => array('min_range' => 1, 'max_range' => 65),
            ));

            if (false === $result) {
                throw new \InvalidArgumentException(sprintf('Precisione non valida: "%s".', $precision));
            }

            return $precision;
        };

        $scaleValidator = function ($scale) {
            if (!$scale) {
                return $scale;
            }

            $result = filter_var($scale, FILTER_VALIDATE_INT, array(
                'options' => array('min_range' => 0, 'max_range' => 30),
            ));

            if (false === $result) {
                throw new \InvalidArgumentException(sprintf('Scala non valida: "%s".', $scale));
            }

            return $scale;
        };

        while (true) {
            $output->writeln('');
            $generator = $this->getGenerator();
            $question = new Question($questionHelper->getQuestion('Nuovo field_name per la Entity: (premi <invio> per terminare il processo di creazione)', null), null);
            $question->setValidator(function ($name) use ($fields, $generator) {
                if (isset($fields[$name]) || 'id' == $name) {
                    throw new \InvalidArgumentException(sprintf('Il field_name "%s" esiste gia.', $name));
                }

                // check reserved words
                if ($generator->isReservedKeyword($name)) {
                    throw new \InvalidArgumentException(sprintf('Il field_name "%s" e una parola riservata.', $name));
                }

                // check for valid PHP variable name
                if (!is_null($name) && !$generator->isValidPhpVariableName($name)) {
                    throw new \InvalidArgumentException(sprintf('"%s" non e un nome di variabile valido per il PHP.', $name));
                }

                return $name;
            });

            // Chiedo il column_name
            $columnName = $questionHelper->ask($input, $output, $question);
            if (!$columnName) {
                break;
            }

            // Imposto di default "type" integer
            $data = array('columnName' => $columnName, 'fieldName' => lcfirst(Container::camelize($columnName)), 'type' => 'integer');

            $question = new Question($questionHelper->getQuestion('Questa property ha una relazione?', 'false'), false);
            $question->setValidator($boolValidator);
            $question->setAutocompleterValues(array('true', 'false'));
            if ($join = $questionHelper->ask($input, $output, $question)) {
                $data['join'] = $join;
            }

            if (array_key_exists('join', $data)) {
                $question = new Question($questionHelper->getQuestion('Relazione OneToMany, ManyToOne oppure ManyToMany?', 'O/M/MM'), 'O');
                $question->setAutocompleterValues(array('O', 'M'));
                $data['relation'] = $questionHelper->ask($input, $output, $question);

                $question = new Question($questionHelper->getQuestion('Qual e\' il nome della Entity da mettere in realzione con questa?', 'NomeDellaEntity'), 'Entity');
                $question->setAutocompleterValues(array());
                $data['targetEntity'] = $questionHelper->ask($input, $output, $question);

                $question = new Question($questionHelper->getQuestion('Si trova nello stesso bundle?', 'true'), true);
                $question->setValidator($boolValidator);
                $question->setAutocompleterValues(array('true', 'false'));
                $sameBundle = $questionHelper->ask($input, $output, $question);

                if(!$sameBundle){
                    // Se la entity non si trova nello stesso Bundle:
                    $question = new Question($questionHelper->getQuestion('Indicare il namespace della Entity in relazione:', 'fooBundle\Entity\FooBar'), "fooBundle\Entity\FooBar");
                    $question->setAutocompleterValues(array());
                    $data['namespace'] = $questionHelper->ask($input, $output, $question);

                }

            } else {

                $defaultType = 'string';

                // Prova ad indovinare il tipo dal prefisso/suffisso del suo nome
                if (substr($columnName, -3) == '_at') {
                    $defaultType = 'datetime';
                } elseif (substr($columnName, -3) == '_id') {
                    $defaultType = 'integer';
                } elseif (substr($columnName, 0, 3) == 'is_') {
                    $defaultType = 'boolean';
                } elseif (substr($columnName, 0, 4) == 'has_') {
                    $defaultType = 'boolean';
                }

                $question = new Question($questionHelper->getQuestion('Tipo di field_name', $defaultType), $defaultType);
                $question->setValidator($fieldValidator);
                $question->setAutocompleterValues($types);
                $type = $questionHelper->ask($input, $output, $question);

                $data = array('columnName' => $columnName, 'fieldName' => lcfirst(Container::camelize($columnName)), 'type' => $type);

                if ($type == 'string') {
                    $question = new Question($questionHelper->getQuestion('Lunghezza', 255), 255);
                    $question->setValidator($lengthValidator);
                    $data['length'] = $questionHelper->ask($input, $output, $question);
                } elseif ('decimal' === $type) {
                    // 10 is the default value given in \Doctrine\DBAL\Schema\Column::$_precision
                    $question = new Question($questionHelper->getQuestion('Precisione', 10), 10);
                    $question->setValidator($precisionValidator);
                    $data['precision'] = $questionHelper->ask($input, $output, $question);

                    // 0 is the default value given in \Doctrine\DBAL\Schema\Column::$_scale
                    $question = new Question($questionHelper->getQuestion('Scala', 0), 0);
                    $question->setValidator($scaleValidator);
                    $data['scale'] = $questionHelper->ask($input, $output, $question);
                }

                $question = new Question($questionHelper->getQuestion('E un field_name nullable?', 'true'), true);
                $question->setValidator($boolValidator);
                $question->setAutocompleterValues(array('true', 'false'));
                if ($nullable = $questionHelper->ask($input, $output, $question)) {
                    $data['nullable'] = $nullable;
                }

                $question = new Question($questionHelper->getQuestion('Si tratta di una unique key?', 'false'), false);
                $question->setValidator($boolValidator);
                $question->setAutocompleterValues(array('true', 'false'));
                if ($unique = $questionHelper->ask($input, $output, $question)) {
                    $data['unique'] = $unique;
                }

            }

            $fields[$columnName] = $data;
        }

        return $fields;
    }

    protected function createGenerator(){

        return new DoctrineEntityGenerator($this->getContainer()->get('filesystem'), $this->getContainer()->get('doctrine'));
    }
}
