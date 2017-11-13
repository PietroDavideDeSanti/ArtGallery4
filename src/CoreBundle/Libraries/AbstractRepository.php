<?php

namespace CoreBundle\Libraries;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use CoreBundle\Utils\status;


abstract class AbstractRepository extends EntityRepository {

    static $getOneOrNullResult = 1;
    static $singleResult = 2;
    static $getResult = 3;
    static $conventionalParametersCounter = "AAA";
    
    static $inner = " JOIN ";
    static $left = " LEFT JOIN ";
    static $right = " RIGHT JOIN ";

    /**
     * @param array $arrEntities
     * @param array $arrJoins
     * @param array $arrParams
     * @param array $arrJoinsCondition
     * @param array $conditionValues
     * @param int $resultType
     * @return Entity instance of $arrEntities[0] hydratate for each $arrEntities[>0]
     */
    public function get_one ($arrEntities, $arrJoins, $arrParams, $arrJoinsCondition = null, $conditionValues = array(), $resultType = 1) {
        $only_entities = implode(":", $arrEntities);
        $arr_tmp = explode(":", $only_entities);
        $arr_only_entities = array();
        for($i=1; $i < count($arr_tmp); $i=$i+2) {
            $arr_only_entities[] = $arr_tmp[$i];
        }
        $join = "";
        foreach($arrJoins as $key=>$value) {
            $join .= $key." ".$value;
            if(isset($arrJoinsCondition[$key])) {
                $join .= " WITH ".$arrJoinsCondition[$key];
            }
        }
        $where = '';
        if (count($arrParams) > 0) {
            $where = " WHERE ";
            $n = 0;
            foreach($arrParams as $value) {
                if($n == 0) {
                    $and = "";
                } else {
                    $and = " AND ";
                }
                $where .= $and.$value;
                $n++;
            }        
        }
        $query = $this->getEntityManager()
            ->createQuery('SELECT '.implode(", ", $arr_only_entities).' FROM '.$arrEntities[0].' '.$arr_only_entities[0].$join.$where);
//        echo $query->getSQL();exit();
        
        $n = self::$conventionalParametersCounter;
        foreach($conditionValues as $value) {
                $query->setParameter(":".$n, $value);
                $n++;
        }
        
        return $this->queryExecute($query, $resultType);
        
        //return $query->getSingleResult();     
    }

    /**
     * @param array $arrEntities
     * @param array $arrJoins
     * @param array $arrParams
     * @param array $arrJoinsCondition
     * @param array $conditionValues
     * @param int $resultType
     */
    public function get_one_active ($arrEntities, $arrJoins, $arrParams, $arrJoinsCondition = null, $conditionValues = array(), $resultType = 1) {
        $only_entities = implode(":", $arrEntities);
        $arr_tmp = explode(":", $only_entities);
        $arr_only_entities = array();
        for($i=1; $i < count($arr_tmp); $i=$i+2) {
            $arr_only_entities[] = $arr_tmp[$i];
        }
        $join = "";
        foreach($arrJoins as $key=>$value) {
            $join .= $key." ".$value;
            if(isset($arrJoinsCondition[$key])) {
                $join .= " WITH ".$value.".status = 'A' ".(isset($arrJoinsCondition[$key]) ? " AND ".$arrJoinsCondition[$key] : null);
            }
        }
        $where = " WHERE ".$arr_only_entities[0].".status = 'A'";
        if (count($arrParams) > 0) {
            foreach($arrParams as $value) {
                $and = " AND ";
                $where .= $and.$value;
            }
        }

        $query = $this->getEntityManager()
            ->createQuery('SELECT '.implode(", ", $arr_only_entities).' FROM '.$arrEntities[0].' '.$arr_only_entities[0].$join.$where);

//        echo $query->getSQL();

        $n = self::$conventionalParametersCounter;
        foreach($conditionValues as $value) {
            $query->setParameter(":".$n, $value);
            $n++;
        }

        return $this->queryExecute($query, $resultType);

        //return $query->getSingleResult();
    }


