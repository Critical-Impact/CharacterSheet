<?php

namespace CCS\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Shadow\ConfBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Paginator;
use Gregwar\ImageBundle\Services\ImageHandling;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use FOS\RestBundle\Controller\FOSRestController;
use Twig_Environment;


class BaseController extends FOSRestController
{
    /**
     * @return bool
     */
    public function isAdmin()
    {
        //TODO:Technically if they are imitating someone else they are an admin, maybe do a full on check later
        if ($this->getSecurityContext()->isGranted('ROLE_ADMIN') || $this->isImitating()) {
            return true;
        }
        return false;
    }

    public function isImitating()
    {
        return Shared::isImitating($this->getSecurityContext());
    }

    /**
     * @return \CCS\BaseBundle\Entity\User
     */	
    public function getImitatingUser()
    {
        $token = $this->container->get('security.context')->getToken();
        if($token != null)
        {
            $roles = $token->getRoles();
            foreach ($roles as $role) {
                if (method_exists($role, 'getSource')) {
                    return $this->manager->findUserByUsername(($role->getSource()->getUser()->getUsername()));
                }
            }
        }
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getDoctrine()->getEntityManager();
    }

    /**
     * @return \Symfony\Component\Security\Core\SecurityContext
     */
    public function getSecurityContext()
    {
        return $this->get('security.context');
    }


    /**
     * @return \WhiteOctober\TCPDFBundle\Controller\TCPDFController
     */
    public function getTCPDF()
    {
        return $this->get('white_october.tcpdf');
    }

    /**
     * @return Paginator
     */
    public function getPaginator()
    {
        return $this->get('knp_paginator');
    }

    public function generateRedirect($path, array $parameters = array(), $absolute = false)
    {
        return $this->redirect($this->getRouter()->generate($path,$parameters,$absolute));
    }

    public function generateReferrerRedirect()
    {
        $referrer = $this->getRequest()->headers->get('referer');
        return new RedirectResponse($referrer);
    }

    public function generateSelfRedirect()
    {
        return $this->redirect($this->getRequest()->getRequestUri());
    }

    /**
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this->get('twig');
    }

    public function getSession($name, $default = null)
    {
        $session = $this->getRequest()->getSession();
        if($session->has($name))
        {
            return $session->get($name, $default);
        }
        return $default;
    }

    /**
     * @return \Liuggio\ExcelBundle\Service\ExcelContainer
     */
    public function getXLSService()
    {
        return $this->get('xls.service_xls5');
    }

    /**
     * @return \PHPExcel_IOFactory
     */
    public function getXLSLoaderService()
    {
        return $this->get('xls.load_xls5');
    }


    /**
     * @return Breadcrumbs
     */
    public function getBreadcrumbs($addHome = true)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home",$this->getRouter()->generate("dashboard_home"));
        return $breadcrumbs;
    }

    public function setSession($name, $value)
    {
        $session = $this->getRequest()->getSession();
        $session->set($name,$value);
        return $value;
    }

    public function deleteSession($name)
    {
        $session = $this->getRequest()->getSession();
        $session->remove($name);
    }

    public function hasSession($name)
    {
        $session = $this->getRequest()->getSession();
        return $session->has($name);
    }

    /**
     * @return \FOS\UserBundle\Model\UserManager
     */
    public function getUserManager()
    {
        return $this->get('fos_user.user_manager');
    }

    public function isCurrentUser($id)
    {
        if($this->getUser()->getId() != $id)
        {
            return false;
        }
        return true;
    }


    /**
     * @return \CCS\BaseBundle\Entity\User
     */
    public function getUser()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if($user == "anon.")
        {
            return null;
        }
        return $user;
    }


    public function loginAsUser(User $user)
    {
        $providerKey = $this->container->getParameter('fos_user.firewall_name');

        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        $this->container->get('security.context')->setToken($token);
    }

    public function logOut()
    {
        $this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();
    }



    /**
     * @return \Symfony\Component\Routing\Router
     */
    public function getRouter()
    {
        return $this->get("router");
    }

    public function setSuccessFlash($title,$body)
    {
        $this->container->get("session")->setFlash("success-flash", $body);
        $this->container->get("session")->setFlash("success-title", $title);
    }
    public function setFailureFlash($title,$body)
    {
        $this->container->get("session")->setFlash("failure-flash", $body);
        $this->container->get("session")->setFlash("failure-title", $title);
    }

    protected function setFlash($action, $value)
    {
        $this->container->get('session')->setFlash($action, $value);
    }

    public function getBaseUrl()
    {
        $request = $this->getRequest();
        return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
    }

    /**
     * @param $headers
     * @param $content
     * @param $title
     * @return mixed
     */
    public function outputXLS($headers, $content, $title, $creator = null)
    {
        /* @var $xls \PHPExcel */
        //Failure in code completion
        $XLSWriter = $this->getXLSService();
        $xls = $XLSWriter->getExcelObj();
        $workSheet = $xls->setActiveSheetIndex(0);
        $xls->getProperties()
            ->setLastModifiedBy($creator)
            ->setTitle($title);
        if($creator != null)
        {
            $xls->getProperties()->setCreator($creator);
        }
        $workSheet->fromArray($headers, null, 'A1');
        $contentArray = array();
        $x = 2;
        foreach($content as $row)
        {
            $workSheet->fromArray($row, null, 'A'.$x);
            $x++;
        }


        $response = $XLSWriter->getResponse();
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename='.$title.'.xls');

        // If you are using a https connection, you have to set those two headers for compatibility with IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        return $response;

    }

    public function outputRawXLS($xlsContainer,$title)
    {
        $response = $xlsContainer->getResponse();
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename='.$title.'.xls');

        // If you are using a https connection, you have to set those two headers for compatibility with IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        return $response;

    }

    function arrayToCSV($data)
    {
        $outstream = fopen("php://temp", 'r+');
        fputcsv($outstream, $data, ',', '"');
        rewind($outstream);
        $csv = fgets($outstream);
        fclose($outstream);
        return $csv;
    }


    /**
     * @param $headers array An array of headers
     * @param $content array[array] An array of rows inside an array
     * @param $title string The title of the csv
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function outputCsv($headers, $content, $title)
    {
        $finalContent = array();
        foreach($content as $contentRow)
        {
            $finalContent[] = $this->arrayToCSV($contentRow);
        }
        $headerCount = count($headers);
        for($count = 0; $count < $headerCount; $count++)
        {
            $headers[$count] = "\"".$headers[$count]."\"";
        }


        $rows = array();
        foreach($content as $row)
        {

            $rows[] = "\"".implode("\",\"",$row)."\"";

        }





        $response = $this->render("ShadowConfBundle:Templates:csv.html.twig", array('headers' => $headers, 'content' => $rows));
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename='.$title.'.csv');

        // If you are using a https connection, you have to set those two headers for compatibility with IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        //$response->headers->set('Content-Length', filesize($fileName));
        return $response;
    }

    public function resetPagination()
    {
        $this->get('request')->query->remove("page");
    }

}