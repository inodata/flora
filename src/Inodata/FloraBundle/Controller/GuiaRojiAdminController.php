<?php

namespace Inodata\FloraBundle\Controller;

use Inodata\FloraBundle\Entity\GuiaRoji;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GuiaRojiAdminController extends Controller
{
    public function findByIdAction($id)
    {
        $guiaRoji = $this->getDoctrine()->getRepository('InodataFloraBundle:GuiaRoji')
                ->find($id);

        $return = ['neighborhood'=>$guiaRoji->getNeighborhood(), 'city'=> $guiaRoji->getCity(), 'postal_code' => $guiaRoji->getPostalCode()];

        return $this->renderJson($return);
    }

    public function searchAction()
    {
        $letter = $this->getRequest()->get('letter');
        $page = $this->getRequest()->get('page');

        $url = 'http://guiaroji.com.mx/listado_colonia.php?letra='.$letter.'&ciudad=3&pagina='.$page;

        $cookie = tmpfile();
        $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.52 Safari/537.36';
        $header = [
        'Accept: text/html',
        'Accept-Language: es-MX',
        'Accept-Charset: iso-8859-1',
        'Keep-Alive: 300', ];

        $ch = curl_init($url);
        $options = [
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_USERAGENT      => $userAgent,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEFILE     => $cookie,
            CURLOPT_COOKIEJAR      => $cookie,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0, ];

        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        print_r($response);

        curl_close($ch);

        return new Response('');
    }

    public function saveAction()
    {
        $colonies = $this->getRequest()->get('colonies');
        $em = $this->getDoctrine()->getManager();

        foreach ($colonies as $colony) {
            $guiaRoji = new GuiaRoji();
            $guiaRoji->setCity($colony['city']);
            $guiaRoji->setNeighborhood($colony['name']);
            $guiaRoji->setPostalCode($colony['cp']);
            $guiaRoji->setMap($colony['map']);
            $guiaRoji->setCoordinate($colony['coordinate']);

            $em->persist($guiaRoji);
            $em->flush();
        }
        $em->clear();

        return new Response('success');
    }

    public function configure()
    {
        $adminCode = $this->container->get('request')->get('_sonata_admin');

        if ($adminCode) {
            parent::configure();
        }
    }
}