    /**
     * @param array $arrEntities
     * @param array $arrJoins
     * @param array $arrParams
     * @param array $arrJoinsCondition
     * @param array $conditionValues
     * @param $fieldToMax
     * @return Entity instance of $arrEntities[0] hydratate for each $arrEntities[>0]
     */
    public function get_max ($arrEntities, $arrJoins, $arrParams, $arrJoinsCondition = null, $conditionValues = array(), $fieldToMax) {
        $resultType = self::$singleResult;
        $only_entities = implode(":", $arrEntities);
        $arr_tmp = explode(":", $only_entities);
        $arr_only_entities = array();
        for($i=1; $i < count($arr_tmp); $i=$i+2) {
            $arr_only_entities[] = $arr_tmp[$i];
        }
        $join = "";
        foreach($arrJoins as $key=>$value) {
            $join .= $key." ".$value;
            if(isset($arrJoinsCondition[$key])) {
                $join .= " WITH ".$arrJoinsCondition[$key];
            }
        }
        $where = '';
        if (count($arrParams) > 0) {
            $where = " WHERE ";
            $n = 0;
            foreach($arrParams as $value) {
                if($n == 0) {
                    $and = "";
                } else {
                    $and = " AND ";
                }
                $where .= $and.$value;
                $n++;
            }        
        }
        $query = $this->getEntityManager()
            ->createQuery('SELECT MAX('.$arr_only_entities[0].'.'.$fieldToMax.') as result FROM '.$arrEntities[0].' '.$arr_only_entities[0].$join.$where);
//        echo $query->getSQL();die();
        
        $n = self::$conventionalParametersCounter;
        foreach($conditionValues as $value) {
                $query->setParameter(":".$n, $value);
                $n++;
        }
        
        return $this->queryExecute($query, $resultType);
    }

    /**
     * @param array $arrEntities
     * @param array $arrJoins
     * @param array $arrParams
     * @param array $arrJoinsCondition
     * @param array $conditionValues
     * @return Entity instance of $arrEntities[0] hydratate for each $arrEntities[>0]
     */
    public function get_count ($arrEntities, $arrJoins, $arrParams, $arrJoinsCondition = null, $conditionValues = array()) {
        $resultType = self::$singleResult;
        $only_entities = implode(":", $arrEntities);
        $arr_tmp = explode(":", $only_entities);
        $arr_only_entities = array();
        for($i=1; $i < count($arr_tmp); $i=$i+2) {
            $arr_only_entities[] = $arr_tmp[$i];
        }
        $join = "";
        foreach($arrJoins as $key=>$value) {
            $join .= $key." ".$value;
            if(isset($arrJoinsCondition[$key])) {
                $join .= " WITH ".$arrJoinsCondition[$key];
            }
        }
        $where = '';
        if (count($arrParams) > 0) {
            $where = " WHERE ";
            $n = 0;
            foreach($arrParams as $value) {
                if($n == 0) {
                    $and = "";
                } else {
                    $and = " AND ";
                }
                $where .= $and.$value;
                $n++;
            }        
        }
        $query = $this->getEntityManager()
            ->createQuery('SELECT COUNT('.$arr_only_entities[0].'.id) as result FROM '.$arrEntities[0].' '.$arr_only_entities[0].$join.$where);
//        echo $query->getSQL();die();
        
        $n = self::$conventionalParametersCounter;
        foreach($conditionValues as $value) {
                $query->setParameter(":".$n, $value);
                $n++;
        }
        
        return $this->queryExecute($query, $resultType);
    }

