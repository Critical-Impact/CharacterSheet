<?php

namespace CCS\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class UserController extends BaseController
{
	public function addFilesAction(Request $request)
	{
        //get the logged in user
        $user = $this->getUser();
		$addFilesForm = $this->createForm(new \CCS\BaseBundle\Form\UserType(),$user);
        if($request->getMethod() == "POST")
        {
            $addFilesForm->bindRequest($request);
            if($addFilesForm->isValid())
            {
                foreach($user->getFiles() as $file)
                {
                    $this->getEntityManager()->persist($file);
                }
                $this->getEntityManager()->flush();
            }
        }
        return $this->render('CCSBaseBundle:User:file.html.twig',array('form' => $addFilesForm->createView()));
	}		
}