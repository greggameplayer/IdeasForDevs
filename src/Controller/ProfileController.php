<?php

namespace App\Controller;


use App\Entity\Account;
use App\Entity\Job;
use App\Entity\JobsAccount;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }


    /**
     * @Route("/profile", name="profile")
     */
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'firstname' => $this->getUser()->getFirstname(),
            'lastname' => $this->getUser()->getLastname(),
            'birthDate' => $this->getUser()->getBirthDate(),
            'email' => $this->getUser()->getEmail()
        ]);
    }

    /**
     * @Route("/api/user/jobs", name="userjobs", methods={"GET"})
     */
    public function getCurrentUserJobs() : Response
    {
        $result = array_map(function($o) { return $o->getJob()->getId();}, $this->getDoctrine()->getRepository(JobsAccount::class)->findBy(['account' => $this->getUser()]));
        return $this->json($result);
    }

    /**
     * @Route("/user/personalInfos", name="modifyPersonalInfos", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function modifyPersonalInfos(Request $request): JsonResponse
    {
        $rq = json_decode($request->getContent());

        $entityManager = $this->getDoctrine()->getManager();
        /** @var Account $user */
        $user = $this->getUser();
        $user->setFirstname($rq->firstname);
        $user->setLastname($rq->lastname);
        $user->setBirthDate(new DateTime($rq->birthDate));

        $entityManager->persist($user);

        $this->getDoctrine()->getRepository(JobsAccount::class)->removeAllJobsAccountForAUser($user);

        foreach($rq->jobs as $job) {
            $jobobj = new JobsAccount();
            $jobobj->setAccount($user);
            $jobobj->setJob($this->getDoctrine()->getRepository(Job::class)->find($job));
            $entityManager->persist($jobobj);
        }

        $entityManager->flush();

        return $this->json(["message" => "ok"]);
    }

    /**
     * @Route("/user/password", name="modifyPassword", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function modifyPassword(Request $request) : JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        /** @var Account $user */
        $user = $this->getUser();
        $rq = json_decode($request->getContent());

        if (!$this->passwordEncoder->isPasswordValid($user, $rq->oldpassword)) {
            return $this->json(["error" => ["message" => "Votre mot de passe n'est pas correct !", "type" => "oldpassword"]]);
        } else if ($rq->password != $rq->confirmpassword) {
            return $this->json(["error" => ["message" => "Les mot de passe doivent être identique !", "type" => "newpassword"]]);
        } else if (strlen($rq->password) < 8) {
            return $this->json(["error" => ["message" => "Le nouveau mot de passe doit faire au moins 8 caractères !", "type" => "newpassword"]]);
        }

        $user->setPassword($this->passwordEncoder->encodePassword($user, $rq->password));

        $entityManager->persist($user);

        $entityManager->flush();

        return $this->json(["message" => "ok"]);
    }
}
