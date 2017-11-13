<?php

namespace CoreBundle\Services;
//exception
use Symfony\Component\HttpKernel\Exception\HttpException as HttpException;
use CoreBundle\Services\MyException as MyException;
use CoreBundle\Utils\status as status;

class MyEntityEditor
{

    public function generateEntity($baseDir, $bundle, $entityName, $tableName, $normalfield, $manytoone, $onetomany, $manytomany, $createJoin = true) {
        $str = "<?php \n";
        $str .= "\n";
        $str .= "namespace ".$bundle."\Entity;\n";
        $str .= "\n";
        $str .= "use Doctrine\ORM\Mapping as ORM;\n";
        $str .= "use Doctrine\Common\Collections\ArrayCollection as Collection;\n";
        $str .= "//serializer\n";
        $str .= "use JMS\Serializer\Annotation\Exclude;\n";
        $str .= "use JMS\Serializer\Annotation\Groups;\n";
        $str .= "use JMS\Serializer\Annotation\MaxDepth;\n";
        $str .= "\n";
        $str .= "/**\n";
        $str .= "* ".$entityName."\n";
        $str .= "*\n";
        $str .= "* @ORM\Table(name=\"z_".$tableName."\")\n";
        $str .= "* @ORM\Entity(repositoryClass=\"".$bundle."\Repository\\".$entityName."Repository\")\n";
        $str .= "*/\n";
        $str .= "class ".$entityName." {\n";
        $str .= "\n";
        $str .= "    /**\n";
        $str .= "     * @var int\n";
        $str .= "     *\n";
        $str .= "     * @ORM\Column(name=\"id\", type=\"integer\")\n";
        $str .= "     * @ORM\Id\n";
        $str .= "     * @ORM\GeneratedValue(strategy=\"AUTO\")\n";
        $str .= "     * @Groups({\"".$entityName."\", \"".$entityName.".id\"})\n";
        $str .= "     */\n";
        $str .= "    private \$id;\n";
        $str .= "\n";

        /*   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   */
        /*   *   *       R e l a z i o n i    M a n y T o O n e          *   *   *   *   */
        /*   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   */
        foreach($manytoone as $property){

            $str .= $this->addManyToOneField($property, $entityName);

            if($createJoin) {
                // Verifico se l'Entity con cui ha la relazione esiste:
                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                // Recupero il path e il namespace della Entity di cui devo verificare l'esistenza
                $tmp = $this->getPathAndTargetNamespace($property, $baseDir, $bundle, $entityName);
                $path = $tmp['path'];
                $joinedNamespace = $tmp['joinedNamespace'];

                // Verifico se il file esiste
                $exists = file_exists($path);

                if ($exists) {
                    $this->addJoinedProperty($path, $property, $entityName, $joinedNamespace);

                } else {
                    $this->generateJoinedEntityAndRepository($baseDir, $path, $property, $entityName, $joinedNamespace);
                }
            }
        }

        /*   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   */
        /*   *     A t t r i b  u t i    S e n z a     R e l a z i o n i     *   *   *   */
        /*   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   */
        foreach ($normalfield as $property) {
            $str .= $this->addNormalField($property, $entityName);
        }

        /*   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   */
        /*   *   *   *   *     C a m p i    d i    S i s t e m a     *   *   *   *   *   */
        /*   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   */

        $str .= $this->addSystemFields();

        /*   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   */
        /*   *   *       R e l a z i o n i    O n e T o M a n y          *   *   *   *   */
        /*   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   */
        foreach($onetomany as $property){

            $str .= $this->addOneToManyField($property, $entityName);

            if($createJoin) {
                // Verifico se l'Entity con cui ha la relazione esiste:
                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                // Recupero il path e il namespace della Entity di cui devo verificare l'esistenza
                $tmp = $this->getPathAndTargetNamespace($property, $baseDir, $bundle, $entityName);
                $path = $tmp['path'];
                $joinedNamespace = $tmp['joinedNamespace'];

                // Verifico se il file esiste
                $exists = file_exists($path);

                if ($exists) {
                    $this->addJoinedProperty($path, $property, $entityName, $joinedNamespace);

                } else {
                    $this->generateJoinedEntityAndRepository($baseDir, $path, $property, $entityName, $joinedNamespace);
                }
            }
        }

        /*   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   */
        /*   *   *       R e l a z i o n i    M a n y T o M a n y        *   *   *   *   */
        /*   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   */
        foreach($manytomany as $property){
            $str .= $this->addManyToMany($property, $entityName);

            if($createJoin) {
                // Verifico se l'Entity con cui ha la relazione esiste:
                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                // Recupero il path e il namespace della Entity di cui devo verificare l'esistenza
                $tmp = $this->getPathAndTargetNamespace($property, $baseDir, $bundle, $entityName);
                $path = $tmp['path'];
                $joinedNamespace = $tmp['joinedNamespace'];

                // Verifico se il file esiste
                $exists = file_exists($path);

                if ($exists) {
                    $this->addJoinedProperty($path, $property, $entityName, $joinedNamespace);

                } else {
                    $this->generateJoinedEntityAndRepository($baseDir, $path, $property, $entityName, $joinedNamespace);
                }
            }
        }

        /*   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   */
        /*   *   *   *   *       C o s t r u t t o r e           *   *   *   *   *   *   */
        /*   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   */

        $str .= "    /**\n";
        $str .= "     * Constructor\n";
        $str .= "     */\n";
        $str .= "    public function __construct(){\n";
        $str .= "        \$this->timeInsert = new \DateTime('now');\n";
        $str .= "        \$this->timeAction = new \DateTime('now');\n";
        $str .= "        \$this->userAction = 0;\n";
        $str .= "        \$this->status = \"A\";\n";
        $str .= "\n";
        $str .= "        #####\n";
        foreach($manytomany as $property){
            $str .= "        \$this->".strtolower($property["fieldName"])." = new Collection();\n";
        }
        foreach($onetomany as $property){
            $str .= "        \$this->".strtolower($property["fieldName"])." = new Collection();\n";
        }
        $str .= "    }  \n";
        $str .= "\n";
        $str .= "\n";
        $str .= "}\n";

        $srcDir = $baseDir . "\\..\\src\\";

        $path = $srcDir . $bundle . "\\Entity\\" . $entityName . ".php";

        $this->file_force_contents($path, $str);

    }

