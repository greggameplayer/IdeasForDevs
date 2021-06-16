<?php

namespace App\Controller;

use App\Document\Avatar;
use App\Entity\Account;
use App\Entity\Job;
use App\Entity\Skill;
use App\Form\RegistrationFlow;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Repository\AccountRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\GridFS\ReadableStream;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
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
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, DocumentManager $dm)
    {

        $user = new Account();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {

            if($request->get("password")["first"] != $request->get("password")["second"]) {
                return $this->json(["error" => "Les mot de passe doivent être identique !"]);
            } else if(strlen($request->get("password")["first"]) < 8) {
                return $this->json(["error" => "Votre mot de passe doit faire au moins 8 caractères !"]);
            }

            /** @var UploadedFile $document */
            $document = $request->files->get("avatar");
            /** @var resource $stream */
            $stream = fopen($document->getPathname(), 'r');
            $id = $dm->getDocumentBucket(Avatar::class)->uploadFromStream($document->getClientOriginalName(), $stream);
            fclose($stream);



                /*$user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('ideastodev@gregoire.live', 'Contact IdeasToDev'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );*/
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
