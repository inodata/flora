<?php

namespace Inodata\FloraBundle\Controller;

use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AjaxWidgetsController extends Controller
{
    public function textAction(Request $request)
    {
        $text = $request->get('text');
        $entity = $request->get('entity');
        $column = $request->get('column');

        $words = explode(' ', $text);

        $search = "'";
        foreach ($words as $word) {
            $search .= "%{$word}%";
        }
        $search .= "'";

        //Create query builder using entity passed on parameters
        $qb = $this->getDoctrine()->getRepository($entity)
            ->createQueryBuilder('p');

        $qb->orWhere('p.' . $column . ' LIKE ' . $search);
        if ($column == 'city') {
            $qb->groupBy('p.city');
        }

        //Get query result as array
        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        $return = [];
        foreach ($result as $row) {
            $text = $row[$column];

            $data = ['id' => $row['id'], 'text' => $text . ' - ' . $row['city']];
            array_push($return, $data);
        }

        return new Response(json_encode($return));
    }

    public function entityAction(Request $request)
    {
        $text = $request->get('text');
        $entity = $request->get('entity');
        $columns = $request->get('columns');

        $words = explode(' ', $text);

        if (!ctype_digit($words[0])) {
            $search = "'";
            foreach ($words as $word) {
                $search .= "%{$word}%";
            }
            $search .= "'";
        } else {
            $search = $words[0];
        }

        //Parse columns gotten on parameters to an array
        $columns = explode(',', $columns);

        //Create query builder using entity passed on parameters
        $qb = $this->getDoctrine()->getRepository($entity)
            ->createQueryBuilder('p');

        //Create where clausule with columns parsed
        foreach ($columns as $column) {
            if (!ctype_digit($search)) {
                $qb->orWhere('p.' . $column . ' LIKE ' . $search);
            } else {
                $qb->orWhere('p.' . $column . ' = ' . $search);
            }
        }

        //TODO: Agregar parámetro de criteria para recibir strings y agregarlos aquí
        //$qb->andWhere('p.status = \'delivered\'');

        //Get query result as array
        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        $return = [];
        foreach ($result as $row) {
            $text = '';

            foreach ($columns as $column) {
                if ($text != '') {
                    $text .= (' - ' . $row[$column]);
                } else {
                    $text = $row[$column];
                }
            }

            $data = ['id' => $row['id'], 'text' => "{$text}"];
            array_push($return, $data);
        }

        return new Response(json_encode($return));
    }
}