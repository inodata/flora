<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class GuiaRojiAdmin extends Admin
{
    /**
     * @param Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('neighborhood', null, ['label' => 'label.neighborhood'])
            ->add('city', null, ['label' => 'label.city'])
            ->add('postal_code', null, ['label' => 'label.postal_code'])
            ->add('map', null, ['label' => 'label.map'])
            ->add('coordinate', null, ['label' => 'label.coordinate']);
    }

    /**
     * @param Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('neighborhood', null, ['label' => 'label.neighborhood'])
            ->add('city', null, ['label' => 'label.city'])
            ->add('postal_code', null, ['label' => 'label.postal_code'])
            ->add('map', null, ['label' => 'label.map'])
            ->add('coordinate', null, ['label' => 'label.coordinate'])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit'   => [],
                    'delete' => [],
                ],
            ]);
    }

    /**
     * @param Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('neighborhood', null, ['label' => 'label.neighborhood'])
            ->add('city', null, ['label' => 'label.city'])
            ->add('postal_code', null, ['label' => 'label.postal_code'])
            ->add('map', null, ['label' => 'label.map'])
            ->add('coordinate', null, ['label' => 'label.coordinate']);
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'InodataFloraBundle:GuiaRoji:edit.html.twig';
                break;
            case 'list':
                return 'InodataFloraBundle:GuiaRoji:list.html.twig';
                break;
            default:
                    return parent::getTemplate($name);
                break;
        }
    }

    //Add an action button
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('search');
    }

    /**
     * Determina el ordenamiento por default en el listado.
     */
    protected $datagridValues = [
        '_page'       => 1,
        '_sort_order' => 'ASC', // sort direction
        '_sort_by'    => 'neighborhood', // field name
    ];

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery();
        $query->addOrderBy($query->getRootAlias().'.city', 'ASC');

        return $query;
    }
}
