<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class InvoiceAdmin extends Admin
{
    /**
     * @param Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('number', null, [
                    'label' => 'label.number', ])
            ->add('order', null, ['label' => 'label.order'])
            ->add('creator', null, ['label' => 'label.creator'])
            ->add('createdAt', null, [
                    'label' => 'label.created_at', ])
            ->add('isCanceled', null, ['label' => 'label.is_canceled'])
            ->add('comment', 'text', ['label' => 'label.comment'])
            ->add('canceledBy', null, ['label' => 'label.canceled_by']);
    }

    /**
     * @param Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('isCanceled', null, [
                    'label'   => 'label.is_canceled',
                    'required'=> true,
                    'attr'    => ['checked' => 'checked'],
            ])
            ->add('comment', 'textarea', ['label' => 'label.comment']);
    }

    /**
     * @param Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('number', null, ['label' => 'label.number'])
            ->add('order', 'entity', ['label' => 'label.order', 'admin_code' => 'admin.order'])
            ->add('creator', null, ['label' => 'label.creator'])
            ->add('isCanceled', null, ['label' => 'label.is_canceled'])
            ->add('comment', 'text', ['label' => 'label.comment'])
            ->add('canceledBy', null, ['label' => 'label.canceled_by'])
            ->add('createdAt', null, [
                    'label'  => 'label.created_at',
                    'format' => 'd/M/Y', ])
            ->add('_action', 'actions', [
                    'actions' => [
                            'edit' => [],
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
            ->add('number', null, ['label' => 'label.invoice_number'])
            ->add('order.id', null, ['label' => 'label.order'])
            ->add('creator', null, ['label' => 'label.creator'])
            ->add('isCanceled', null, ['label' => 'label.is_canceled'])
            ->add('canceledBy', null, ['label' => 'label.canceled_by']);
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'list':
                return 'InodataFloraBundle:Invoice:list.html.twig';
            break;
            case 'edit':
                return 'InodataFloraBundle:Invoice:edit.html.twig';
            break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('delete');
    }

    public function setSecurityContext($securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function getSecurityContext()
    {
        return $this->securityContext;
    }

    public function prePersist($order)
    {
        $user = $this->getSecurityContext()->getToken()->getUser();
        $order->setCreator($user);
    }
}
