<?php

namespace Inodata\FloraBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Query;

class AjaxWidgetsController extends Controller
{
    public function textAction()
    {
        $text = $this->getRequest()->get('text');
        $entity = $this->getRequest()->get('entity');
        $column = $this->getRequest()->get('column');
        
        
        $words = explode(" ", $text);
        
        $search = "'";
        foreach ($words as $word){
            $search.="%{$word}%";
        }
        $search.="'";
        
        //Create query builder using entity passed on parameters
        $qb = $this->getDoctrine()->getRepository($entity)
                ->createQueryBuilder('p');
       
        $qb->orWhere("p.".$column." LIKE ".$search);
        if($column == "city"){
            $qb->groupBy("p.city");
        }
        
        //Get query result as array
        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        
        $return = array();        
        foreach ($result as $row){
            $text = $row[$column];
            
            $data = array('id' => $row['id'], 'text' => $text);
            array_push($return, $data);
        }
        
        return new Response(json_encode($return));
    }
    
    public function entityAction()
    {
        $text = $this->getRequest()->get('text');
        $entity = $this->getRequest()->get('entity');
        $columns = $this->getRequest()->get('columns');
        
        
        $words = explode(" ", $text);
        
        if(!ctype_digit($words[0])){
            $search = "'";
            foreach ($words as $word){
                $search.="%{$word}%";
            }
            $search.="'";
        }  else {
            $search = $words[0];
        }
        
        
        //Parse columns gotten on parameters to an array
        $columns = explode(",", $columns);
        
        //Create query builder using entity passed on parameters
        $qb = $this->getDoctrine()->getRepository($entity)
                ->createQueryBuilder('p');
        
        //Create where clausule with columns parsed
        foreach ($columns as $column){
            if(!ctype_digit($search)){
                $qb->orWhere("p.".$column." LIKE ".$search);
            }  else {
                $qb->orWhere("p.".$column." = ".$search);
            }
        }
        
        //Get query result as array
        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        
        $return = array();        
        foreach ($result as $row){
            $text = "";
            
            foreach ($columns as $column){
                if($text!=""){
                    $text.=(" - ".$row[$column]);
                }else{
                    $text = $row[$column];
                }
            }
            
            $data = array('id' => $row["id"], 'text' => "{$text}");
            array_push($return, $data);
        }
        
        return new Response(json_encode($return));
    }
}
