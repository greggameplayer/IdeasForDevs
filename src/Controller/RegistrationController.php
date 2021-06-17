<?php

namespace App\Controller;

use App\Document\Avatar;
use App\Entity\Account;
use App\Entity\Job;
use App\Entity\JobsAccount;
use App\Entity\Skill;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Repository\AccountRepository;
use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use MongoDB\BSON\ObjectId;
use MongoDB\GridFS\ReadableStream;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    private $filesystem;

    public function __construct(EmailVerifier $emailVerifier, Filesystem $filesystem)
    {
        $this->emailVerifier = $emailVerifier;
        $this->filesystem = $filesystem;
    }

    /**
     * @Route("/api/jobs", name="jobs", methods={"GET"})
     */
    public function getJobs() : Response
    {
        return $this->json($this->getDoctrine()->getRepository(Job::class)->findAll());
    }

    /**
     * @Route("/api/skills", name="skills", methods={"GET"})
     */
    public function getSkills() : Response
    {
        return $this->json($this->getDoctrine()->getRepository(Skill::class)->findAll());
    }

    /**
     * @Route("/register", name="app_register")
     * @throws MongoDBException
     * @throws \Exception
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, DocumentManager $dm)
    {
        $user = new Account();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {

            /** @var UploadedFile $document */
            $document = $request->files->get("avatar");

            if($this->getDoctrine()->getRepository(Account::class)->findOneBy(["email" => $request->get("email")]) != null) {
                $this->filesystem->remove($document->getPathname());
                return $this->json(["error" => "L'email est déjà utilisé !"]);
            } else if($request->get("password")["first"] != $request->get("password")["second"]) {
                $this->filesystem->remove($document->getPathname());
                return $this->json(["error" => "Les mot de passe doivent être identique !"]);
            } else if(strlen($request->get("password")["first"]) < 8) {
                $this->filesystem->remove($document->getPathname());
                return $this->json(["error" => "Votre mot de passe doit faire au moins 8 caractères !"]);
            } else if(!in_array($document->guessExtension(), ["png", "jpg", "jpeg", "gif"])) {
                $this->filesystem->remove($document->getPathname());
                return $this->json(["error" => "Le format du fichier pour l'avatar que vous avez envoyé n'est pas supporté !"]);
            } else if($document->getSize() > 2000000) {
                $this->filesystem->remove($document->getPathname());
                return $this->json(["error" => "Votre fichier avatar pése plus de 2 Mo !"]);
            }

            /** @var resource $stream */
            $stream = fopen($document->getPathname(), 'r');

            /** @var ObjectId $id */
            $id = $dm->getDocumentBucket(Avatar::class)->uploadFromStream($document->getClientOriginalName(), $stream);
            fclose($stream);

            $this->filesystem->remove($document->getPathname());

            $user = new Account();

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $request->get('password')['first']
                )
            );

            $user->setBirthDate(new DateTime($request->get("birthDate")));
            $user->setEmail($request->get("email"));
            $user->setFirstname($request->get("firstname"));
            $user->setLastname($request->get("lastname"));
            $user->setRoles(["ROLE_USER"]);
            $user->setSubscribeDate(new DateTime());
            $user->setSkills(json_decode($request->get("skills")));
            $user->setIdMongo($id->serialize());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);

            foreach(json_decode($request->get("jobs")) as $job) {
                $jobobj = new JobsAccount();
                $jobobj->setAccount($user);
                $jobobj->setJob($this->getDoctrine()->getRepository(Job::class)->find($job));
                $entityManager->persist($jobobj);
            }

            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('ideastodev@gregoire.live', 'Contact IdeasToDev'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            return $this->json(["message" => "good !", "avatarId" => $id]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0])
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, AccountRepository $accountRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $accountRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('homepage');
    }
}
