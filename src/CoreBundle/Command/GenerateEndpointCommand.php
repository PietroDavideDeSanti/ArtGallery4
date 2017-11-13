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

/* Servizio MyEndpointEditor*/
use CoreBundle\Services\MyEndpointEditor;
use CoreBundle\Protocol\MyEndpoint;


class GenerateEndpointCommand extends GenerateDoctrineCommand
{
    protected $myEndpoint;

    protected function configure()
    {
        $this
            ->setName('k2:generate:endpoint')
            ->setDescription('Generazione di endpoint')
            ->addOption('endpoint', null, InputOption::VALUE_REQUIRED, 'Qual e\' il nome della function dell\'endpoint da creare?')
            ->addOption('properties', null, InputOption::VALUE_REQUIRED, 'Quali sono le sue proprieta?');
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
        $questionHelper->writeSection($output, 'Benvenuto nel generatore di Endpoint K2:');

        // namespace
        $output->writeln(array(
            'Inserire il nome dell\' endpoint seguendo questa notazione:',
            '<info>nomeBundle</info>:<info>NomeController</info>:<info>nomeDellaFunzione</info>',
            '(Esempio <comment>userBundle:UserController:getAllUsers</comment>)',
            '',
            '<info>Tips:</info>',
            '',
            '- "<comment>NomeController</comment>" va scritto in PascalCase (prima lettera maiuscola)',
            '- "<comment>nomeDellaFunzione</comment>" va scritto in camelCase (prima lettera minuscola)',
            '',
            '',
        ));

        while (true) {
            // Domanda per la scrittura del nome dell'endpoint
            // seguendo la sintassi fooBundle:FooBarController:getAllFoo
            $question = new Question($questionHelper->getQuestion(
                'Nome shortcut dell\'endpoint',
                $input->getOption('endpoint'),
                ":"
            ));
            // Recupero la risposta
            $answer = $questionHelper->ask($input, $output, $question);

            try {
                // Trim della risposta per recuperare nome del bundle, nome del controller, e nome della funzione dell'endpoint:
                list($bundle, $controller, $functionName) = $this->trimName($answer);
            } catch (\Exception $e) {
                // Se la sintassi e' stata scritta male:
                $questionHelper->writeSection($output, 'Attenzione! Sintassi non valida', 'bg=red;fg=white');
                continue;
            }

            // Verifico se il Bundle esiste:
            if(!$this->checkIfBundleExists($bundle)){
                // Se il bundle non esiste:
                $questionHelper->writeSection($output, 'Il bundle non esiste! Bisogna prima crearlo con "php app/console generate:bundle"', 'bg=red;fg=white');
                continue;
            }

            // Verifico se il Controller esiste:
            $baseDir = $this->getContainer()->get('kernel')->getRootDir();
            $srcDir = $this->getSrcDir($baseDir);

            $exists = file_exists($srcDir.$bundle."\\Controller\\".$controller.".php");

            if(!$exists) {
                // Se il controller non esiste
                $question = new Question($questionHelper->getQuestion('Il controller '.$controller.' non esiste. Si desidera crearlo?', 'false'), false);
                $answer = $questionHelper->ask($input, $output, $question);
                if ($answer) {
                    $created = $this->getContainer()->get('MyEndpointEditor')->generateController($bundle, $controller, $srcDir.$bundle."\\Controller\\".$controller.".php");
                    if(!$created){
                        $questionHelper->writeSection($output, 'Errore nella creazione del controller!', 'bg=red;fg=white');
                    }
                    $model = str_replace("Controller", "Model", $controller);
                    $mCreated = $this->getContainer()->get('MyEndpointEditor')->generateModel($bundle, $model, $srcDir.$bundle."\\Model\\".$model.".php");
                    if(!$mCreated){
                        $questionHelper->writeSection($output, 'Errore nella creazione del model!', 'bg=red;fg=white');
                        break;
                    }
                } else {
                    continue;
                }
            }

            // Verifico se il nome della funzione dell'endpoint e' gia' utilizzato:
            $controllerStream = file_get_contents($srcDir.$bundle."\\Controller\\".$controller.".php");
            $check = strpos(strtolower($controllerStream), strtolower($functionName."("));

            if($check){
                // Se il nome della function e' gia' utilizzato:
                $questionHelper->writeSection($output, 'Il nome della function e\' gia\' utilizzato in questro controller!', 'bg=red;fg=white');
                continue;

            }
            //Esco dal ciclo while
            break;
        }

        $this->myEndpoint = new MyEndpoint();
        $this->myEndpoint->setBundle($bundle);
        $this->myEndpoint->setControllerName($controller);
        $this->myEndpoint->setFunctionName($functionName);
        $this->myEndpoint->setSrcDir($srcDir);

        $tmp = explode('Bundle', $bundle);
        $bundlelc = strtolower($tmp[0]);

        $functionlc = strtolower($functionName);

        $this->myEndpoint->setEndpointName($bundlelc . "_" . $functionlc);

        // Setto la prima opzione del comando
        $input->setOption('endpoint', $bundle.':'.$controller.':'.$functionName);

        // Per settare la seconda opzione del comando utilizzo la funzione "addProperties",
        // nella quale pongo le domande per ottenere le informazioni per scrivere l'endpoint
        $input->setOption('properties', $this->addProperties($input, $output, $questionHelper));

    }

