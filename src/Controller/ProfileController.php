<?php

namespace App\Controller;


use App\Document\Avatar;
use App\Entity\Account;
use App\Entity\Job;
use App\Entity\JobsAccount;
use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Exception;
use MongoDB\BSON\ObjectId;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    private $passwordEncoder;

    private $filesystem;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, Filesystem $filesystem) {
        $this->passwordEncoder = $passwordEncoder;
        $this->filesystem = $filesystem;
    }


    /**
     * @Route("/profile", name="profile")
     * @throws MongoDBException
     */
    public function index(DocumentManager $dm): Response
    {
        /** @var Account $user */
        $user = $this->getUser();

        $oid = new ObjectId(); // create Mongo ObjectId
        $oid->unserialize($user->getIdMongo()); // unserialize user mongo avatar id

        $tempfile = $this->filesystem->tempnam($this->getParameter('kernel.project_dir') . "/public/uploads", "avt", '.png'); // create a temporary file in /public/uploads to store and use the avatar image
        $this->filesystem->chmod($tempfile, 0777); // give max permission on this temp file
        $stream = fopen($tempfile, "w+"); // open a file stream to write this temporary file
        try {
            $dm->getDocumentBucket(Avatar::class)->downloadToStream($oid, $stream); // download the user avatar image through mongodb with it's id and store it in the temp file
        } finally {
            fclose($stream); // close the file stream
        }


        return $this->render('profile/index.html.twig', [
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'birthDate' => $user->getBirthDate(),
            'email' => $user->getEmail(),
            'avatar' => 'uploads/' . basename($tempfile)
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
