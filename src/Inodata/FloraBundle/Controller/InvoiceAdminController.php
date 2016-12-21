<?php

namespace Inodata\FloraBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

class InvoiceAdminController extends Controller
{
    public function editAction($id = null)
    {
        if ($this->getRestMethod() == 'POST') {
            $object = $this->admin->getObject($id);
            $user = $this->getDoctrine()->getRepository('\Application\Sonata\UserBundle\Entity\User')
                ->find($this->getUser()->getId());

            $order = $object->getOrder();

            $object->setCanceledBy($user);
            $this->admin->update($object);

            if ($order) {
                $order->setInvoiceNumber('');
                $this->admin->update($order);
            }
        }

        return parent::editAction($id);
    }
}
