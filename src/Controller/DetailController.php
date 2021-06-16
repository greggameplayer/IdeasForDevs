<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Apply;
use App\Entity\Commentary;
use App\Entity\IsFor;
use App\Entity\Project;
use ArrayObject;
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

        foreach ($IdsOfAdmins as $idAdmin){
            $detailsAdminProjectForEachAdmin->append($this->getDoctrine()->getRepository(Account::class)->detailsAdminProjectForEachAdmin($idAdmin));
            $countProjectAsAdminForEachAdmin->append($this->getDoctrine()->getRepository(Apply::class)->countProjectAsAdmin($idAdmin));
            $countProjectParticipationForEachAdmin->append($this->getDoctrine()->getRepository(Apply::class)->countProjectParticipation($idAdmin));
            $countProjectSuccessfullForEachAdmin->append($this->getDoctrine()->getRepository(Apply::class)->countProjectSuccessfull($idAdmin));
        }

        return $this->render('detail/project.html.twig', [
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0]),
            'detailsProject' => $this->getDoctrine()->getRepository(Project::class)->detailsProject($id),
            'commentaries' => $this->getDoctrine()->getRepository(Commentary::class)->GetCommentariesAndUser($id),
            'countFor' => $this->getDoctrine()->getRepository(IsFor::class)->countVotesFor($id),
            'countAgainst' => $this->getDoctrine()->getRepository(IsFor::class)->countVotesAgainst($id),
            'detailsAdminProjectForEachAdmin' => $detailsAdminProjectForEachAdmin,
            'countProjectAsAdminForEachAdmin' => $countProjectAsAdminForEachAdmin,
            'countProjectParticipationForEachAdmin' => $countProjectParticipationForEachAdmin,
            'countProjectSuccessfullForEachAdmin' => $countProjectSuccessfullForEachAdmin,
        ]);
    }
}