    public function generateRepository($baseDir, $bundle, $entityName, $manytoone=null) {

        $str = "<?php \n";
        $str .= "\n";
        $str .= "namespace ".$bundle."\Repository;\n";
        $str .= "\n";
        $str .= "use CoreBundle\Libraries\AbstractRepository;\n";
        $str .= "use CoreBundle\Utils\status as status;\n";
        $str .= "use ".$bundle."\Entity\\".$entityName.";\n";
        $str .= "\n";
        $str .= "/**\n";
        $str .= "* ".$entityName."Repository\n";
        $str .= "*\n";
        $str .= "*/\n";
        $str .= "class ".$entityName."Repository extends AbstractRepository {\n";


        if(!is_null($manytoone)){
            foreach($manytoone as $mto){
                $str .="    public function get_all_by_".strtolower($mto['targetEntity'])."_id(\$id, \$sort=array(), \$limit=null, \$offset=null) {\n";
                $str .="        return parent::findBy( array('".strtolower($mto['targetEntity'])."Id' => \$id, 'status' => 'A'), \$sort, \$limit, \$offset);\n";
                $str .="    }\n";
            }
        }

        $str .= "}\n";

        $srcDir = $baseDir . "\\..\\src\\";
        $path = $srcDir . $bundle . "\\Repository\\" . $entityName . "Repository.php";
        $this->file_force_contents($path, $str);
    }