    /**
     * @param array $arrEntities
     * @param array $arrJoins
     * @param array $arrParams
     * @param array $arrJoinsCondition
     * @param array $conditionValues
     * @param array $arrGroupBy
     * @return Entity instance of $arrEntities[0] hydratate for each $arrEntities[>0]
     */
    public function get_count_group_by ($arrEntities, $arrJoins, $arrParams, $arrJoinsCondition = null, $conditionValues = array(), $arrGroupBy = array()) {
        $resultType = self::$getResult;
        $only_entities = implode(":", $arrEntities);
        $arr_tmp = explode(":", $only_entities);
        $arr_only_entities = array();
        for($i=1; $i < count($arr_tmp); $i=$i+2) {
            $arr_only_entities[] = $arr_tmp[$i];
        }
        $join = "";
        foreach($arrJoins as $key=>$value) {
            $join .= $key." ".$value;
            if(isset($arrJoinsCondition[$key])) {
                $join .= " WITH ".$arrJoinsCondition[$key];
            }
        }
        $where = '';
        if (count($arrParams) > 0) {
            $where = " WHERE ";
            $n = 0;
            foreach($arrParams as $value) {
                if($n == 0) {
                    $and = "";
                } else {
                    $and = " AND ";
                }
                $where .= $and.$value;
                $n++;
            }        
        }
        $strGroupBy = '';
        $selectGroupBy = '';
        if (count($arrGroupBy) > 0) {
            $strGroupBy = " GROUP BY ";
            $n = 0;
            $k = "";
            foreach($arrGroupBy as $groupBy) {
                if($n == 0) {
                    $and = "";
                } else {
                    $and = " , ";
                }
                $strGroupBy .= $and.$groupBy;
                $selectGroupBy = strpos(strtolower($groupBy), "id") > 0 ? $and." IDENTITY(".$groupBy.") as name".$k : $and.$groupBy." as name".$k;
                $n++;
                $k = $k+1;
            }        
        }
        $query = $this->getEntityManager()
            ->createQuery('SELECT COUNT('.$arr_only_entities[0].'.id) as result FROM '.$arrEntities[0].' '.$arr_only_entities[0].$join.$where.$strGroupBy);
//        echo $query->getSQL();die();
        
        $n = self::$conventionalParametersCounter;
        foreach($conditionValues as $value) {
                $query->setParameter(":".$n, $value);
                $n++;
        }
        
        return $this->queryExecute($query, $resultType);
    }


    /**
     * @param array $arrEntities
     * @param array $arrJoins
     * @param array $arrParams
     * @param array $arrJoinsCondition
     * @param array $conditionValues
     * @param $page
     * @param $per_page
     * @param $sort_by
     * @param array $arrGroupBy
     * @return ArrayCollection
     */
    public function get_all ($arrEntities, $arrJoins, $arrParams, $arrJoinsCondition = null, $conditionValues = array(), $page = null, $per_page = null, $sort_by = null, $arrGroupBy = array()) {
        $resultType = self::$getResult;
        $only_entities = implode(":", $arrEntities);
        $arr_tmp = explode(":", $only_entities);
        $arr_only_entities = array();
        for($i=1; $i < count($arr_tmp); $i=$i+2) {
            $arr_only_entities[] = $arr_tmp[$i];
        }
        $join = "";
        foreach($arrJoins as $key=>$value) {
            $join .= " ".$key." ".$value.(isset($arrJoinsCondition[$key]) ? " WITH ".$arrJoinsCondition[$key] : null);
        }
        $where = '';
        if (count($arrParams) > 0) {
            $where = " WHERE ";
            $n = 0;
            foreach($arrParams as $value) {
                if($n == 0) {
                    $and = "";
                } else {
                    $and = " AND ";
                }
                $where .= $and.$value;
                $n++;
            }        
        }
        
        $strGroupBy = '';
        $selectGroupBy = '';
        if (count($arrGroupBy) > 0) {
            $strGroupBy = " GROUP BY ";
            $n = 0;
            $k = "";
            foreach($arrGroupBy as $groupBy) {
                if($n == 0) {
                    $and = "";
                } else {
                    $and = " , ";
                }
                $strGroupBy .= $and.$groupBy;
                $selectGroupBy = strpos(strtolower($groupBy), "id") > 0 ? $and.", IDENTITY(".$groupBy.") as name".$k : $and.$groupBy." as name".$k;
                $n++;
                $k = $k+1;
            }        
        }        
        
        
        // order by
        $sort = "";
        if(count($sort_by) > 0) {
            $sort .= " ORDER BY ";
            foreach($sort_by as $field => $order) {
                $sort .= $field.' '.$order.", ";
            }
            $sort = substr($sort, 0, strlen($sort) -2);
        }
        $query = $this->getEntityManager()
            ->createQuery('SELECT '.implode(", ", $arr_only_entities).' FROM '.$arrEntities[0].' '.$arr_only_entities[0].$join.$where.$strGroupBy.$sort);
//        echo $query->getSQL();
        
        $n = self::$conventionalParametersCounter;
        foreach($conditionValues as $value) {
                $query->setParameter(":".$n, $value);
                $n++;
        }

        //paginazione
        return $this->queryPaginator($query, $resultType, $page, $per_page);
    }


