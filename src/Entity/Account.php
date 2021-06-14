<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AccountRepository::class)
 */
class Account
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
     * @ORM\Column(type="datetime")
     */
    private $subscribeDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActivated;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $skills = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $job = [];

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
     * @ORM\OneToMany(targetEntity=ReportComment::class, mappedBy="idAccount", orphanRemoval=true)
     */
    private $reportComments;

    /**
     * @ORM\OneToMany(targetEntity=ReportProject::class, mappedBy="idAccount", orphanRemoval=true)
     */
    private $reportProjects;

    public function __construct()
    {
        $this->applies = new ArrayCollection();
        $this->commentaries = new ArrayCollection();
        $this->reportComments = new ArrayCollection();
        $this->reportProjects = new ArrayCollection();
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

    public function getSubscribeDate(): ?\DateTimeInterface
    {
        return $this->subscribeDate;
    }

    public function setSubscribeDate(\DateTimeInterface $subscribeDate): self
    {
        $this->subscribeDate = $subscribeDate;

        return $this;
    }

    public function getIsActivated(): ?bool
    {
        return $this->isActivated;
    }

    public function setIsActivated(bool $isActivated): self
    {
        $this->isActivated = $isActivated;

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

    public function getJob(): ?array
    {
        return $this->job;
    }

    public function setJob(?array $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getRole(): ?array
    {
        return $this->role;
    }

    public function setRole(?array $role): self
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
            $reportComment->setIdAccount($this);
        }

        return $this;
    }

    public function removeReportComment(ReportComment $reportComment): self
    {
        if ($this->reportComments->removeElement($reportComment)) {
            // set the owning side to null (unless already changed)
            if ($reportComment->getIdAccount() === $this) {
                $reportComment->setIdAccount(null);
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
            $reportProject->setIdAccount($this);
        }

        return $this;
    }

    public function removeReportProject(ReportProject $reportProject): self
    {
        if ($this->reportProjects->removeElement($reportProject)) {
            // set the owning side to null (unless already changed)
            if ($reportProject->getIdAccount() === $this) {
                $reportProject->setIdAccount(null);
            }
        }

        return $this;
    }
}
