<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Apply;
use App\Entity\Commentary;
use App\Entity\Project;
use ArrayObject;
use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailController extends AbstractController
{

    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @Route("/commente/{id}", name="commente")
     */
    public function commente($id, Request $request): Response
    {
        $commente = new commentary();
        $commente->setComment($_POST['commentaire']);
        $commente->setIdAccount($this->getUser());
        $commente->setIdProject($this->getDoctrine()->getRepository(Project::class)->findOneBy(['id' => $id]));
        $commente->setDateComment(new DateTime());
        $em = $this->getDoctrine()->getManager();
        $em->persist($commente);
        $em->flush();

        return $this->redirectToRoute('detailProject', ["id" => $id]);
    }

    /**
     * @Route("/UpdateCommente/{id}", name="UpdateCommente")
     */
    public function UpdateCommente($id): Response
    {
        $commente = $this->getDoctrine()->getRepository(Commentary::class)->findOneBy(['idProject' => $id, 'idAccount' => $this->getUser()]);
        $commente->setComment($_POST['commentaire']);
        $commente->setDateComment(new DateTime());
        $em = $this->getDoctrine()->getManager();
        $em->persist($commente);
        $em->flush();

        return $this->redirectToRoute('detailProject', ["id" => $id]);
    }


    /**
     * @Route("/detailProject/{id}", name="detailProject")
     */
    public function detailProject($id, DocumentManager $dm): Response
    {
        $candidatureUserConnected = $this->getDoctrine()->getRepository(Apply::class)->findOneBy(["idAccount" => $this->getUser()->getId(), "idProject" => $id]);

        $IdsOfAdmins = $this->getDoctrine()->getRepository(Apply::class)->IdOfAdmins($id, $this->getUser()->getId());

        $avatarAdminForEachAdmin = new ArrayObject(array());
        $detailsAdminProjectForEachAdmin = new ArrayObject(array());
        $countProjectAsAdminForEachAdmin = new ArrayObject(array());
        $countProjectParticipationForEachAdmin = new ArrayObject(array());
        $countProjectSuccessfullForEachAdmin = new ArrayObject(array());
        $skillsForEachAdmin = new ArrayObject(array());

        foreach ($IdsOfAdmins as $idAdmin) {
            $detailsAdminProjectForEachAdmin->append($this->getDoctrine()->getRepository(Account::class)->detailsAdminProjectForEachAdmin($idAdmin));
            $avatarAdminForEachAdmin->append(ProfileController::getUserAvatar($this->getDoctrine()->getRepository(Account::class)->find($idAdmin), $dm, $this->filesystem, $this->getDoctrine()->getManager(), $this->getParameter('kernel.project_dir')));
            $skillsForEachAdmin->append(json_decode($this->getDoctrine()->getRepository(Account::class)->skillsForEachAdmin($idAdmin)));
            $countProjectAsAdminForEachAdmin->append($this->getDoctrine()->getRepository(Apply::class)->countProjectAsAdmin($idAdmin));
            $countProjectParticipationForEachAdmin->append($this->getDoctrine()->getRepository(Apply::class)->countProjectParticipation($idAdmin));
            $countProjectSuccessfullForEachAdmin->append($this->getDoctrine()->getRepository(Apply::class)->countProjectSuccessfull($idAdmin));
        }


        $commentariesAndUser = $this->getDoctrine()->getRepository(Commentary::class)->GetCommentariesAndUser($id);
        $avatarUserForEachCommentaries = new ArrayObject(array());

        foreach ($commentariesAndUser as $commentaryAndUser){
            $avatarUserForEachCommentaries->append(ProfileController::getUserAvatar($this->getDoctrine()->getRepository(Account::class)->find($commentaryAndUser['idUser']), $dm, $this->filesystem, $this->getDoctrine()->getManager(), $this->getParameter('kernel.project_dir')));
        }


        $notation = new ArrayObject(array());
        $isFors = $this->getDoctrine()->getRepository(Project::class)->find($id)->getIsFors();
        $noted = 0;
        $likes = 0;
        $dislikes = 0;
        foreach ($isFors as $evaluation){
            if($evaluation->getEvaluation()){
                $likes++;
            }
            else{
                $dislikes++;
            }
            if($evaluation->getIdAccount()->getId() == $this->getUser()->getId() && $evaluation->getEvaluation()){
                $noted = 1;
            }
            if($evaluation->getIdAccount()->getId() == $this->getUser()->getId() && !$evaluation->getEvaluation()){
                $noted = 2;
            }
        }
        $notation->append($noted);
        $notation->append($likes);
        $notation->append($dislikes);


        if($candidatureUserConnected->getRoleProject()->getName() == "En attente" || $candidatureUserConnected->getRoleProject()->getName() == "Refusé"){
            return $this->render('detail/projectCandidat.html.twig', [
                'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0]),
                'detailsProject' => $this->getDoctrine()->getRepository(Project::class)->detailsProject($id),
                'imgProject' => ProfileController::getProjectImage($this->getDoctrine()->getRepository(Project::class)->find($id), $dm, $this->filesystem, $this->getDoctrine()->getManager(), $this->getParameter('kernel.project_dir')),
                'skillsNeeded' => json_decode($this->getDoctrine()->getRepository(Project::class)->skillsAndJobsNeeded($id)["skills_needed"]),
                'jobsNeeded' => json_decode($this->getDoctrine()->getRepository(Project::class)->skillsAndJobsNeeded($id)["job_needed"]),

                'candidatureUserConnected' => $candidatureUserConnected,

                'commentaries' => $commentariesAndUser,
                'avatarUserForEachCommentaries' => $avatarUserForEachCommentaries,

                'notation' => $notation,

                'detailsAdminProjectForEachAdmin' => $detailsAdminProjectForEachAdmin,
                'avatarAdminForEachAdmin' => $avatarAdminForEachAdmin,
                'skillsForEachAdmin' => $skillsForEachAdmin,
                'countProjectAsAdminForEachAdmin' => $countProjectAsAdminForEachAdmin,
                'countProjectParticipationForEachAdmin' => $countProjectParticipationForEachAdmin,
                'countProjectSuccessfullForEachAdmin' => $countProjectSuccessfullForEachAdmin,
            ]);
        }
        else if($candidatureUserConnected->getRoleProject()->getName() == "Membre" || $candidatureUserConnected->getRoleProject()->getName() == "Administrateur"){
            return $this->render('detail/projectCandidat.html.twig', [
                'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0]),
                'detailsProject' => $this->getDoctrine()->getRepository(Project::class)->detailsProject($id),
                'imgProject' => ProfileController::getProjectImage($this->getDoctrine()->getRepository(Project::class)->find($id), $dm, $this->filesystem, $this->getDoctrine()->getManager(), $this->getParameter('kernel.project_dir')),
                'skillsNeeded' => json_decode($this->getDoctrine()->getRepository(Project::class)->skillsAndJobsNeeded($id)["skills_needed"]),
                'jobsNeeded' => json_decode($this->getDoctrine()->getRepository(Project::class)->skillsAndJobsNeeded($id)["job_needed"]),

                'candidatureUserConnected' => $candidatureUserConnected,

                'commentaries' => $commentariesAndUser,
                'avatarUserForEachCommentaries' => $avatarUserForEachCommentaries,

                'notation' => $notation,

                'detailsAdminProjectForEachAdmin' => $detailsAdminProjectForEachAdmin,
                'avatarAdminForEachAdmin' => $avatarAdminForEachAdmin,
                'skillsForEachAdmin' => $skillsForEachAdmin,
                'countProjectAsAdminForEachAdmin' => $countProjectAsAdminForEachAdmin,
                'countProjectParticipationForEachAdmin' => $countProjectParticipationForEachAdmin,
                'countProjectSuccessfullForEachAdmin' => $countProjectSuccessfullForEachAdmin,
            ]);
        }
        else{
            return $this->render('detail/project.html.twig', [
                'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0]),
                'detailsProject' => $this->getDoctrine()->getRepository(Project::class)->detailsProject($id),
                'imgProject' => ProfileController::getProjectImage($this->getDoctrine()->getRepository(Project::class)->find($id), $dm, $this->filesystem, $this->getDoctrine()->getManager(), $this->getParameter('kernel.project_dir')),
                'skillsNeeded' => json_decode($this->getDoctrine()->getRepository(Project::class)->skillsAndJobsNeeded($id)["skills_needed"]),
                'jobsNeeded' => json_decode($this->getDoctrine()->getRepository(Project::class)->skillsAndJobsNeeded($id)["job_needed"]),

                'commentaries' => $commentariesAndUser,
                'avatarUserForEachCommentaries' => $avatarUserForEachCommentaries,

                'notation' => $notation,

                'detailsAdminProjectForEachAdmin' => $detailsAdminProjectForEachAdmin,
                'avatarAdminForEachAdmin' => $avatarAdminForEachAdmin,
                'skillsForEachAdmin' => $skillsForEachAdmin,
                'countProjectAsAdminForEachAdmin' => $countProjectAsAdminForEachAdmin,
                'countProjectParticipationForEachAdmin' => $countProjectParticipationForEachAdmin,
                'countProjectSuccessfullForEachAdmin' => $countProjectSuccessfullForEachAdmin,
            ]);
        }

    }

    /**
     * @Route("/modifyApplication/{idProject}", name="modifyApplication")
     */
    public function modifyApplication($idProject): Response
    {
        $apply = $this->getDoctrine()->getRepository(Apply::class)->findOneBy(['idProject' => $idProject, 'idAccount' => $this->getUser()]);
        $apply->setDescription($_POST['description']);
        $em = $this->getDoctrine()->getManager();
        $em->persist($apply);
        $em->flush();

        return $this->redirectToRoute('detailProject', ["id" => $idProject]);
    }
}