    public function updateEntity($baseDir, $bundle, $entityName, $normalfield, $manytoone, $onetomany, $manytomany, $deleted) {

        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
        //*  *  Recupero la entity e la divido seguendo le "linee di cesura"   *  *  *  *//
        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

        // Recupero lo stream del file:
        $entityStream = file_get_contents($baseDir."/../src/".$bundle."/Entity/".$entityName.".php", true);

        // Eseguo il comando di explode sulla entity:
        $entityExploded = explode("private \$id;", $entityStream);

        $entityHead = $entityExploded[0] . "private \$id;\n\n";
        $tmp = explode("Constructor", $entityExploded[1]);
        $entityOldProperties = $tmp[0];

        $campiSistema = $this->addSystemFields();

        // Preparo un array con tutte le entity che devono avere l'inizializzazione della Collection nel costruttore:
        $costruttore = $this->addConstructor(array_merge($manytomany, $onetomany));

        // Preparo il namespace della entity di cui devo recuperare le colonne:
        $entityNamespace = "\\".$bundle."\\Entity\\".$entityName;

        // Recupero le properties della entity:
        $entityProperties = $this->getEntityProperties($entityNamespace);

        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
        //*  *  *  Preparo le stringhe che costituiranno la entity aggiornata  *  *  *  *//
        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

        $newNormalField = "";
        $newManyToOne = "";
        $newOneToMany = "";
        $newManyToMany = "";

        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
        //*  *  *  *        Opero (in un ciclo foreach) le modifiche     *  *  *  *  *  *//
        //*  *  *  *  *   alle proprieta' gia' presenti nella Entity     *  *  *  *  *  *//
        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

        foreach($entityProperties as $oldPropertyName){

            $stringOldProperty = $this->getEntityProperty($oldPropertyName, $entityOldProperties);

            $trovata = false;

            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
            // Cerco se la property/colonna i-ma e' stata CANCELLATA
            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
            foreach($deleted as $key => $field){

                if($oldPropertyName == $field['fieldName']){
                    // Se esiste, rimuovo l'eventuale proprieta' dalla entity legata con la join

                    $this->removeJoinedProperty($baseDir, $stringOldProperty, $bundle);

                    $trovata = true;

                    // Tolgo l'elemento dall'array
                    unset($deleted[$key]);
                    // Esco dal ciclo
                    break;
                }
            }

            if($trovata){
                continue;
            }

            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
            // Cerco se la property i-ma e' tra i campi "NORMALI" (quelli senza relazioni O-M, M-O, M-M)
            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
            foreach($normalfield as $key => $field){

                if($oldPropertyName == $field['fieldName']){

                    // Se quella proprieta' aveva legami precedenti, li elimino:
                    $this->removeJoinedProperty($baseDir, $stringOldProperty, $bundle);

                    // Se la trovo, aggiungo alla stringa $newNormalField il campo aggiornato
                    $newNormalField .= $this->addNormalField($field, $entityName);

                    $trovata = true;
                    // Tolgo l'elemento dall'array
                    unset($normalfield[$key]);
                    // Esco dal ciclo
                    break;
                }
            }

            if($trovata){
                continue;
            }

            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
            // Cerco tra le modifiche sulle MANY TO ONE
            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
            foreach($manytoone as $key => $field){

                if($oldPropertyName == $field['fieldName']){

                    // Se quella proprieta' aveva legami precedenti, li elimino:
                    $this->removeJoinedProperty($baseDir, $stringOldProperty, $bundle);

                    // Aggiungo alla stringa $newManyToOne il campo aggiornato
                    $newManyToOne .= $this->addManyToOneField($field, $entityName);

                    // Verifico se l'Entity con cui ha la relazione esiste:
                    //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                    // Recupero il path e il namespace della Entity di cui devo verificare l'esistenza
                    $tmp = $this->getPathAndTargetNamespace($field, $baseDir, $bundle, $entityName);
                    $path = $tmp['path'];
                    $joinedNamespace = $tmp['joinedNamespace'];

                    // Verifico se il file esiste
                    $exists = file_exists($path);

                    if($exists) {
                        $this->addJoinedProperty($path, $field, $entityName, $joinedNamespace);

                    } else {
                        $this->generateJoinedEntityAndRepository($baseDir, $path, $field, $entityName, $joinedNamespace);
                    }

                    $trovata = true;
                    // Tolgo l'elemento dall'array
                    unset($manytoone[$key]);
                    // Esco dal ciclo
                    break;
                }
            }

            if($trovata){
                continue;
            }

            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
            // Cerco tra le modifiche sulle ONE TO MANY
            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
            foreach($onetomany as $key => $field){

                if($oldPropertyName == $field['fieldName']){

                    // Se quella proprieta' aveva legami precedenti, li elimino:
                    $this->removeJoinedProperty($baseDir, $stringOldProperty, $bundle);

                    // Aggiungo alla stringa $newOneToMany il campo aggiornato
                    $newOneToMany .= $this->addOneToManyField($field, $entityName);

                    // Verifico se l'Entity con cui ha la relazione esiste:
                    //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                    // Recupero il path e il namespace della Entity di cui devo verificare l'esistenza
                    $tmp = $this->getPathAndTargetNamespace($field, $baseDir, $bundle, $entityName);
                    $path = $tmp['path'];
                    $joinedNamespace = $tmp['joinedNamespace'];

                    // Verifico se il file esiste
                    $exists = file_exists($path);

                    if($exists) {
                        $this->addJoinedProperty($path, $field, $entityName, $joinedNamespace);


                    } else {
                        $this->generateJoinedEntityAndRepository($baseDir, $path, $field, $entityName, $joinedNamespace);
                    }

                    $trovata = true;
                    // Tolgo l'elemento dall'array
                    unset($onetomany[$key]);
                    // Esco dal ciclo
                    break;
                }
            }

            if($trovata){
                continue;
            }

            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
            // Cerco tra le modifiche sulle MANY TO MANY
            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
            foreach($manytomany as $key => $field){

                if($oldPropertyName == $field['fieldName']){

                    // Se quella proprieta' aveva legami precedenti, li elimino:
                    $this->removeJoinedProperty($baseDir, $stringOldProperty, $bundle);

                    // Aggiungo alla stringa $newManyToMany il campo aggiornato
                    $newManyToMany .= $this->addManyToMany($field, $entityName);

                    // Verifico se l'Entity con cui ha la relazione esiste:
                    //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                    // Recupero il path e il namespace della Entity di cui devo verificare l'esistenza
                    $tmp = $this->getPathAndTargetNamespace($field, $baseDir, $bundle, $entityName);
                    $path = $tmp['path'];
                    $joinedNamespace = $tmp['joinedNamespace'];

                    // Verifico se il file esiste
                    $exists = file_exists($path);

                    if($exists) {
                        $this->addJoinedProperty($path, $field, $entityName, $joinedNamespace);


                    } else {
                        $this->generateJoinedEntityAndRepository($baseDir, $path, $field, $entityName, $joinedNamespace);
                    }

                    $trovata = true;
                    // Tolgo l'elemento dall'array
                    unset($manytomany[$key]);
                    // Esco dal ciclo
                    break;
                }
            }

            if($trovata){
                continue;
            }

            // Altrimenti scrivo la proprieta' così com'era:
            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

            // Verifico se la precedente proprieta' era un campo "normale", oppure un campo con relazione (O-M, M-O, M-M):
            if(strrpos($stringOldProperty, "ManyToOne")){
                $newManyToOne .= $stringOldProperty ."\n";

            } else if(strrpos($stringOldProperty, "OneToMany")){
                $newOneToMany .= $stringOldProperty ."\n";

                $costruttore = $this->updateConstructor($costruttore, $stringOldProperty);

            } else if(strrpos($stringOldProperty, "ManyToMany")){
                $newManyToMany .= $stringOldProperty ."\n";

                $costruttore = $this->updateConstructor($costruttore, $stringOldProperty);

            } else {
                $newNormalField .= $stringOldProperty . "\n";
            }

        }

        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
        //*  *  *  *  *  *   Aggiungo alla Entity le nuove proprieta'    *  *  *  *  *  *//
        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

        // Aggiungo tutti i campi che non sono stati rimossi dai rispettivi array nei cicli sopra

        foreach($normalfield as $newPropertyAttributes){
            $newNormalField .= $this->addNormalField($newPropertyAttributes, $entityName);
        }

        foreach($manytoone as $newPropertyAttributes) {
            $newManyToOne .= $this->addManyToOneField($newPropertyAttributes, $entityName);

            // Verifico se l'Entity con cui ha la relazione esiste:
            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
            // Recupero il path e il namespace della Entity di cui devo verificare l'esistenza
            $tmp = $this->getPathAndTargetNamespace($newPropertyAttributes, $baseDir, $bundle, $entityName);
            $path = $tmp['path'];
            $joinedNamespace = $tmp['joinedNamespace'];

            // Verifico se il file esiste
            $exists = file_exists($path);

            if($exists) {
                $this->addJoinedProperty($path, $newPropertyAttributes, $entityName, $joinedNamespace);


            } else {
                $this->generateJoinedEntityAndRepository($baseDir, $path, $field, $entityName, $joinedNamespace);
            }
        }

        foreach($onetomany as $newPropertyAttributes){
            $newOneToMany .= $this->addOneToManyField($newPropertyAttributes, $entityName);

            // Verifico se l'Entity con cui ha la relazione esiste:
            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
            // Recupero il path e il namespace della Entity di cui devo verificare l'esistenza
            $tmp = $this->getPathAndTargetNamespace($newPropertyAttributes, $baseDir, $bundle, $entityName);
            $path = $tmp['path'];
            $joinedNamespace = $tmp['joinedNamespace'];

            // Verifico se il file esiste
            $exists = file_exists($path);

            if($exists) {
                $this->addJoinedProperty($path, $newPropertyAttributes, $entityName, $joinedNamespace);


            } else {
                $this->generateJoinedEntityAndRepository($baseDir, $path, $field, $entityName, $joinedNamespace);
            }
        }

        foreach($manytomany as $newPropertyAttributes){
            $newManyToMany .= $this->addManyToMany($newPropertyAttributes, $entityName);

            // Verifico se l'Entity con cui ha la relazione esiste:
            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
            // Recupero il path e il namespace della Entity di cui devo verificare l'esistenza
            $tmp = $this->getPathAndTargetNamespace($newPropertyAttributes, $baseDir, $bundle, $entityName);
            $path = $tmp['path'];
            $joinedNamespace = $tmp['joinedNamespace'];

            // Verifico se il file esiste
            $exists = file_exists($path);

            if($exists) {
                $this->addJoinedProperty($path, $newPropertyAttributes, $entityName, $joinedNamespace);


            } else {
                $this->generateJoinedEntityAndRepository($baseDir, $path, $field, $entityName, $joinedNamespace);
            }
        }

        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
        //*  *  *           Ricostruisco la Entity aggiornata         *  *  *  *  *  *  *//
        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

        $codiceEntityAggiornata = "";
        $codiceEntityAggiornata .= $entityHead;

        $codiceEntityAggiornata .= $newManyToOne;
        $codiceEntityAggiornata .= $newNormalField;

        $codiceEntityAggiornata .= $campiSistema;

        $codiceEntityAggiornata .= $newOneToMany;
        $codiceEntityAggiornata .= $newManyToMany;

        $codiceEntityAggiornata .= $costruttore;

        $srcDir = $baseDir . "\\..\\src\\";

        $path = $srcDir . $bundle . "\\Entity\\" . $entityName . ".php";

        $this->file_force_contents($path, $codiceEntityAggiornata);

    }