    /**
     * @param array $arrEntities
     * @param array $arrJoins
     * @param array $arrParams
     * @param array $arrJoinsCondition
     * @param array $conditionValues
     * @param $page
     * @param $per_page
     * @param $sort_by
     * @param array $arrGroupBy
     * @return ArrayCollection
     */
    public function get_all_active ($arrEntities, $arrJoins, $arrParams, $arrJoinsCondition = null, $conditionValues = array(), $page = null, $per_page = null, $sort_by = null, $arrGroupBy = array()) {
        $resultType = self::$getResult;
        $only_entities = implode(":", $arrEntities);
        $arr_tmp = explode(":", $only_entities);
        $arr_only_entities = array();
        for($i=1; $i < count($arr_tmp); $i=$i+2) {
            $arr_only_entities[] = $arr_tmp[$i];
        }
        $join = "";
        foreach($arrJoins as $key=>$value) {
            $join .= " ".$key." ".$value." WITH ".$value.".status = 'A' ".(isset($arrJoinsCondition[$key]) ? " AND ".$arrJoinsCondition[$key] : null);
        }
        $where = " WHERE ".$arr_only_entities[0].".status = 'A'";
        if (count($arrParams) > 0) {
            foreach($arrParams as $value) {
                $and = " AND ";
                $where .= $and.$value;
            }
        }

        $strGroupBy = '';
        $selectGroupBy = '';
        if (count($arrGroupBy) > 0) {
            $strGroupBy = " GROUP BY ";
            $n = 0;
            $k = "";
            foreach($arrGroupBy as $groupBy) {
                if($n == 0) {
                    $and = "";
                } else {
                    $and = " , ";
                }
                $strGroupBy .= $and.$groupBy;
                $selectGroupBy = strpos(strtolower($groupBy), "id") > 0 ? $and.", IDENTITY(".$groupBy.") as name".$k : $and.$groupBy." as name".$k;
                $n++;
                $k = $k+1;
            }
        }


        // order by
        $sort = "";
        if(count($sort_by) > 0) {
            $sort .= " ORDER BY ";
            foreach($sort_by as $field => $order) {
                $sort .= $field.' '.$order.", ";
            }
            $sort = substr($sort, 0, strlen($sort) -2);
        }
        $query = $this->getEntityManager()
            ->createQuery('SELECT '.implode(", ", $arr_only_entities).' FROM '.$arrEntities[0].' '.$arr_only_entities[0].$join.$where.$strGroupBy.$sort);
//        echo $query->getSQL();die();

        $n = self::$conventionalParametersCounter;
        foreach($conditionValues as $value) {
            $query->setParameter(":".$n, $value);
            $n++;
        }

        //paginazione
        return $this->queryPaginator($query, $resultType, $page, $per_page);
    }


    /**
     * @param $entity
     * @return Entity
     */
    public function freeze_entity($entity) {
        $em = $this->getEntityManager();
        $entity->setTimeDelete(new \DateTime('now'));
        $entity->setTimeAction(new \DateTime('now'));
        $entity->setStatus(status::$cancelled);
        $em->persist($entity);
        $em->flush();  
        return $entity;
    }

    /**
     * @param $entity
     * @return Entity
     */
    public function suspend_entity($entity) {
        $em = $this->getEntityManager();
        $entity->setTimeAction(new \DateTime('now'));
        $entity->setStatus(status::$suspended);
        $em->persist($entity);
        $em->flush();  
        return $entity;
    }

    /**
     * @param $entity
     * @return Entity
     */
    public function eliminate_entity($entity) {
        $em = $this->getEntityManager();
        $entity->setTimeDelete(new \DateTime('now'));
        $entity->setTimeAction(new \DateTime('now'));
        $entity->setStatus(status::$eliminated);
        $em->persist($entity);
        $em->flush();
        return $entity;
    }

