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
use App\Entity\RoleProject;
use App\Form\ApplyType;
use App\Form\ProjectType;
use DateTime;

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
        if(isset($_GET['search'])){
            $data = $this->getDoctrine()->getRepository(Project::class)->findByName($_GET['search']);
        }
        else{
            $data = $this->getDoctrine()->getRepository(Project::class)->findAll();
        }


        $projects = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),

            2
        );


        $projectsNotation =[];

        foreach($projects as $project){
            //To get creator of the project
            $account = $this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $project->getAccount()->getId()]);
            $project->setAccount($account);
            //To get like and dislike
            $test = True;
            $isFors = $project->getIsFors();
            foreach ($isFors as $like){
                $noted = 0;
                $likes = 0;
                $dislikes = 0;
                if($like->getEvaluation()){
                    $likes++;
                }
                else{
                    $dislikes++;
                }
                if($like->getIdAccount()->getId() == $this->getUser()->getId() && $like->getEvaluation()){
                    $noted = 1;
                }
                if($like->getIdAccount()->getId() == $this->getUser()->getId() && !$like->getEvaluation()){
                    $noted = 2;
                }
                array_push($projectsNotation, [$noted, $likes, $dislikes]);
                $test = False;
            }
            if($test){
                array_push($projectsNotation, [0, 0, 0]);
            }
        }
        
        return $this->render('project/index.html.twig', [
            'projects' => $projects,
            'notations' => $projectsNotation,
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
        //TODO implement redirection after the creation of the project
        date_default_timezone_set('Europe/Paris');
        $currentDate = new DateTime();

        $project = new Project;
        $project->setDateCreation($currentDate);
        $project->setStatus(0);
        $project->setAccount($this->getUser());

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $project->setAccount($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            //return $this->redirectToRoute('homepagePatient'); Ajouter la redirection vers le projet
        }

        return $this->render('project/createProject.html.twig', [
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
        //TODO implement redirection after the creation of the project
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
    
            return $this->render('test.html.twig', [
                'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0]),
                'creator' => $creator,
                'members'=>$members,
                'administrator'=>$admin,
                'waiting'=>$waiting,
                'rejected'=>$rejected
            ]); 
        }
    }


    /**
     * @Route("/applyToProject/{idProject}", name="applyToProject")
     * @param Request $request
     * @return Response
     */
    public function applyToProject(Request $request, $idProject): Response
    { 
        //TODO implement redirection after the creation of the project
        $apply = new Apply;

        $roleProject = $this->getDoctrine()->getRepository(RoleProject::class)->findOneBy(['id' => 1]);
        $project=$this->getDoctrine()->getRepository(Project::class)->findOneBy(['id' => $idProject]);
        $project->setAccount($this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $project->getAccount()->getId()]));        

        $apply->setIdProject($project);
        //here
        //$apply->setIdAccount($this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => 1]));
        $apply->setRoleProject($roleProject);

        $form = $this->createForm(ApplyType::class, $apply);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($apply);
            $em->flush();

            //return $this->redirectToRoute('homepagePatient'); Ajouter la redirection vers le projet
        }

        return $this->render('project/applyProject.html.twig', [
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0]),
            'project' => $project,
            'form' => $form->createView()
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