    private function getEntityProperty($property, $stringOldProperties){

        // Eseguo l'explode della stringa contenente le properties della vecchia entity:
        $tmp = explode("private $" . $property . ";", $stringOldProperties);

        // Recupero la vecchia proprieta'
        $tmp = explode("/**", $tmp[0]);

        $entityProperty = "    /**" . end($tmp) . "private $".$property . ";\n";

        return $entityProperty;

    }

    private function addEntityHead($bundle, $entityName){


        $entityHead = "<?php \n";
        $entityHead .= "\n";
        $entityHead .= "namespace ".$bundle."\Entity;\n";
        $entityHead .= "\n";
        $entityHead .= "use Doctrine\ORM\Mapping as ORM;\n";
        $entityHead .= "use Doctrine\Common\Collections\ArrayCollection as Collection;\n";
        $entityHead .= "//serializer\n";
        $entityHead .= "use JMS\Serializer\Annotation\Exclude;\n";
        $entityHead .= "use JMS\Serializer\Annotation\Groups;\n";
        $entityHead .= "use JMS\Serializer\Annotation\MaxDepth;\n";
        $entityHead .= "\n";
        $entityHead .= "/**\n";
        $entityHead .= "* ".$entityName."\n";
        $entityHead .= "*\n";
        $entityHead .= "* @ORM\Table(name=\"z_".$this->camelCaseToUnderscore($entityName)."\")\n";
        $entityHead .= "* @ORM\Entity(repositoryClass=\"".$bundle."\Repository\\".$entityName."Repository\")\n";
        $entityHead .= "*/\n";
        $entityHead .= "class ".$entityName." {\n";
        $entityHead .= "\n";
        $entityHead .= "    /**\n";
        $entityHead .= "     * @var int\n";
        $entityHead .= "     *\n";
        $entityHead .= "     * @ORM\Column(name=\"id\", type=\"integer\")\n";
        $entityHead .= "     * @ORM\Id\n";
        $entityHead .= "     * @ORM\GeneratedValue(strategy=\"AUTO\")\n";
        $entityHead .= "     * @Groups({\"".$entityName."\", \"".$entityName.".id\"})\n";
        $entityHead .= "     */\n";
        $entityHead .= "    private \$id;\n";
        $entityHead .= "\n";

        return $entityHead;
    }

    private function addNormalField($field, $entityName){

        $normalField = "";
        $normalField .= "    /**\n";
        $normalField .= "     * @var ".$field["type"]."\n";
        $normalField .= "     *\n";
        $normalField .= "     * @ORM\Column(name=\"".$field["columnName"]."\", type=\"".$field["type"]."\"";
        if(array_key_exists('length',$field)){
            $normalField .= ", length=".$field['length'];
        }
        if(array_key_exists("nullable", $field)){
            $normalField .= ", nullable=true";
        }
        if(array_key_exists("unique", $field)) {
            $normalField .= ", unique=true";
        }

        $normalField .= ")\n";
        $normalField .= "     * @Groups({\"".$entityName."\", \"".$entityName.".".$field["fieldName"]."\"})\n";
        $normalField .= "     */\n";
        $normalField .= "    private $".$field["fieldName"].";\n";
        $normalField .= "\n";

        return $normalField;

    }

    private function addManyToOneField($field, $entityName){

        $manyToOneField = "";
        $manyToOneField .= "    /**\n";
        $manyToOneField .= "     * Molti ".$entityName." sono associati ad un unico ".$field["targetEntity"] . "\n";
        $manyToOneField .= "     *\n";
        $manyToOneField .= "     * @var ".$field["type"]."\n";
        $manyToOneField .= "     *\n";
        $manyToOneField .= "     * @ORM\ManyToOne(targetEntity=\"";
        $manyToOneField .= array_key_exists("namespace",$field) ? $field["namespace"] : $field["targetEntity"];
        $manyToOneField .= "\", inversedBy=\"".strtolower($entityName)."\")\n";
        $manyToOneField .= "     * @ORM\JoinColumn(name=\"" . $field["columnName"] . "\", referencedColumnName=\"id\")\n";
        $manyToOneField .= "     * @Groups({\"".$entityName.".".$field["fieldName"]."\"})\n";
        $manyToOneField .= "     */\n";
        $manyToOneField .= "    private $".$field["fieldName"].";\n";
        $manyToOneField .= "\n";

        return $manyToOneField;
    }

    private function addOneToManyField($field, $entityName){

        $oneToManyField = "";
        $oneToManyField .= "    /**\n";
        $oneToManyField .= "     *\n";
        $oneToManyField .= "     * Un ".$entityName." e' associato a molti ".$field["targetEntity"]."\n";
        $oneToManyField .= "     *\n";
        $oneToManyField .= "     * @ORM\OneToMany(targetEntity=\"";
        $oneToManyField .= array_key_exists("namespace",$field) ? $field["namespace"] : $field["targetEntity"];
        $oneToManyField .= "\", mappedBy=\"".lcfirst($entityName)."Id\")\n";
        $oneToManyField .= "     * @Groups({\"".$entityName.".".$field["fieldName"]."\"})\n";
        $oneToManyField .= "     */\n";
        $oneToManyField .= "    private $".$field["fieldName"].";\n";
        $oneToManyField .= "\n";

        return $oneToManyField;
    }

