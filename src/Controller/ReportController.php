<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Commentary;
use App\Entity\Project;
use App\Entity\ReportComment;
use App\Entity\ReportProject;
use App\Entity\ReportUser;
use App\Form\CommentReportType;
use App\Form\ProjectReportType;
use App\Form\UserReportType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    /**
     * @Route("/user/{id}/report", name="userReport")
     */
    public function userReport($id, Request $request): Response
    {
        /** @var Account $user */
        $user = $this->getUser();
        $userReport = new ReportUser();
        $userReport->setDateReport(new DateTime());
        $userReport->setReporter($user);
        $userReport->setReported($this->getDoctrine()->getRepository(Account::class)->find($id));

        $form = $this->createForm(UserReportType::class, $userReport);

        $form->handleRequest($request);

        if($form->isSubmitted() && $request->isMethod("POST") && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($userReport);
            $entityManager->flush();

            return $this->redirectToRoute("homepage");
        }

        return $this->render('report/index.html.twig', [
            'reportForm' => $form->createView(),
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0])
        ]);
    }

    /**
     * @Route("/project/{id}/report", name="projectReport")
     */
    public function projectReport($id, Request $request) : Response
    {
        /** @var Account $user */
        $user = $this->getUser();
        $projectReport = new ReportProject();
        $projectReport->setDateReport(new DateTime());
        $projectReport->setAccount($user);
        $projectReport->setProject($this->getDoctrine()->getRepository(Project::class)->find($id));

        $form = $this->createForm(ProjectReportType::class, $projectReport);

        $form->handleRequest($request);

        if($form->isSubmitted() && $request->isMethod("POST") && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($projectReport);
            $entityManager->flush();

            return $this->redirectToRoute("homepage");
        }

        return $this->render('report/index.html.twig', [
            'reportForm' => $form->createView(),
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0])
        ]);
    }

    /**
     * @Route("/comment/{id}/report", name="commentReport")
     */
    public function commentReport($id, Request $request) : Response
    {
        /** @var Account $user */
        $user = $this->getUser();

        $commentReport = new ReportComment();
        $commentReport->setDateReport(new DateTime());
        $commentReport->setAccount($user);
        $commentReport->setCommentary($this->getDoctrine()->getRepository(Commentary::class)->find($id));

        $form = $this->createForm(CommentReportType::class, $commentReport);

        $form->handleRequest($request);

        if($form->isSubmitted() && $request->isMethod("POST") && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commentReport);
            $entityManager->flush();

            return $this->redirectToRoute("homepage");
        }

        return $this->render('report/index.html.twig', [
            'reportForm' => $form->createView(),
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0])
        ]);
    }
}