    /**
     * @param $entity
     * @return Entity
     */
    public function transaction_entity($entity) {
        $em = $this->getEntityManager();
        $entity->setTimeAction(new \DateTime('now'));
        $entity->setStatus(status::$transaction);
        $em->persist($entity);
        $em->flush();  
        return $entity;
    }

    /**
     * @param $entity
     * @return Entity
     */
    public function unfreeze_entity($entity) {
        $em = $this->getEntityManager();
        $entity->setTimeDelete(null);
        $entity->setTimeAction(new \DateTime('now'));
        $entity->setStatus(status::$active);
        $em->persist($entity);
        $em->flush();  
        return $entity;
    }

    /**
     * @param $entity
     * @return Entity
     */
    public function update_entity($entity) {
        $em = $this->getEntityManager();
        $entity->setTimeAction(new \DateTime('now'));
        $user = !empty($entity->getUserAction()) ? $entity->getUserAction() : 0;
        $entity->setUserAction($user);
        $em->persist($entity);
        $em->flush();
        return $entity;
    }

    /**
     * @param $entity
     * @return Entity
     */
    public function insert_entity($entity) {
        $em = $this->getEntityManager();
        $entity->setTimeAction(new \DateTime('now'));
        $user = !empty($entity->getUserAction()) ? $entity->getUserAction() : 0;
        $entity->setUserAction($user);
        $em->persist($entity);
        $em->flush();
        return $entity;
    }

    public function delete_entity($entity){
        $em = $this->getEntityManager();
        $em->remove($entity);
        $em->flush();

    }