    private function addManyToMany($field, $entityName){

        $manyToManyField = "";
        $manyToManyField .= "    /**\n";
        $manyToManyField .= "     *\n";
        $manyToManyField .= "     * Molti ".$entityName." sono associato a molti ".$field["targetEntity"]."\n";
        $manyToManyField .= "     *\n";
        $manyToManyField .= "     * @ORM\ManyToMany(targetEntity=\"";
        $manyToManyField .= array_key_exists("namespace",$field) ? $field["namespace"] : $field["targetEntity"];
        $manyToManyField .= "\", inversedBy=\"". $field["inversedBy"] ."\")\n";
        $manyToManyField .= "     * @ORM\JoinTable(name=\"".strtolower($entityName)."_".strtolower($field["targetEntity"])."\")\n";
        $manyToManyField .= "     * @Groups({\"".$entityName.".".$field["fieldName"]."\"})\n";
        $manyToManyField .= "     */\n";
        $manyToManyField .= "    private $".$field["fieldName"].";\n";
        $manyToManyField .= "\n";

        return $manyToManyField;
    }

    private function addRelatedManyToMany($field, $entityName){

        $relatedManyToManyField = "";
        $relatedManyToManyField .= "    /**\n";
        $relatedManyToManyField .= "     *\n";
        $relatedManyToManyField .= "     * Molti ".$entityName." sono associato a molti ".$field["targetEntity"]."\n";
        $relatedManyToManyField .= "     *\n";
        $relatedManyToManyField .= "     * @ORM\ManyToMany(targetEntity=\"";
        $relatedManyToManyField .= array_key_exists("namespace",$field) ? $field["namespace"] : $field["targetEntity"];
        $relatedManyToManyField .= "\", mappedBy=\"". strtolower($entityName) ."\")\n";
        $relatedManyToManyField .= "     * @Groups({\"".$entityName.".".$field["fieldName"]."\"})\n";
        $relatedManyToManyField .= "     */\n";
        $relatedManyToManyField .= "    private $".$field["fieldName"].";\n";
        $relatedManyToManyField .= "\n";

        return $relatedManyToManyField;

    }

    private function addSystemFields(){

        $str = "    /**\n";
        $str .= "     * @var \DateTime\n";
        $str .= "     *\n";
        $str .= "     * @ORM\Column(name=\"time_insert\", type=\"datetime\", nullable=false)\n";
        $str .= "     * @Exclude\n";
        $str .= "     */\n";
        $str .= "    private \$timeInsert;\n";
        $str .= "\n";
        $str .= "    /**\n";
        $str .= "     * @var \DateTime\n";
        $str .= "     *\n";
        $str .= "     * @ORM\Column(name=\"time_delete\", type=\"datetime\", nullable=true)\n";
        $str .= "     * @Exclude\n";
        $str .= "     */\n";
        $str .= "    private \$timeDelete;\n";
        $str .= "\n";
        $str .= "    /**\n";
        $str .= "     * @var \DateTime\n";
        $str .= "     *\n";
        $str .= "     * @ORM\Column(name=\"time_action\", type=\"datetime\", nullable=false)\n";
        $str .= "     * @Exclude\n";
        $str .= "     */\n";
        $str .= "    private \$timeAction;\n";
        $str .= "\n";
        $str .= "    /**\n";
        $str .= "     * @var int\n";
        $str .= "     *\n";
        $str .= "     * @ORM\Column(name=\"user_action\", type=\"integer\", nullable=false)\n";
        $str .= "     * @Exclude\n";
        $str .= "     */\n";
        $str .= "    private \$userAction;\n";
        $str .= "\n";
        $str .= "    /**\n";
        $str .= "     * @var string\n";
        $str .= "     *\n";
        $str .= "     * @ORM\Column(name=\"status\", type=\"string\", length=1, nullable=false)\n";
        $str .= "     * @Exclude\n";
        $str .= "     */\n";
        $str .= "    private \$status;\n";
        $str .= "\n";

        return $str;
    }

    private function addConstructor($properties){

        $str = "    /**\n";
        $str .= "     * Constructor\n";
        $str .= "     */\n";
        $str .= "    public function __construct(){\n";
        $str .= "        \$this->timeInsert = new \DateTime('now');\n";
        $str .= "        \$this->timeAction = new \DateTime('now');\n";
        $str .= "        \$this->userAction = 0;\n";
        $str .= "        \$this->status = \"A\";\n";
        $str .= "\n";
        $str .= "        #####\n";
        foreach($properties as $property){
            $str .= "        \$this->".$property["fieldName"]." = new Collection();\n";
        }
        $str .= "    }\n";
        $str .= "\n";
        $str .= "\n";
        $str .= "}\n";

        return $str;
    }

    private function updateConstructor($costruttore, $oldProp){

        $property = $this->cutString('$', ";", $oldProp);

        $costruttore = str_replace("#####\n","#####\n        \$this->".$property." = new Collection();\n", $costruttore);

        return $costruttore;

    }

