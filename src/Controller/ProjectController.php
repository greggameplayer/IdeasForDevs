<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Apply;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Project;
use App\Form\ProjectType;

class ProjectController extends AbstractController
{

    public function isAdmin($project)
    {
        $applies = $project->getApplies();
        if($project->getAccount()->getId() == $this->getUser()->getId()){
            return True;
        }
        foreach ($applies as $apply){
            if ($apply->getRoleProject()->getName() == 'Administrateur'){
                $account = $this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $apply->getIdAccount()->getId()]);
                if($account->getId() ==  $this->getUser()->getId()){
                    return True;
                }
            }
        }
        return False;
    }

    public function isMember($project)
    {
        $applies = $project->getApplies();
        if($project->getAccount()->getId() == $this->getUser()->getId()){
            return True;
        }
        foreach ($applies as $apply){
            if ($apply->getRoleProject()->getName() == 'Administrateur' || $apply->getRoleProject()->getName() == 'Membre'){
                $account = $this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $apply->getIdAccount()->getId()]);
                if($account->getId() ==  $this->getUser()->getId()){
                    return True;
                }
            }
        }
        return False;
    }


    /**
     * @Route("/allProjects", name="allProjects")
     * @param Request $request
     * @return Response
     */
    public function allProjects(Request $request, PaginatorInterface $paginator): Response
    {
        $data = $this->getDoctrine()->getRepository(Project::class)->findAll();

        $projects = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        dump($projects);
        
        return $this->render('test.html.twig', [
            'projects' => $projects,
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0])
        ]);
    }

    /**
     * @Route("/createProject", name="createProject")
     * @param Request $request
     * @return Response
     */
    public function createProject(Request $request): Response
    {
        $project = new Project;
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $project->setAccount($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            //return $this->redirectToRoute('homepagePatient'); Ajouter la redirection vers le projet
        }


        return $this->render('test.html.twig', [
            'form' => $form->createView(),
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0])
        ]);
    }

    /**
     * @Route("/updateProject/{id}", name="updateProject")
     * @param Request $request
     * @return Response
     */
    public function updateProject(Request $request, $id): Response
    {

        $project = $this->getDoctrine()->getRepository(Project::class)->findOneBy(['id' => $id]);
        if($this->isAdmin){
            $form = $this->createForm(ProjectType::class, $project);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($project);
                $em->flush();
    
                //return $this->redirectToRoute('homepagePatient'); Ajouter la redirection vers le projet
            }
            return $this->render('test.html.twig', [
                'form' => $form->createView(),
                'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0])
            ]);
        }
        return $this->render('test.html.twig', [
            'info' => "Vous n'avez pas les droits pour modifier ce projet",
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0])
        ]);
        
    }

    /**
     * @Route("/delProject/{id}", name="delProject")
     */
    public function delProject($id)
    {
        
        $project = $this->getDoctrine()->getRepository(Project::class)->findOneBy(['id' => $id]);
        
        if($this->isAdmin($project)){
            $em = $this->getDoctrine()->getManager();
            $em->remove($project);
            $em->flush();
        }

        dump("You don't have the right to delete this project");

                
        return $this->render('test.html.twig', [
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0])
        ]);
    }

    /**
     * @Route("/getMemberProject/{id}", name="getMemberProject")
     */
    public function getMemberProject($id)
    {        
        $project = $this->getDoctrine()->getRepository(Project::class)->findOneBy(['id' => $id]);

        if($this->isAdmin($project)){
            $applies = $project->getApplies();
            $members = [];
            $admin=[];
            $waiting=[];
            $rejected=[];
    
            foreach ($applies as $apply){
                if ($apply->getRoleProject()->getName() == 'Membre'){
                    $account = $this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $apply->getIdAccount()->getId()]);
                    array_push($members, $account);
                }
                if ($apply->getRoleProject()->getName() == 'Administrateur'){
                    $account = $this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $apply->getIdAccount()->getId()]);
                    array_push($admin, $account);
                }
                if ($apply->getRoleProject()->getName() == 'En attente'){
                    $account = $this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $apply->getIdAccount()->getId()]);
                    array_push($waiting, $account);
                }
                if ($apply->getRoleProject()->getName() == 'Refusé'){
                    $account = $this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $apply->getIdAccount()->getId()]);
                    array_push($rejected, $account);
                }
            }
    
            $creator = $this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $project->getAccount()->getId()]);
            dump($creator,$members, $admin, $waiting, $rejected);
    
            return $this->render('test.html.twig', [
                'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0]),
                'creator' => $creator,
                'members'=>$members,
                'administrator'=>$admin,
                'waiting'=>$waiting,
                'rejected'=>$rejected
            ]); 
        }
        
        $info = "Vous n'avez pas les "
    }


    /**
     * @Route("/applyToProject/{id}", name="applyToProject")
     * @param Request $request
     * @return Response
     */
    public function applyToProject(Request $request, $id): Response
    { 
        $apply = new Apply;
        $project=$this->getDoctrine()->getRepository(Project::class)->findOneBy(['id' => $id]);

        $form = $this->createForm(ProjectType::class, $apply);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            //return $this->redirectToRoute('homepagePatient'); Ajouter la redirection vers le projet
        }


        return $this->render('test.html.twig', [
            'form' => $form->createView(),
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0])
        ]);
    
    
    
    
    }

    /**
     * @Route("/updateRoleProject/{idProject}/{idApply}", name="updateRoleProject")
     * @param Request $request
     * @return Response
     */
    public function updateRoleProject(Request $request, $idProject, $idApply): Response
    {  
        
        $project=$this->getDoctrine()->getRepository(Project::class)->findOneBy(['id' => $idProject]);

        if($this->isAdmin($project)){
            $apply =$this->getDoctrine()->getRepository(Apply::class)->findOneBy(['id' => $idApply]);
            $form = $this->createForm(ProjectType::class, $apply);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($project);
                $em->flush();
    
                //return $this->redirectToRoute('homepagePatient'); Ajouter la redirection vers le projet
            }
        }
        dump("You don't have the right to delete this project");       
        
    }

    /**
     * @Route("/delApply/{idApply}", name="delApply")
     */
    public function delApply($idApply)
    {  
                
        $apply = $this->getDoctrine()->getRepository(Apply::class)->findOneBy(['id' => $idApply]);
        
        if($apply->setIdAccount()->getId() == $this->getUser()->getId()){
            $em = $this->getDoctrine()->getManager();
            $em->remove($apply);
            $em->flush();
        }

        return $this->render('test.html.twig', [
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0])
        ]);        
    }    
}