    protected function execute(InputInterface $input, OutputInterface $output){
        
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/* - 1. inizialize()                                                           */
/* - 2. interact()                                                             */
/* - 3. EXECUTE()                                                              */
/*                                                                             */
/* This method is executed after interact() and initialize(). It contains      */ 
/* the logic you want the command to execute.                                  */
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/* *     Q u i    s i    g e n e r a    l ' E n d p o i n t      * * * * * * * */
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

        $this->getContainer()->get('MyEndpointEditor')->generateEndpoint($this->myEndpoint);
                  

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

        echo "\n\n";
        echo "/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */\n";
        echo "/* * * * * * * * *     Endpoint generato     * * * * * * * * * */\n";
        echo "/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */\n";
        
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    }


    private function addProperties(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper){

        $questionHelper->writeSection($output, 'Definisci le caratteristiche dell\'endpoint rispondendo alle domande che ti verranno poste', 'bg=green;fg=white');

        $output->writeln(array(
            '<info>Tip</info>: Se si vuole ricominciare a rispondere a tutte le domande,',
            'e\' sufficiente rispondere "<comment>ricomincia</comment>" ad una qualsiasi domanda',
            '',
            '<comment>Domande</comment>:',
            ''
        ));

        $boolValidator = function ($value) {
            if (null === $valueAsBool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
                throw new \InvalidArgumentException(sprintf('Valore booleano non valido: "%s".', $value));
            }

            return $valueAsBool;
        };

        while (true) {

            $question = new Question($questionHelper->getQuestion('Qual e\' la descrizione di questo endpoint?', 'descrizione'), 'TODO Descrizione');
            $ans = $questionHelper->ask($input, $output, $question);
            if($ans === 'ricomincia'){
                continue;
            }
            $this->myEndpoint->setDescription($ans);


            $question = new Question($questionHelper->getQuestion('Qual e\' la route dell\'endpoint?', '/foo/{var1}/bar/{var2}'), '/foo');
            $route = $questionHelper->ask($input, $output, $question);
            if($route === 'ricomincia'){
                continue;
            }
            $this->myEndpoint->setRoute($route);

            $var = $this->getVariablesFromRoute($route);

            $variables = array();
            foreach($var as $variable){
                $question = new Question($questionHelper->getQuestion('Che tipo di variabile e\' '.$variable.'?', 'int/string'), 'int');
                $type = $questionHelper->ask($input, $output, $question);
                if($type === 'ricomincia'){
                    continue;
                }
                $variables[] = array(
                    'name' => $variable,
                    'type' => $type
                );

            }
            $this->myEndpoint->setVariables($variables);

            $question = new Question($questionHelper->getQuestion('Qual e\' il metodo dell\'endpoint?', 'POST/GET/PUT'), 'POST');
            $ans = $questionHelper->ask($input, $output, $question);
            if($ans === 'ricomincia'){
                continue;
            }
            $this->myEndpoint->setMethod($ans);

            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
            // *  *  Domande per la configurazione del Front Controller    *  *  *//
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
            $frontController = array();

            $question = new Question($questionHelper->getQuestion('Ci sono parametri da validare con le Request (Rawbody, Post, ...)?', 'true'), true);
            $question->setValidator($boolValidator);
            $ans = $questionHelper->ask($input, $output, $question);
            if($ans === 'ricomincia'){
                continue;
            }
            $this->myEndpoint->setCheck400($ans);

            // Se ci sono validazioni con le Request?
            $request = array();
            if($ans) {
                // Querystring?
                $question = new Question($questionHelper->getQuestion('Ci sono Request di tipo QUERYSTRING?', 'false'), false);
                $question->setValidator($boolValidator);
                if ($ans = $questionHelper->ask($input, $output, $question)) {
                    if ($ans === 'ricomincia') {
                        continue;
                    }
                    if ($ans) {
                        $request['Querystring'] = true;
                    }
                }
                // Post
                $question = new Question($questionHelper->getQuestion('Ci sono Request di tipo POST?', 'false'), false);
                $question->setValidator($boolValidator);
                if ($ans = $questionHelper->ask($input, $output, $question)) {
                    if ($ans === 'ricomincia') {
                        continue;
                    }
                    if ($ans) {
                        $request['Post'] = true;
                    }
                }
                // Rawbody
                $question = new Question($questionHelper->getQuestion('Ci sono Request di tipo RAWBODY?', 'false'), false);
                $question->setValidator($boolValidator);
                if ($ans = $questionHelper->ask($input, $output, $question)) {
                    if ($ans === 'ricomincia') {
                        continue;
                    }
                    if ($ans) {
                        $request['Rawbody'] = true;
                    }
                }
                // Headers
                $question = new Question($questionHelper->getQuestion('Ci sono Request di tipo HEADERS?', 'false'), false);
                $question->setValidator($boolValidator);
                if ($ans = $questionHelper->ask($input, $output, $question)) {
                    if ($ans === 'ricomincia') {
                        continue;
                    }
                    if ($ans) {
                        $request['Headers'] = true;
                    }
                }

                $frontController['400'] = $request;

            } // Fine delle domande sulle REQUEST


            $question = new Question($questionHelper->getQuestion('Il controller ha il controllo sul 401?', 'true'), true);
            $question->setValidator($boolValidator);
            $ans = $questionHelper->ask($input, $output, $question);
            if($ans === 'ricomincia'){
                continue;
            }

            $question = new Question($questionHelper->getQuestion('Il controller ha il controllo sul 403?', 'true'), true);
            $ans = $questionHelper->ask($input, $output, $question);
            if($ans === 'ricomincia'){
                continue;
            }
            if($ans == true) {
                $frontController['403'] = true;
            }
            // Fine delle domande sul front controller

            $this->myEndpoint->setFrontController($frontController);

            $question = new Question($questionHelper->getQuestion('L\'endpoint restituisce una VISTA o un JSON?', 'JSON/VISTA'), 'JSON');
            $ans = $questionHelper->ask($input, $output, $question);
            if($ans == 'ricomincia'){
                continue;
            }
            $this->myEndpoint->setReturn($ans);

            $this->myEndpoint->setFrontController($frontController);

            if($ans=="VISTA") {

                $question = new Question($questionHelper->getQuestion('Ci sono dei Context?', 'true'), true);
                $ans = $questionHelper->ask($input, $output, $question);
                if ($ans == true) {
                    $nextContext = true;
                    while($nextContext) {

                        $question = new Question($questionHelper->getQuestion('Scrivi il nome di un Context (per terminare il processo di inserimento dei context premere \<INVIO\> senza digitare alcun nome)', 'Nome-Del-Context'), '');
                        $ans = $questionHelper->ask($input, $output, $question);
                        if ($ans == '') {
                            $nextContext = false;
                        } else {
                            $this->myEndpoint->addContext($ans);
                        }
                    }
                }
            }


            break;
        }

        return true;
    }

    protected function createGenerator(){

        return new DoctrineEntityGenerator($this->getContainer()->get('filesystem'), $this->getContainer()->get('doctrine'));
    }

    private function trimName($input){
        $tmp = explode(":", $input);
        $bundle = $tmp[0];
        $controller = $tmp[1];
        $functionName = $tmp[2];

        return array($bundle, $controller, $functionName);
    }

    private function getSrcDir($baseDir){
        $tmp = explode("src", $baseDir);
        $srcDir = $tmp[0] . "\\..\\src\\";
        return $srcDir;
    }

    private function checkIfBundleExists($bundle){
        try {
            $this->getContainer()->get('kernel')->getBundle($bundle);
        } catch (\Exception $e) {
            // Se il bundle non esiste:
            return false;
        }
        return true;
    }

    private function getVariablesFromRoute($route){
        $variables = array();
        $tmps = explode('{', $route);
        foreach($tmps as $tmp){
            $tmp = explode('}',$tmp);
            $variables[] = $tmp[0];
        }
        unset($variables[0]);
        return $variables;
    }

}