    private function checkIfPropertyHasJoin($property, $bundle){

        // Verifico se c'e' una relazione ManyToOne
        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
        if(strrpos($property, "ManyToOne")){

            // Recupero la property che fa JOIN nell'altra entity:
            $propertyToBeDeleted = $this->cutString('inversedBy="', '")', $property);

            // Recupero la "targetEntity" che e' legata in JOIN con la $propertyToBeDeleted:
            $joinedEntity = $this->cutString('targetEntity="', '", inversedBy', $property);

            $joinedEntity = str_replace("\\", "/", $joinedEntity);
            // Verifico se la stringa e' semplicemente il nome di una Entity (e dunque si trova
            // nello stesso bundle) oppure e' il namespace di una Entity (e dunque si trova in un altro bundle)
            $tmp3 = explode("/", $joinedEntity);

            if( count($tmp3) > 1 ){
                // Se l'array $joinedEntity ha piu' di un elemento, la stringa $joinedEntity contiene tutto il namespace
                $path = $joinedEntity;
            } else {
                // Altrimenti, la stringa contiene solamente il nome della entity
                $path = $bundle."/Entity/".$joinedEntity;
            }

            // Verifico se c'e' una relazione OneToMany
            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
        } else if(strrpos($property, "OneToMany")){

            // Recupero la property che fa JOIN nell'altra entity:
            $propertyToBeDeleted = $this->cutString('mappedBy="', '")', $property);

            // Recupero la "targetEntity" che e' legata in JOIN con la $propertyToBeDeleted:
            $joinedEntity = $this->cutString('targetEntity="', '", mappedBy', $property);

            $joinedEntity = str_replace("\\", "/", $joinedEntity);
            // Verifico se la stringa e' semplicemente il nome di una Entity (e dunque si trova
            // nello stesso bundle) oppure e' il namespace di una Entity (e dunque si trova in un altro bundle)
            $tmp3 = explode("/", $joinedEntity);

            if( count($tmp3) > 1 ){
                // Se l'array $joinedEntity ha piu' di un elemento, la stringa $joinedEntity contiene tutto il namespace
                $path = $joinedEntity;
            } else {
                // Altrimenti, la stringa contiene solamente il nome della entity
                $path = $bundle."/Entity/".$joinedEntity;
            }

            // Verifico se c'e' una relazione ManyToMany
            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
        } else if(strrpos($property, "ManyToMany")){

            if( strpos($property, "JoinTable") ) {
                // Recupero la property che fa JOIN nell'altra entity:
                $propertyToBeDeleted = $this->cutString('inversedBy="', '")', $property);

                // Recupero la "targetEntity" che e' legata in JOIN con la $propertyToBeDeleted:
                $joinedEntity = $this->cutString('targetEntity="', '", inversedBy', $property);

            } else {
                // Recupero la property che fa JOIN nell'altra entity:
                $propertyToBeDeleted = $this->cutString('mappedBy="', '")', $property);

                // Recupero la "targetEntity" che e' legata in JOIN con la $propertyToBeDeleted:
                $joinedEntity = $this->cutString('targetEntity="', '", mappedBy', $property);

            }

            $joinedEntity = str_replace("\\", "/", $joinedEntity);
            // Verifico se la stringa e' semplicemente il nome di una Entity (e dunque si trova
            // nello stesso bundle) oppure e' il namespace di una Entity (e dunque si trova in un altro bundle)
            $tmp3 = explode("/", $joinedEntity);

            if( count($tmp3) > 1 ){
                // Se l'array $joinedEntity ha piu' di un elemento, la stringa $joinedEntity contiene tutto il namespace
                $path = $joinedEntity;
            } else {
                // Altrimenti, la stringa contiene solamente il nome della entity
                $path = $bundle."/Entity/".$joinedEntity;
            }


            // Altrimenti non ci sono relazioni e restituisco null;
            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

        } else {
            $propertyToBeDeleted = null;
            $path = null;

        }

        $return = array(
            "path" => $path,
            "propertyToBeDeleted" => $propertyToBeDeleted);

        return $return;

    }

