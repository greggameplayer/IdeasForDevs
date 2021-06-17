<?php


namespace App\Controller;


use App\Entity\Account;
use App\Entity\IsFor;
use App\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class IsForController extends AbstractController
{

    /**
     * @Route("/addLike/{idProject}", name="addLike")
     */
    public function addLike($idProject)
    {
        //TODO implement $this->getUser()
        $like = $this->getDoctrine()->getRepository(IsFor::class)->findOneByLike(1, $idProject);
        if($like){
            $isFor = $this->getDoctrine()->getRepository(IsFor::class)->findOneBy(["id"=>$like]);
            $em = $this->getDoctrine()->getManager();
            $em->remove($isFor);
            $em->flush();
        }
        else{
            $dislike = $this->getDoctrine()->getRepository(IsFor::class)->findOneByDislike(1, $idProject);
            if($dislike){
                $isFor = $this->getDoctrine()->getRepository(IsFor::class)->findOneBy(["id"=>$dislike]);
                $em = $this->getDoctrine()->getManager();
                $em->remove($isFor);
                $em->flush();
            }
            $isFor = new IsFor();

            $isFor->setEvaluation(True);
            $isFor->setIdProject($this->getDoctrine()->getRepository(Project::class)->findOneBy(['id'=>$idProject]));
            $isFor->setIdAccount($this->getDoctrine()->getRepository(Account::class)->findOneBy(["id"=>1]));


            $em = $this->getDoctrine()->getManager();
            $em->persist($isFor);
            $em->flush();

        }
        return $this->redirectToRoute('allProjects');

    }


    /**
     * @Route("/addDislike/{idProject}", name="addDislike")
     */
    public function addDislike($idProject)
    {
        //TODO implement $this->getUser()
        $dislike = $this->getDoctrine()->getRepository(IsFor::class)->findOneByDislike(1, $idProject);
        if($dislike){
            $isFor = $this->getDoctrine()->getRepository(IsFor::class)->findOneBy(["id"=>$dislike]);
            $em = $this->getDoctrine()->getManager();
            $em->remove($isFor);
            $em->flush();
        }
        else{
            $like = $this->getDoctrine()->getRepository(IsFor::class)->findOneByLike(1, $idProject);
            if($like){
                $isFor = $this->getDoctrine()->getRepository(IsFor::class)->findOneBy(["id"=>$like]);
                $em = $this->getDoctrine()->getManager();
                $em->remove($isFor);
                $em->flush();
            }
            $isFor = new IsFor();

            $isFor->setEvaluation(False);
            $isFor->setIdProject($this->getDoctrine()->getRepository(Project::class)->findOneBy(['id'=>$idProject]));
            $isFor->setIdAccount($this->getDoctrine()->getRepository(Account::class)->findOneBy(["id"=>1]));


            $em = $this->getDoctrine()->getManager();
            $em->persist($isFor);
            $em->flush();

        }
        return $this->redirectToRoute('allProjects');

    }
}