    /**
     * @param array $arrEntities
     * @param array $parrJoins
     * @param array $arrParams
     * @param array $parrJoinsCondition
     * @param $conditionValues
     * @param $datatable
     * @param array $arrGroupBy
     * @return mixed
     */
    public function datatable($arrEntities, $parrJoins, $arrParams, $parrJoinsCondition, $conditionValues, $datatable, $arrGroupBy=array()) {
        //GESTIONE CONDITION
        $arrFilter = array();

        $entityFrom = explode(":", $arrEntities[0]);
        $entityFrom = $entityFrom[1];

        foreach ($datatable["columns"] as $column) {
//            if(!empty($column["search"]["value"])) {
            if($column["search"]["value"] != "") {
                $arrName = explode("__", $column["name"]);
                $filter = array();
                $filter["field"] = $arrName[0].".".$arrName[1];
                $filter["value"] = $column["search"]["value"];
                $filter["type"] = isset($column["type"]) ? $column["type"] : null;
                $arrFilter[$arrName[0]][] = $filter;
                //se il filtro è sulla entity del from va subito risolta in arrParams
                if (strtolower($entityFrom) == strtolower($arrName[0])) {
                    foreach ($arrFilter[$arrName[0]] as $filter) {
                        switch ($filter["type"]) {
                            case "daterange": //eseguo ricerca per range di date
                                //creo array di date
                                $daterange = explode(" - ", $column["search"]["value"]);
                                if (count($daterange) === 2) {
                                    //formatto data
                                    $daterange[0] = substr($daterange[0], 6) . "-" . substr($daterange[0], 3, 2) . "-" . substr($daterange[0], 0, 2);
                                    $daterange[1] = substr($daterange[1], 6) . "-" . substr($daterange[1], 3, 2) . "-" . substr($daterange[1], 0, 2);
                                    $arrParams[] = $arrName[0] . "." . $arrName[1] . " >= '" . $daterange[0] . "' AND " . $arrName[0] . "." . $arrName[1] . " <= '" . $daterange[1] . "'";
                                }
                                break;
                            default:
                                //gestione apici e like
                                if (!is_numeric($column["search"]["value"])) {
                                    //verifico se è una multiselect per recuperare tutti i valori
                                    $multiselect = explode("§", $column["search"]["value"]);
                                    $valueMultiselect = array();
                                    if(count($multiselect) > 1){
                                        foreach($multiselect as $valueToSearch){
                                            $valueMultiselect[] = "'".$valueToSearch."'";
                                        }
                                        $arrParams[] = " (".$arrName[0] . "." . $arrName[1] ." IN(".implode(", ", $valueMultiselect).")) ";
                                    } else {
                                        foreach($multiselect as $valueToSearch){
                                            $valueMultiselect[] = $arrName[0] . "." . $arrName[1] ." LIKE '%" . $valueToSearch . "%'";
                                        }
                                        $arrParams[] = " (".implode(" OR ", $valueMultiselect).") ";

                                    }
//                                    $arrParams[] = $arrName[0] . "." . $arrName[1] . " LIKE '%" . $column["search"]["value"] . "%'";
                                } else {
                                    if (!$filter["type"]) {
                                        $arrParams[] = $arrName[0] . "." . $arrName[1] . " = '" . $column["search"]["value"] . "'";
                                    } else {
                                        $arrParams[] = $arrName[0] . "." . $arrName[1] . " " . $filter["type"] . " " . $column["search"]["value"];
                                    }
                                }
                                break;
                        }
                    }
                }
            }
        }


        //nuovi array da ricostruiore da capo per conservare inalterato l'ordine di esecuzione delle join
        $arrJoins = array();
        $arrJoinsCondition = array();

        foreach ($parrJoins as $join_key => $join_value) {
            if(isset($arrFilter[$join_value])) {
                //TRC 20170613 - se ci sta qualche vincolo sulla join questa deve per forza diventare una inner join
                $arrJoins[str_replace("LEFT", "INNER", $join_key)] = $join_value;
                // end fix

                if(isset($parrJoinsCondition[$join_key])) {
                   $strCondition = $parrJoinsCondition[$join_key];
                   $AND =  " AND ";
                } else {
                    $strCondition = "";
                    $AND = "";
                }
                $strNewCondition = "";
                foreach ($arrFilter[$join_value] as $filter) {
                    $strNewCondition = strlen($strNewCondition) > 0 ? $strNewCondition." AND " : $strNewCondition;
                    //gestione apici e like e datarange
                    switch ($filter['type']) {
                        case "daterange": //eseguo ricerca per range di date
                            //creo array di date
                            $daterange = explode(" - ", $filter['value']);
                            if (count($daterange) === 2) {
                                //formatto data
                                $daterange[0] = substr($daterange[0], 6) . "-" . substr($daterange[0], 3, 2) . "-" . substr($daterange[0], 0, 2);
                                $daterange[1] = substr($daterange[1], 6) . "-" . substr($daterange[1], 3, 2) . "-" . substr($daterange[1], 0, 2);
                                $strNewCondition .= $filter["field"] . " >= '" . $daterange[0] . "' AND " . $filter["field"] . " <= '" . $daterange[1] . "'";
                            }
                            break;
                        default:
                            //gestione apici e like
                            if (!is_numeric($filter["value"])) {
                                //verifico se è una multiselect per recuperare tutti i valori
                                $multiselect = explode("§", $filter["value"]);
                                $valueMultiselect = array();
                                if(count($multiselect) > 1){
                                    foreach($multiselect as $valueToSearch){
                                        $valueMultiselect[] = "'".$valueToSearch."'";
                                    }
                                    $strNewCondition = " (".$filter["field"] ." IN(".implode(", ", $valueMultiselect).")) ";
                                } else {
                                    foreach($multiselect as $valueToSearch){
                                        $valueMultiselect[] = $filter["field"] ." LIKE '%" . $valueToSearch . "%'";
                                    }
                                    $strNewCondition = " (".implode(" OR ", $valueMultiselect).") ";
                                }
//                                $strNewCondition .= $filter["field"] . " LIKE '%" . $filter["value"] . "%'";
                            } else {
                                if (!$filter['type']) {
                                    $strNewCondition .= $filter["field"] . " = '" . $filter["value"] . "'";
                                } else {
                                    $strNewCondition .= $filter["field"] . " " . $filter['type'] . " '" . $filter["value"] . "'" ;
                                }
                            }

                    }
                }
                $arrJoinsCondition[str_replace("LEFT", "INNER", $join_key)] = $strNewCondition. $AND . $strCondition;
            } else {
                //serve per mantenere inalterato l'ordine delle join
                $arrJoins[$join_key] = $join_value;
                $arrJoinsCondition[$join_key] = $parrJoinsCondition[$join_key];
            }
        }


        //ORDINAMENTO
        $arrSortBy = array();
        if(isset($datatable["order"])) {
            foreach ($datatable["order"] as $order) {
                $column_name = $datatable["columns"][$order["column"]]["name"];
                $arrSortBy[str_replace("__", ".", $column_name)] = $order["dir"];
            }
        }
//        if(count($arrGroupBy) > 0) {
//            $total = $this->get_count_group_by($arrEntities, $arrJoins, $arrParams, $arrJoinsCondition, $conditionValues, $arrGroupBy);
//            $result["count"] = $total;
//        } else {
//            $total = $this->get_count($arrEntities, $arrJoins, $arrParams, $arrJoinsCondition, $conditionValues);
//            $result["count"] = $total["result"];
//        }
        $result["data"]  = $this->get_all($arrEntities, $arrJoins, $arrParams, $arrJoinsCondition, $conditionValues, $datatable["start"], ($datatable["length"] + 1), $arrSortBy, $arrGroupBy);
        return $result;
    }