    private function removeJoinedProperty($baseDir, $oldProp, $bundle){

        // Se la trovo, verifico se la proprieta' aveva join:
        $response = $this->checkIfPropertyHasJoin($oldProp, $bundle);

        $path = $response["path"];
        $deleted = $response["propertyToBeDeleted"];

        if(!is_null($path)) {
            // Se aveva Join, verifico se l'Entity con cui ha la relazione esiste:
            // Recupero il path e il namespace della Entity di cui devo verificare l'esistenza
            $path = $baseDir."/../src/".$path.".php";
            // Verifico se il file esiste
            $exists = file_exists($path);

            if($exists){
                // Se esiste, rimuovo la proprieta' dalla entity legata con la join

                // Recupero preliminare del nome della Entity e del Bundle (informazioni contenute nel path)
                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                // Bundle
                $bundle = $this->cutString("src/", "/Entity", $path);

                // EntityName
                $entityName = $this->cutString("Entity/", ".php", $path);

                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                //*  *  Recupero la entity e la divido seguendo le "linee di cesura"   *  *  *  *//
                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

                // Recupero lo stream del file:
                $entityStream = file_get_contents($path, true);

                // Eseguo il comando di explode sulla entity:
                $entityExploded = explode("private \$id;", $entityStream);

                $entityHead = $entityExploded[0] . "private \$id;\n\n";
                $entityOldProperties = $entityExploded[1];
                $campiSistema = $this->addSystemFields();

                // Inizializzo il costruttore:
                $costruttore = $this->addConstructor(array());

                // Preparo il namespace della entity di cui devo recuperare le colonne:
                $entityNamespace = "\\".$bundle."\\Entity\\".$entityName;

                // Recupero le properties della entity:
                $entityProperties = $this->getEntityProperties($entityNamespace);

                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                //*  *  *  Preparo le stringhe che costituiranno la entity aggiornata  *  *  *  *//
                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

                $newNormalField = "";
                $newManyToOne = "";
                $newOneToMany = "";
                $newManyToMany = "";

                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                //*  *  *  *   Ciclo in cui cancello la proprieta' selezionata   *  *  *  *  *  *//
                //*  *  *  *          e lascio invariate tutte le altre          *  *  *  *  *  *//
                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

                foreach($entityProperties as $oldProperty){

                    // Eseguo l'explode della stringa contenente le properties della vecchia entity:
                    $oldProp = $this->getEntityProperty($oldProperty, $entityOldProperties);

                    $trovata = false;

                    // Verifico se la property i-ma e' stata cancellata
                    //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                    if($oldProperty == $deleted){
                        $trovata = true;
                    }

                    if($trovata){
                        continue;
                    }

                    // Altrimenti scrivo la proprieta' così com'era:
                    //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

                    // Verifico se la precedente proprieta' era un campo "normale", oppure un campo con relazione (O-M, M-O, M-M):
                    if(strrpos($oldProp, "ManyToOne")){
                        $newManyToOne .= $oldProp ."\n";

                    } else if(strrpos($oldProp, "OneToMany")){
                        $newOneToMany .= $oldProp . "\n";

                        $costruttore = $this->updateConstructor($costruttore, $oldProp);

                    } else if(strrpos($oldProp, "ManyToMany")){
                        $newManyToMany .= $oldProp . "\n";

                        $costruttore = $this->updateConstructor($costruttore, $oldProp);

                    } else {
                        $newNormalField .= $oldProp . "\n";
                    }
                }

                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                //*  *  *           Ricostruisco la Entity aggiornata         *  *  *  *  *  *  *//
                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

                $codiceEntityAggiornata = "";
                $codiceEntityAggiornata .= $entityHead;

                $codiceEntityAggiornata .= $newManyToOne;
                $codiceEntityAggiornata .= $newNormalField;

                $codiceEntityAggiornata .= $campiSistema;

                $codiceEntityAggiornata .= $newOneToMany;
                $codiceEntityAggiornata .= $newManyToMany;

                $codiceEntityAggiornata .= $costruttore;


                $edit = file_put_contents($path, $codiceEntityAggiornata);

                if($edit){
                    // Tutto ok: relazione nell'altro file rimossa!
                    return true;
                } else {
                    // Errore nella modifica del file!
                    return false;
                }

            }
            // Tutto ok: il file dal quale voglio rimuovere la relazione di JOIN non esiste ancora!
            else return true;
        }
        // Tutto ok: la property non aveva join
        else return true;
    }

    private function generateJoinedEntityAndRepository($baseDir, $path, $field, $joiningEntity, $joiningNamespace){

        // Inizializzo la proprieta' "inversa" che la entity che sto creando deve contenere:
        $field = $this->getReversedProperty($field, $joiningEntity, $joiningNamespace);


        $bundle = $this->cutString("src/", "/Entity", $path);
        $entityName = $this->cutString("Entity/", ".php", $path);;

        // Inizializzo la "testa" della Entity (namespace, use, id)
        $entityHead = $this->addEntityHead($bundle, $entityName);

        // Inizializzo il costruttore:
        if($field["relation"] == "M"){
            $costruttore = $this->addConstructor(array());
        } else {
            $costruttore = $this->addConstructor(array($field));
        }

        // Inizializzo i campi di sistema
        $campiSistema = $this->addSystemFields();

        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
        //*  *  *  Preparo le stringhe che costituiranno la entity aggiornata  *  *  *  *//
        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

        $newManyToOne = "";
        $newOneToMany = "";
        $newManyToMany = "";

        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
        //*  *  *  *  *  *   Aggiungo alla Entity la nuova proprieta'    *  *  *  *  *  *//
        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

        // Aggiungo il campo field:
        if ($field["relation"] == "M") {
            $newManyToOne .= $this->addManyToOneField($field, $entityName);
            // Aggiungo il campo alla property statica
        }

        if ($field["relation"] == "O") {
            $newOneToMany .= $this->addOneToManyField($field, $entityName);
            // Aggiungo il campo alla property statica
        }

        if ($field["relation"] == "MM") {
            $newManyToMany .= $this->addRelatedManyToMany($field, $entityName);
            // Aggiungo il campo alla property statica
        }

        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
        //*  *  *         Ricostruisco la Entity appena creata        *  *  *  *  *  *  *//
        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

        $codiceJoinedEntity = $entityHead;

        $codiceJoinedEntity .= $newManyToOne;
        $codiceJoinedEntity .= $campiSistema;
        $codiceJoinedEntity .= $newOneToMany;
        $codiceJoinedEntity .= $newManyToMany;

        $codiceJoinedEntity .= $costruttore;

        $path = str_replace("/", "\\", $path);

        $this->file_force_contents($path, $codiceJoinedEntity);

        if($field["relation"] == "M") {
            $this->generateRepository($baseDir, $bundle, $entityName, array($field) );

        } else {
            $this->generateRepository($baseDir, $bundle, $entityName, null);
        }

    }

    private function addJoinedProperty($path, $joinedProperty, $joiningEntity, $joiningNamespace){

        $bundle = $this->cutString("src/", "/Entity", $path);
        $entityName = $this->cutString("/Entity/", ".php", $path);

        // JoinedProperty e' la proprieta' nella entity di partenza.
        // Tramite questa informazione, devo costruire la property della entity di arrivo (questa).
        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
        $field = $this->getReversedProperty($joinedProperty, $joiningEntity, $joiningNamespace);

        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
        //*  *  Recupero la entity e la divido seguendo le "linee di cesura"   *  *  *  *//
        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

        // Recupero lo stream del file:
        $entityStream = file_get_contents($path, true);

        // Eseguo il comando di explode sulla entity:
        $entityExploded = explode("private \$id;", $entityStream);

        $entityHead = $entityExploded[0] . "private \$id;\n\n";
        $entityOldProperties = $entityExploded[1];
        $campiSistema = $this->addSystemFields();

        // Inizializzo il costruttore:
        if($field["relation"] == "M"){
            $costruttore = $this->addConstructor(array());
        } else {
            $costruttore = $this->addConstructor(array($field));
        }

        // Preparo il namespace della entity di cui devo recuperare le colonne:
        $entityNamespace = "\\" . $bundle . "\\Entity\\" . $entityName;

        // Recupero le properties della entity:
        $entityProperties = $this->getEntityProperties($entityNamespace);

        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
        //*  *  *  Preparo le stringhe che costituiranno la entity aggiornata  *  *  *  *//
        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

        $newNormalField = "";
        $newManyToOne = "";
        $newOneToMany = "";
        $newManyToMany = "";

        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
        //*  *  *  *        Opero (in un ciclo foreach) le modifiche     *  *  *  *  *  *//
        //*  *  *  *  *   alle proprieta' gia' presenti nella Entity     *  *  *  *  *  *//
        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

        $trovata = false;

        foreach($entityProperties as $oldProperty){

            // Eseguo l'explode della stringa contenente le properties della vecchia entity:
            $oldProp = $this->getEntityProperty($oldProperty, $entityOldProperties);

            if(!$trovata) {

                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                // Verifico se e' stato modificato un campo MANY TO ONE
                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                if ($oldProperty == $field['fieldName'] && $field["relation"] == "M") {

                    // Aggiungo alla stringa $newManyToOne il campo aggiornato
                    $newManyToOne .= $this->addManyToOneField($field, $entityName);

                    $trovata = true;

                    // Elimino la variabile "field"
                    unset($field);
                }

                if ($trovata) {
                    continue;
                }

                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                // Verifico se e' stato modificato un campo ONE TO MANY
                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                if ($oldProperty == $field['fieldName'] && $field["relation"] == "O") {

                    // Aggiungo alla stringa $newOneToMany il campo aggiornato
                    $newOneToMany .= $this->addOneToManyField($field, $entityName);

                    $trovata = true;

                    // Elimino la variabile "field"
                    unset($field);
                }

                if ($trovata) {
                    continue;
                }

                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                // Verifico se e' stato modificato un campo MANY TO MANY
                //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
                if ($oldProperty == $field['fieldName'] && $field["relation"] == "MM") {

                    // Aggiungo alla stringa $newManyToMany il campo aggiornato
                    $newManyToMany .= $this->addRelatedManyToMany($field, $entityName);

                    $trovata = true;
                    // Elimino la variabile "field"
                    unset($field);
                }

                if ($trovata) {
                    continue;
                }

            }


            // Altrimenti scrivo la proprieta' così com'era:
            //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

            // Verifico se la precedente proprieta' era un campo "normale", oppure un campo con relazione (O-M, M-O, M-M):
            if(strrpos($oldProp, "ManyToOne")){
                $newManyToOne .= $oldProp ."\n";

            } else if(strrpos($oldProp, "OneToMany")){
                $newOneToMany .= $oldProp . "\n";

                $costruttore = $this->updateConstructor($costruttore, $oldProp);

            } else if(strrpos($oldProp, "ManyToMany")){
                $newManyToMany .= $oldProp . "\n";

                $costruttore = $this->updateConstructor($costruttore, $oldProp);

            } else {
                $newNormalField .= $oldProp . "\n";
            }
        }

        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
        //*  *  *  *  *  *   Aggiungo alla Entity la nuova proprieta'    *  *  *  *  *  *//
        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

        // Aggiungo il campo field se non e' stato eseguito l'unset alla variabile "field"
        if(isset($field)) {
            if ($field["relation"] == "M") {
                $newManyToOne .= $this->addManyToOneField($field, $entityName);
            }

            if ($field["relation"] == "O") {
                $newOneToMany .= $this->addOneToManyField($field, $entityName);
            }

            if ($field["relation"] == "MM") {
                $newManyToMany .= $this->addRelatedManyToMany($field, $entityName);
            }
        }

        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//
        //*  *  *           Ricostruisco la Entity aggiornata         *  *  *  *  *  *  *//
        //*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *//

        $codiceEntityAggiornata = "";
        $codiceEntityAggiornata .= $entityHead;

        $codiceEntityAggiornata .= $newManyToOne;
        $codiceEntityAggiornata .= $newNormalField;

        $codiceEntityAggiornata .= $campiSistema;

        $codiceEntityAggiornata .= $newOneToMany;
        $codiceEntityAggiornata .= $newManyToMany;

        $codiceEntityAggiornata .= $costruttore;


        $edit = file_put_contents($path, $codiceEntityAggiornata);

        if ($edit) {
            // Tutto ok: relazione nell'altro file rimossa!
            return true;
        } else {
            // Errore nella modifica del file!
            return false;
        }
    }

    public function camelCaseToUnderscore($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    private function cutString($limiteSx, $limiteDx, $string){

        $tmp1 = explode($limiteSx, $string);
        $tmp2 = explode($limiteDx, $tmp1[1]);
        $string = $tmp2[0];

        return $string;
    }

    private function getPathAndTargetNamespace($field, $baseDir, $bundle, $entityName){
        if(array_key_exists("namespace",$field)){
            $path = $baseDir."/../src/".$field["namespace"] . ".php";
            $path = str_replace("\\", "/", $path);
            $joinedNamespace = $bundle."\\Entity\\".$entityName;
        } else {
            $path = $baseDir."/../src/".$bundle."/Entity/".$field["targetEntity"] . ".php";
            $joinedNamespace = $entityName;
        }

        return array(
            "path" => $path,
            "joinedNamespace" => $joinedNamespace);

    }

    private function getReversedProperty($joinedProperty, $joinedEntity, $joinedNamespace){
        if( $joinedProperty['relation'] == "M" ) {
            $reversedProperty = array(
                "columnName" => strtolower($joinedEntity),
                "fieldName" => strtolower($joinedEntity),
                "relation" => "O",
                "targetEntity" => $joinedEntity,
                "namespace" => $joinedNamespace,
            );
        } else if( $joinedProperty['relation'] == "O" ){
            $reversedProperty = array(
                "columnName" => $this->camelCaseToUnderscore(lcfirst($joinedEntity)."Id"),
                "fieldName" => lcfirst($joinedEntity)."Id",
                "type" => "integer",
                "relation" => "M",
                "targetEntity" => $joinedEntity,
                "namespace" => $joinedNamespace,
            );
        } else {
            // Altrimenti e' una MANY TO MANY
            $reversedProperty = array(
                "columnName" => strtolower($joinedEntity),
                "fieldName" => strtolower($joinedEntity),
                "relation" => "MM",
                "targetEntity" => $joinedEntity,
                "namespace" => $joinedNamespace,
            );
        }

        return $reversedProperty;
    }

    private function getEntityProperties($entityNamespace){

        $foo = new $entityNamespace();

        $reflect = new \ReflectionClass($foo);
        $props   = $reflect->getProperties(\ReflectionProperty::IS_PRIVATE);

        $entityProperties = array();
        foreach ($props as $prop) {
            // Non recupero la proprieta' se fa parte dei campi di sistema o e' l'id:
            if($prop->getName() == "id" || $prop->getName() == "timeInsert" || $prop->getName() == "timeDelete" || $prop->getName() == "timeAction" ||  $prop->getName() == "userAction" ||  $prop->getName() == "status"){
                continue;
            }

            $entityProperties[] = $prop->getName();
        }

        return $entityProperties;

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

        file_put_contents("$dir\\$fileName", $contents);

    }

}

