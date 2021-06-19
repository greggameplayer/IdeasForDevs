<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Apply;
use App\Entity\Commentary;
use App\Entity\IsFor;
use App\Entity\Project;
use ArrayObject;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailController extends AbstractController
{
    /**
     * @Route("/project/{id}", name="detailproject")
     */
    public function DetailProject($id): Response
    {

        $IdsOfAdmins = $this->getDoctrine()->getRepository(Apply::class)->IdOfAdmins($id);

        $detailsAdminProjectForEachAdmin =new ArrayObject(array());
        $countProjectAsAdminForEachAdmin = new ArrayObject(array());
        $countProjectParticipationForEachAdmin = new ArrayObject(array());
        $countProjectSuccessfullForEachAdmin = new ArrayObject(array());
        $skillsForEachAdmin = new ArrayObject(array());

        foreach ($IdsOfAdmins as $idAdmin){
            $detailsAdminProjectForEachAdmin->append($this->getDoctrine()->getRepository(Account::class)->detailsAdminProjectForEachAdmin($idAdmin));
            $skillsForEachAdmin->append(json_decode($this->getDoctrine()->getRepository(Account::class)->skillsForEachAdmin($idAdmin)));
            $countProjectAsAdminForEachAdmin->append($this->getDoctrine()->getRepository(Apply::class)->countProjectAsAdmin($idAdmin));
            $countProjectParticipationForEachAdmin->append($this->getDoctrine()->getRepository(Apply::class)->countProjectParticipation($idAdmin));
            $countProjectSuccessfullForEachAdmin->append($this->getDoctrine()->getRepository(Apply::class)->countProjectSuccessfull($idAdmin));
        }


        return $this->render('detail/project.html.twig', [
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0]),
            //'idUserConnected' => $this->getUser()->getId(),
            'idUserConnected' => 2,
            'detailsProject' => $this->getDoctrine()->getRepository(Project::class)->detailsProject($id),
            'skillsNeeded' => json_decode($this->getDoctrine()->getRepository(Project::class)->skillsAndJobsNeeded($id)["skills_needed"]),
            'jobsNeeded' => json_decode($this->getDoctrine()->getRepository(Project::class)->skillsAndJobsNeeded($id)["job_needed"]),
            'commentaries' => $this->getDoctrine()->getRepository(Commentary::class)->GetCommentariesAndUser($id),
            'countFor' => $this->getDoctrine()->getRepository(IsFor::class)->countVotesFor($id),
            'countAgainst' => $this->getDoctrine()->getRepository(IsFor::class)->countVotesAgainst($id),
            'detailsAdminProjectForEachAdmin' => $detailsAdminProjectForEachAdmin,
            'skillsForEachAdmin' => $skillsForEachAdmin,
            'countProjectAsAdminForEachAdmin' => $countProjectAsAdminForEachAdmin,
            'countProjectParticipationForEachAdmin' => $countProjectParticipationForEachAdmin,
            'countProjectSuccessfullForEachAdmin' => $countProjectSuccessfullForEachAdmin,
        ]);
    }

    /**
     * @Route("/commente/{id}", name="commente")
     */
    public function commente($id): Response
    {
        $commente = new commentary();
        $commente->setComment($_POST['commentaire']);
        //$commente->setIdAccount($this->getUser()->getId());
        $commente->setIdAccount(2);
        $commente->setIdProject($id);
        $em = $this->getDoctrine()->getManager();
        $em->persist($commente);
        $em->flush();

        return $this->redirectToRoute('detailproject', ["id" => $id]);
    }

    /**
     * @Route("/UpdateCommente/{id}", name="UpdateCommente")
     */
    public function UpdateCommente($id): Response
    {
        //$commente = $this->getDoctrine()->getRepository(Commentary::class)->findOneBy(['idProject' => $id, 'idAccount' => $this->getUser()->getId()]);
        $commente = $this->getDoctrine()->getRepository(Commentary::class)->findOneBy(['idProject' => $id, 'idAccount' => 2]);
        $commente->setComment($_POST['commentaire']);
        $commente->setDateComment(new DateTime());
        $em = $this->getDoctrine()->getManager();
        $em->persist($commente);
        $em->flush();

        return $this->redirectToRoute('detailproject', ["id" => $id]);
    }
}
