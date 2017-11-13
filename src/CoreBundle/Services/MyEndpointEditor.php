<?php

namespace CoreBundle\Services;

use CoreBundle\Protocol\MyEndpoint as Endpoint;
use Doctrine\ORM\EntityManager;
use UserBundle\Entity\Element;

class MyEndpointEditor
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function generateEndpoint(Endpoint $myEndpoint)
    {
        $str = "/**\n";
        $str .= "     * ".$myEndpoint->getDescription()."\n";
        // Route "normale" (senza "local")
        $str .= '     * @Route("'.$myEndpoint->getRoute().'", name="'.$myEndpoint->getEndpointName().'"';
        // Se ci sono dei requirements
        $n = count($myEndpoint->getVariables());
        if( $n > 0 ){
            $str .= ', requirements={';
            foreach($myEndpoint->getVariables() as $key => $variable) {
                switch($variable['type']) {
                    case 'int':
                        $str .= '"'. $variable["name"].'": "\\d+"';
                        break;
                    case 'string':
                        $str .= '"'.  $variable["name"].'": "\\w+"';
                }
                if( ($key+1) < $n ){
                    $str .= ', ';
                }
            }
            $str .= '}';
        }
        $str .= ")\n";

        // Route con "{_locale}"
//        $str .= '     * @Route("{_locale}'.$myEndpoint->getRoute().'", name="'.$myEndpoint->getEndpointName().'"';
//        // Se ci sono dei requirements
//        $n = count($myEndpoint->getVariables());
//        if( $n > 0 ){
//            $str .= ', requirements={';
//            foreach($myEndpoint->getVariables() as $key => $variable) {
//                switch($variable['type']) {
//                    case 'int':
//                        $str .= '"'. $variable["name"].'": "\\d+"';
//                        break;
//                    case 'string':
//                        $str .= '"'.  $variable["name"].'": "\\w+"';
//                }
//                if( ($key+1) < $n ){
//                    $str .= ', ';
//                }
//            }
//            $str .= '}';
//        }
//        $str .= ")\n";

        $str .= '     * @Method("'.$myEndpoint->getMethod().'"'.");\n";
        $str .= "     */\n";
        $str .= '    public function '.$myEndpoint->getFunctionName().'(Request $request';
        foreach($myEndpoint->getVariables() as $variable){
            $str .= ", $" .$variable['name'];
        }
        $str .=") {\n";
        $str .= "        try{\n";
        $str .= "\n";
        $str .= '            $config = new EndpointConfiguration();';
        $str .= "\n";
        // 400:
        //  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  //
        if($myEndpoint->getCheck400()){
            $str .= $this->add400Check($myEndpoint);
        }
        // 401:
        //  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  //
        $str .='            $config->login = ';
        $frontController = $myEndpoint->getFrontController();
        if(array_key_exists('401', $frontController)) {
            $str .="true;\n";
        } else {
            $str .="false;\n";
        }
        // C'e' il 403?
        //  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  //
        if(array_key_exists('403', $frontController)) {
            $str .='            $config->aclcode = "'.$myEndpoint->getRoute().'"'.";\n";
        }

        // Inserimento dell'Element nel database
        $elementData = array();
        $elementData["code"] = $myEndpoint->getRoute();
        $elementData["source"] = $myEndpoint->getControllerName() . ":" . $myEndpoint->getFunctionName();
        $elementData["action"] = $myEndpoint->getRoute();
        $isPublic = array_key_exists('403', $frontController) ? 0 : 1;
        $elementData["isPublic"] = $isPublic;

//        $this->em->getRepository("UserBundle:Element")->insertElement($elementData);

        $str .= '            $config->context = array('."\n";

        $arrContext = $myEndpoint->getContext();
        foreach($arrContext as $context){
            $str .= "                '". $context ."',\n";
        }
        $str .= "            );\n";
        $str .= "\n";
        $str .= "            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):\n";
        $str .= "            \$globalVars = \$this->validateRequest(\$request, \$config);\n";
        $str .= "\n";
        $str .= "            // Inizializzo la risposta:\n";
        $str .= "            \$response = \$this->initResponse(\$config, \$globalVars);\n";
        $str .= "\n";

        $controllerName = $myEndpoint->getControllerName();
        $tmp = explode('Controller', $controllerName);
        $modelName = ucfirst(strtolower($tmp[0]) . "Model");

        $str .= "            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *\n";
        $str .= "            \$em = \$this->getDoctrine()->getManager();\n";
        $str .= "            \$container = \$this->container;\n";
        $str .= "            \$model = new " . $modelName . "(\$em, \$container);\n";
        $str .= "            \$response = \$model->{__FUNCTION__}(\$globalVars, \$response";

        foreach($myEndpoint->getVariables() as $variable){
            $str .= ", $" .$variable['name'];
        }

        $str .=");\n";
        $str .= "            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *\n";
        $str .= "            \n";

        // E' un endpoint che restituisce una VIEW o un JSON?
        $return = $myEndpoint->getReturn();
        if(strtoupper($return) == "VISTA") {
            $str .= "            // Lancio il render della view:\n";
            $str .= '            return $this->render("<html><body>Sator arepo tenet opera rotas</body></html>", array("twig" => $response));'."\n";
            $str .= "\n";
        } else {
            $str .= "            // Restituisco il JSON:\n";
            $str .= '            return new JsonResponse($response, 200);';
            $str .= "\n";
            $str .= "\n";
        }
        $str .= '        } catch (HttpException $e) {'."\n";
        $str .= '            return $this->get("MyException")->errorHttpHandler($e);'."\n";
        $str .= "        }\n";
        $str .= "    }\n";


        $srcDir = $myEndpoint->getSrcDir();
        $bundle = $myEndpoint->getBundle();

        $controllerPath = $srcDir . $bundle . "\\Controller\\" . $controllerName . ".php";

        $controller = file_get_contents($controllerPath, true);
        $updatedController = str_replace("#####",$str."\n    #####", $controller);
        file_put_contents($controllerPath, $updatedController);

        $strModel = '';
        $strModel .= 'public function '.$myEndpoint->getFunctionName()." (GlobalVars \$globalVars, Response \$response";

        foreach($myEndpoint->getVariables() as $variable){
            $strModel .= ", $" .$variable['name'];
        }
        $strModel .= "){\n";
        $strModel .= "        try{\n";
        $strModel .= "\n";
        $strModel .= "            \$response->data = '';\n";
        $strModel .= "            return \$response;\n";
        $strModel .= "\n";
        $strModel .= '        } catch (DBALException $e) {'."\n";
        $strModel .= '            throw new HttpException(500, $e->getMessage());'."\n";
        $strModel .= "        }\n";
        $strModel .= "    }\n";

        $modelPath = $srcDir . $bundle . "\\Model\\" . $modelName . ".php";
        $model = file_get_contents($modelPath, true);
        $updatedModel = str_replace("#####",$strModel."\n    #####", $model);
        file_put_contents($modelPath, $updatedModel);


    }

    public function generateController($bundle, $controllerName, $path)
    {

        $tmp = explode('Controller', $controllerName);
        $modelName = strtolower($tmp[0]) . "Model";

        $str = "<?php \n";
        $str .= "\n";
        $str .= "namespace ".$bundle."\\Controller;\n";
        $str .= "\n";

        $str .= "use CoreBundle\Controller\K2Controller;\n";
        $str .= "\n";
        $str .= "use ".$bundle. "\\Model\\". ucfirst($modelName) .";\n";
        $str .= "\n";
        $str .= "// Route Libraries:\n";
        $str .= "use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;\n";
        $str .= "use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;\n";
        $str .= "use Symfony\Component\HttpFoundation\Request;\n";
        $str .= "\n";
        $str .= "// Exception Libraries:\n";
        $str .= "use Symfony\Component\HttpKernel\Exception\HttpException;\n";
        $str .= "\n";
        $str .= "// Response Libraries:\n";
        $str .= "use Symfony\Component\HttpFoundation\JsonResponse;\n";
        $str .= "\n";
        $str .= "// Protocol::\n";
        $str .= "use CoreBundle\Protocol\EndpointConfiguration;\n";
        $str .= "use CoreBundle\Protocol\ResponseProtocol;\n";
        $str .= "\n";
        $str .= "class ".$controllerName." extends K2Controller{\n";
        $str .= "\n";
        $str .= "    #####\n";
        $str .= "\n";
        $str .= "}\n";

        $edit = $this->file_force_contents($path, $str);

        return $edit;

    }

    public function generateModel($bundle, $modelName, $path)
    {

        $str = "<?php \n";
        $str .= "\n";
        $str .= "namespace " . $bundle . "\\Model;\n";
        $str .= "\n";
        $str .= "use CoreBundle\Protocol\GlobalVars;\n";
        $str .= "use CoreBundle\Protocol\Response;\n";
        $str .= "use Doctrine\DBAL\DBALException;\n";
        $str .= "use Symfony\Component\HttpKernel\Exception\HttpException;\n";
        $str .= "\n";
        $str .= "// Dependency Injection:\n";
        $str .= "use Doctrine\Common\Persistence\ObjectManager as EntityManager;\n";
        $str .= "use Symfony\Component\DependencyInjection\ContainerInterface;\n";
        $str .= "\n";
        $str .= "class ".$modelName." {\n";
        $str .= "\n";
        $str .= "    private \$em;\n";
        $str .= "    private \$container;\n";
        $str .= "\n";
        $str .= "    /**\n";
        $str .= "     * @param EntityManager \$em\n";
        $str .= "     * @param ContainerInterface \$container\n";
        $str .= "     */\n";
        $str .= "    function __construct(EntityManager \$em, ContainerInterface \$container) {\n";
        $str .= "        \$this->em = \$em;\n";
        $str .= "        \$this->container = \$container;\n";
        $str .= "    }\n";
        $str .= "\n";
        $str .= "    #####\n";
        $str .= "\n";
        $str .= "}\n";

        $edit = $this->file_force_contents($path, $str);

        return $edit;

    }

    private function add400Check(Endpoint $myEndpoint)
    {

        $frontController = $myEndpoint->getFrontController();
        $params = $frontController['400'];

        $str = '';
        if( array_key_exists('Querystring', $params) ){
            $str .= '            $config->querystring = "'.$myEndpoint->getBundle().'\Request\Querystring\\\" . ucfirst(__FUNCTION__);' . "\n";
            $request = $this->generateRequest($myEndpoint, 'Querystring');
        }
        if( array_key_exists('Post', $params) ){
            $str .= '            $config->post = "'.$myEndpoint->getBundle().'\Request\Post\\\" . ucfirst(__FUNCTION__);' . "\n";
            $request = $this->generateRequest($myEndpoint, 'Post');
        }
        if( array_key_exists('Rawbody', $params) ){
            $str .= '            $config->rawbody = "'.$myEndpoint->getBundle().'\Request\Rawbody\\\" . ucfirst(__FUNCTION__);' . "\n";
            $request = $this->generateRequest($myEndpoint, 'Rawbody');
        }
        if( array_key_exists('Headers', $params) ){
            $str .= '            $config->headers = "'.$myEndpoint->getBundle().'\Request\Headers\\\" . ucfirst(__FUNCTION__);' . "\n";
            $request = $this->generateRequest($myEndpoint, 'Headers');
        }

        return $str;
    }

    private function generateRequest(Endpoint $myEndpoint, $requestType)
    {

        $str = '<?php'."\n";
        $str .= "\n";
        $str .= 'namespace '.$myEndpoint->getBundle().'\\Request\\'.$requestType.';'."\n";
        $str .= "\n";
        $str .= "use Symfony\\Component\\Validator\\Constraints as Assert;\n";
        $str .= "\n";
        $str .= "/**\n";
        $str .= " *\n";
        $str .= " * @author K2\n";
        $str .= ' * Per la documentazione sugli Assert, vedere qui:'."\n";
        $str .= ' * http://symfony.com/doc/current/validation.html'."\n";
        $str .= " * \n";
        $str .= " */\n";
        $str .= 'class '.ucfirst($myEndpoint->getFunctionName()).' {'."\n";
        $str .= "\n";
        $str .= "\n";
        $str .= "\n";
        $str .= "\n";
        $str .= "}\n";

        $srcDir = $myEndpoint->getSrcDir();
        $bundle = $myEndpoint->getBundle();

        $directory = $srcDir . $bundle . "\\Request\\" . $requestType . "\\" . ucfirst($myEndpoint->getFunctionName()) . ".php";

        $this->file_force_contents($directory, $str);
    }

    private function file_force_contents($dir, $contents)
    {
        $parts = explode('\\', $dir);

        // Tolto l'ultimo elemento dell'array (nome del file)
        $fileName = array_pop($parts);

        $dir = $parts[0];
        unset($parts[0]);
        foreach ($parts as $part) {
            // Verifico ricorsivamente se ciascuna cartella esiste
            if ( !is_dir($dir .= "\\$part") ) {
                mkdir($dir);
            }
        }
        return file_put_contents("$dir\\$fileName", $contents);

    }

}