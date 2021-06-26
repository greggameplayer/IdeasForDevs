<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=AccountRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Account implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthDate;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $idMongo;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $tempAvatar;

    /**
     * @ORM\Column(type="datetime")
     */
    private $subscribeDate;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $skills = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $role = [];

    /**
     * @ORM\OneToMany(targetEntity=Apply::class, mappedBy="idAccount", orphanRemoval=true)
     */
    private $applies;

    /**
     * @ORM\OneToMany(targetEntity=Commentary::class, mappedBy="idAccount")
     */
    private $commentaries;


    /**
     * @ORM\OneToMany(targetEntity=IsFor::class, mappedBy="idAccount")
     */
    private $isFors;

    /**
     * @ORM\OneToMany(targetEntity=ReportUser::class, mappedBy="reporter", orphanRemoval=true)
     */
    private $reporters;

    /**
     * @ORM\OneToMany(targetEntity=ReportUser::class, mappedBy="reported", orphanRemoval=true)
     */
    private $reporteds;

    /**
     * @ORM\OneToMany(targetEntity=ReportComment::class, mappedBy="account", orphanRemoval=true)
     */
    private $reportComments;

    /**
     * @ORM\OneToMany(targetEntity=ReportProject::class, mappedBy="account", orphanRemoval=true)
     */
    private $reportProjects;

    /**
     * @ORM\OneToMany(targetEntity=JobsAccount::class, mappedBy="account", orphanRemoval=true)
     */
    private $jobsAccounts;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=Project::class, mappedBy="account")
     */
    private $projects;



    public function __construct()
    {
        $this->applies = new ArrayCollection();
        $this->commentaries = new ArrayCollection();
        $this->isFors = new ArrayCollection();
        $this->reporters = new ArrayCollection();
        $this->reporteds = new ArrayCollection();
        $this->reportComments = new ArrayCollection();
        $this->reportProjects = new ArrayCollection();
        $this->jobsAccounts = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getIdMongo(): ?string
    {
        return $this->idMongo;
    }

    public function setIdMongo(?string $idMongo): self
    {
        $this->idMongo = $idMongo;

        return $this;
    }

    public function getTempAvatar() : ?string
    {
        return $this->tempAvatar;
    }

    public function setTempAvatar(?string $tempAvatar): self
    {
        $this->tempAvatar = $tempAvatar;

        return $this;
    }

    public function getSubscribeDate(): ?\DateTimeInterface
    {
        return $this->subscribeDate;
    }

    public function setSubscribeDate(\DateTimeInterface $subscribeDate): self
    {
        $this->subscribeDate = $subscribeDate;

        return $this;
    }

    public function getSkills(): ?array
    {
        return $this->skills;
    }

    public function setSkills(?array $skills): self
    {
        $this->skills = $skills;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->role;
    }

    public function setRoles(?array $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection|Apply[]
     */
    public function getApplies(): Collection
    {
        return $this->applies;
    }

    public function addApply(Apply $apply): self
    {
        if (!$this->applies->contains($apply)) {
            $this->applies[] = $apply;
            $apply->setIdAccount($this);
        }

        return $this;
    }

    public function removeApply(Apply $apply): self
    {
        if ($this->applies->removeElement($apply)) {
            // set the owning side to null (unless already changed)
            if ($apply->getIdAccount() === $this) {
                $apply->setIdAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Commentary[]
     */
    public function getCommentaries(): Collection
    {
        return $this->commentaries;
    }

    public function addCommentary(Commentary $commentary): self
    {
        if (!$this->commentaries->contains($commentary)) {
            $this->commentaries[] = $commentary;
            $commentary->setIdAccount($this);
        }

        return $this;
    }

    public function removeCommentary(Commentary $commentary): self
    {
        if ($this->commentaries->removeElement($commentary)) {
            // set the owning side to null (unless already changed)
            if ($commentary->getIdAccount() === $this) {
                $commentary->setIdAccount(null);
            }
        }

        return $this;
    }





    /**
     * @return Collection|IsFor[]
     */
    public function getIsFors(): Collection
    {
        return $this->isFors;
    }

    public function addIsFor(IsFor $isFor): self
    {
        if (!$this->isFors->contains($isFor)) {
            $this->isFors[] = $isFor;
            $isFor->setIdAccount($this);
        }

        return $this;
    }

    public function removeIsFor(IsFor $isFor): self
    {
        if ($this->isFors->removeElement($isFor)) {
            // set the owning side to null (unless already changed)
            if ($isFor->getIdAccount() === $this) {
                $isFor->setIdAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReportUser[]
     */
    public function getReporters(): Collection
    {
        return $this->reporters;
    }

    public function addReporter(ReportUser $reporter): self
    {
        if (!$this->reporters->contains($reporter)) {
            $this->reporters[] = $reporter;
            $reporter->setReporter($this);
        }

        return $this;
    }

    public function removeReporter(ReportUser $reporter): self
    {
        if ($this->reporters->removeElement($reporter)) {
            // set the owning side to null (unless already changed)
            if ($reporter->getReporter() === $this) {
                $reporter->setReporter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReportUser[]
     */
    public function getReporteds(): Collection
    {
        return $this->reporteds;
    }

    public function addReported(ReportUser $reported): self
    {
        if (!$this->reporteds->contains($reported)) {
            $this->reporteds[] = $reported;
            $reported->setReported($this);
        }

        return $this;
    }

    public function removeReported(ReportUser $reported): self
    {
        if ($this->reporteds->removeElement($reported)) {
            // set the owning side to null (unless already changed)
            if ($reported->getReported() === $this) {
                $reported->setReported(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReportComment[]
     */
    public function getReportComments(): Collection
    {
        return $this->reportComments;
    }

    public function addReportComment(ReportComment $reportComment): self
    {
        if (!$this->reportComments->contains($reportComment)) {
            $this->reportComments[] = $reportComment;
            $reportComment->setAccount($this);
        }

        return $this;
    }

    public function removeReportComment(ReportComment $reportComment): self
    {
        if ($this->reportComments->removeElement($reportComment)) {
            // set the owning side to null (unless already changed)
            if ($reportComment->getAccount() === $this) {
                $reportComment->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReportProject[]
     */
    public function getReportProjects(): Collection
    {
        return $this->reportProjects;
    }

    public function addReportProject(ReportProject $reportProject): self
    {
        if (!$this->reportProjects->contains($reportProject)) {
            $this->reportProjects[] = $reportProject;
            $reportProject->setAccount($this);
        }

        return $this;
    }

    public function removeReportProject(ReportProject $reportProject): self
    {
        if ($this->reportProjects->removeElement($reportProject)) {
            // set the owning side to null (unless already changed)
            if ($reportProject->getAccount() === $this) {
                $reportProject->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|JobsAccount[]
     */
    public function getJobsAccounts(): Collection
    {
        return $this->jobsAccounts;
    }

    public function addJobsAccount(JobsAccount $jobsAccount): self
    {
        if (!$this->jobsAccounts->contains($jobsAccount)) {
            $this->jobsAccounts[] = $jobsAccount;
            $jobsAccount->setAccount($this);
        }

        return $this;
    }

    public function removeJobsAccount(JobsAccount $jobsAccount): self
    {
        if ($this->jobsAccounts->removeElement($jobsAccount)) {
            // set the owning side to null (unless already changed)
            if ($jobsAccount->getAccount() === $this) {
                $jobsAccount->setAccount(null);
            }
        }

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername(): string
    {
        return (string) $this->getEmail();
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setAccount($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getAccount() === $this) {
                $project->setAccount(null);
            }
        }

        return $this;
    }

}
