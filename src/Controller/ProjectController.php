<?php

namespace App\Controller;

use App\Document\Avatar;
use App\Document\ProjectImage;
use App\Entity\Account;
use App\Entity\Apply;
use App\Entity\Job;
use App\Entity\JobsAccount;
use App\Entity\Status;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use MongoDB\BSON\ObjectId;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
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
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

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
     * @Route("/user/allProjects", name="allProjects")
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
     * @Route("/newProject", name="newProject")
     * @throws MongoDBException
     * @throws \Exception
     */
    public function newProject(Request $request, DocumentManager $dm)
    {
        date_default_timezone_set('Europe/Paris');
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {

            /** @var UploadedFile $document */
            $document = $request->files->get("avatar");
            if(!in_array($document->guessExtension(), ["png", "jpg", "jpeg", "gif"])) {
                $this->filesystem->remove($document->getPathname());
                return $this->json(["error" => "Le format du fichier pour l'avatar que vous avez envoyé n'est pas supporté !"]);
            } else if($document->getSize() > 2000000) {
                $this->filesystem->remove($document->getPathname());
                return $this->json(["error" => "Votre fichier avatar pése plus de 2 Mo !"]);
            }
            /** @var resource $stream */
            $stream = fopen($document->getPathname(), 'r');
            /** @var ObjectId $id */
            $id = $dm->getDocumentBucket(ProjectImage::class)->uploadFromStream($document->getClientOriginalName(), $stream);
            fclose($stream);
            $this->filesystem->remove($document->getPathname());

            $project = new Project();


            $project->setRepo($request->get("repo"));
            $project->setName($request->get("name"));
            $project->setDescription($request->get("description"));
            $project->setDateCreation(new DateTime());
            $project->setSkillsNeeded(json_decode($request->get("skills")));
            $project->setJobNeeded(json_decode($request->get("jobs")));
            $project->setIdMongo($id->serialize());
            $project->setAccount($this->getUser());
            $project->setStatus($this->getDoctrine()->getRepository(Status::class)->find(1));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            $apply = new Apply();
            $roleProject = $this->getDoctrine()->getRepository(RoleProject::class)->findOneBy(['id' => 4]);

            $apply->setRoleProject($roleProject);
            $apply->setIdAccount($this->getUser());
            $apply->setIdProject($project);

            $em = $this->getDoctrine()->getManager();
            $em->persist($apply);
            $em->flush();

            return $this->json(["message" => "good !"]);
        }

        return $this->render('project/createProject.html.twig', [
            'form' => $form->createView(),
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0])
        ]);
    }




    /**
     * @Route("/user/updateProject/{id}", name="updateProject")
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
     * @Route("/user/delProject/{id}", name="delProject")
     */
    public function delProject($id)
    {

        $project = $this->getDoctrine()->getRepository(Project::class)->findOneBy(['id' => $id]);

        if($this->isAdmin($project)){
            $em = $this->getDoctrine()->getManager();
            $em->remove($project);
            $em->flush();
        }

        return $this->render('test.html.twig', [
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0])
        ]);
    }

    /**
     * @Route("/user/getMembersProject/{id}", name="getMembersProject")
     */
    public function getMembersProject($id)
    {
        $project = $this->getDoctrine()->getRepository(Project::class)->findOneBy(['id' => $id]);

        if($this->isAdmin($project)){
            $applies = $project->getApplies();
            $members = [];
            $admin=[];
            $waiting=[];
            $description = [];
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
                    array_push($description, $apply->getDescription());
                    array_push($waiting, $account);
                }
                if ($apply->getRoleProject()->getName() == 'Refusé'){
                    $account = $this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $apply->getIdAccount()->getId()]);
                    array_push($rejected, $account);
                }
            }

            $creator = $this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $project->getAccount()->getId()]);

            return $this->render('project/projectMembers.html.twig', [
                'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0]),
                'creator' => $creator,
                'members'=>$members,
                'administrator'=>$admin,
                'waiting'=>$waiting,
                'rejected'=>$rejected,
                'project' =>$project,
                'descriptions'=>$description,
                'isAdmin'=> 1
            ]);
        }

        elseif ($this->isMember($project)){
            $applies = $project->getApplies();
            $members = [];
            $admin=[];

            foreach ($applies as $apply){
                if ($apply->getRoleProject()->getName() == 'Membre'){
                    $account = $this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $apply->getIdAccount()->getId()]);
                    array_push($members, $account);
                }
                if ($apply->getRoleProject()->getName() == 'Administrateur'){
                    $account = $this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $apply->getIdAccount()->getId()]);
                    array_push($admin, $account);
                }
            }

            $creator = $this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $project->getAccount()->getId()]);


            return $this->render('project/projectMembers.html.twig', [
                'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0]),
                'creator' => $creator,
                'members'=>$members,
                'administrator'=>$admin,
                'project' =>$project
            ]);
        }
        return $this->redirectToRoute('allProjects');
    }


    /**
     * @Route("/user/applyToProject/{idProject}", name="applyToProject")
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
        $apply->setIdAccount($this->getUser());
        $apply->setRoleProject($roleProject);

        $form = $this->createForm(ApplyType::class, $apply);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($apply);
            $em->flush();

            //return $this->redirectToRoute('detailProject'); Ajouter la redirection vers le projet
        }

        return $this->render('project/applyProject.html.twig', [
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0]),
            'project' => $project,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/updateRoleProject/{idProject}/{idApply}", name="updateRoleProject")
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
     * @Route("/user/evolutionApply/{idUser}/{idProject}/{idStatus}", name="evolutionApply")
     */
    public function evolutionApply($idUser, $idProject, $idStatus): Response
    {
        $project=$this->getDoctrine()->getRepository(Project::class)->findOneBy(['id' => $idProject]);
        if($this->isAdmin($project)){

            $appliesAdmin = $project->getApplies();

            $apply=$this->getDoctrine()->getRepository(Apply::class)->findByProjectUser($idUser, $idProject);
            $projectRole = $this->getDoctrine()->getRepository(RoleProject::class)->findOneBy(['id' => $idStatus]);
            $apply->setRoleProject($projectRole);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return  $this->redirectToRoute('getMembersProject',['id' => $project->getId()]);;
        }

        return  $this->redirectToRoute('allProjects');




    }



    /**
     * @Route("/user/delApply/{idApply}", name="delApply")
     */
    public function delApply($idApply)
    {

        $apply = $this->getDoctrine()->getRepository(Apply::class)->findOneBy(['id' => $idApply]);

        if($apply->getIdAccount()->getId() == $this->getUser()->getId()){
            $em = $this->getDoctrine()->getManager();
            $em->remove($apply);
            $em->flush();
        }

        return $this->render('test.html.twig', [
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0])
        ]);
    }
}