    /**
     * @param $arrProperties
     * @param $entity
     * @return string
     */
    public function fillOutEntity($arrProperties, $entity = null){
        if (!$entity) {
            // Recupero il namespace della classe figlia dell'AbstractRepository che e' stata invocata:
            $repositoryNamespace = get_called_class();

            // Taglio il namespace del Repository:
            $tmp1 = explode("\\", $repositoryNamespace);

            // Recupero il nome della entity:
            $tmp2 = explode("Repository", $tmp1[2]);

            // Ricostruisco il namespace della entity:
            $entityNamespace = $tmp1[0] . "\\Entity\\" . $tmp2[0];

            // Creo dinamicamente un'istanza della entity:
            $entity = new $entityNamespace();
        }
        foreach($arrProperties as $property=>$value){
            $entity->{'set'.ucfirst($property)}($value);
        }
        return $entity;
    }

//    /**
//     * @param $entity
//     * @param $arrProperties
//     * @return string
//     */
//    public function updateEntity($entity, $arrProperties){
//
//        foreach($arrProperties as $property=>$value){
//            $entity->{'set'.ucfirst($property)}($value);
//        }
//
//        $entity = $this->update_entity($entity);
//
//        return $entity;
//    }

    /**
     * @param $query
     * @param $resultType
     * @return result
     */
    private function queryExecute($query, $resultType){
        $result = null;
        switch ($resultType){
            case self::$singleResult:
                $result = $query->getSingleResult();
                break;
            case self::$getOneOrNullResult:
                $result = $query->getOneOrNullResult();
                break;
            case self::$getResult:
                $result = $query->getResult();
                break;
        }
        return $result;
    }

    /**
     * @param $query
     * @param $resultType
     * @param $page
     * @param $per_page
     * @return result
     */
    private function queryPaginator($query, $resultType, $page, $per_page){

        $result = array();

        if(!is_null($page) && !(empty($per_page))){
            $query->setFirstResult($page)->setMaxResults($per_page);
            $result = $this->queryExecute($query, $resultType);
            
//            $paginator = new Paginator($query, $fetchJoinCollection = true);
//            if($returnCount){
//                $result['count'] = count($paginator);
//            }
////            $c = count($paginator);
//            foreach ($paginator as $post) {
//                if($returnCount){
//                    $result['data'][] = $post;
//                } else {
//                    $result[] = $post;
//                }
//            }
        } else {
            $result = $this->queryExecute($query, $resultType);
        }
        return $result;
    }

    /**
     * @param $arrValues
     * @param $contatore
     * @return string
     */
    protected function getImplodeParameters($arrValues, &$contatore) {
        $return = "";
        foreach ($arrValues as $value) {
            $return .= ":".$contatore++.", ";
        }
        return substr($return, 0, strlen($return) - 2);
    }

    static function Debug($object, $level) {
        echo "<pre>";
        \Doctrine\Common\Util\Debug::dump($object,$level);
        echo "</pre>";
    }

}
