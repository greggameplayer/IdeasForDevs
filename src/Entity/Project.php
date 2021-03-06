<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $repo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $idMongo;

    /**
     * @ORM\Column(type="string", length=800, nullable=true)
     */
    private $tempImage;

    /**
     * @ORM\ManyToOne (targetEntity=Status::class)
     */
    private $status;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $skillsNeeded = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $jobNeeded = [];

    /**
     * @ORM\OneToMany(targetEntity=Apply::class, mappedBy="idProject", orphanRemoval=true)
     */
    private $applies;

    /**
     * @ORM\OneToMany(targetEntity=Commentary::class, mappedBy="idProject", orphanRemoval=true)
     */
    private $commentaries;

    /**
     * @ORM\OneToMany(targetEntity=IsFor::class, mappedBy="idProject", orphanRemoval=true)
     */
    private $isFors;

    /**
     * @ORM\OneToMany(targetEntity=ReportProject::class, mappedBy="project", orphanRemoval=true)
     */
    private $reportProjects;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="projects")
     */
    private $account;

    public function __construct()
    {
        $this->applies = new ArrayCollection();
        $this->commentaries = new ArrayCollection();
        $this->isFors = new ArrayCollection();
        $this->reportProjects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRepo(): ?string
    {
        return $this->repo;
    }

    public function setRepo(?string $repo): self
    {
        $this->repo = $repo;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

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

    public function getTempImage(): ?string
    {
        return $this->tempImage;
    }

    public function setTempImage(?string $tempImage): self
    {
        $this->tempImage = $tempImage;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSkillsNeeded(): ?array
    {
        return $this->skillsNeeded;
    }

    public function setSkillsNeeded(?array $skillsNeeded): self
    {
        $this->skillsNeeded = $skillsNeeded;

        return $this;
    }

    public function getJobNeeded(): ?array
    {
        return $this->jobNeeded;
    }

    public function setJobNeeded(?array $jobNeeded): self
    {
        $this->jobNeeded = $jobNeeded;

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
            $apply->setIdProject($this);
        }

        return $this;
    }

    public function removeApply(Apply $apply): self
    {
        if ($this->applies->removeElement($apply)) {
            // set the owning side to null (unless already changed)
            if ($apply->getIdProject() === $this) {
                $apply->setIdProject(null);
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
            $commentary->setIdProject($this);
        }

        return $this;
    }

    public function removeCommentary(Commentary $commentary): self
    {
        if ($this->commentaries->removeElement($commentary)) {
            // set the owning side to null (unless already changed)
            if ($commentary->getIdProject() === $this) {
                $commentary->setIdProject(null);
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
            $isFor->setIdProject($this);
        }

        return $this;
    }

    public function removeIsFor(IsFor $isFor): self
    {
        if ($this->isFors->removeElement($isFor)) {
            // set the owning side to null (unless already changed)
            if ($isFor->getIdProject() === $this) {
                $isFor->setIdProject(null);
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
            $reportProject->setProject($this);
        }

        return $this;
    }

    public function removeReportProject(ReportProject $reportProject): self
    {
        if ($this->reportProjects->removeElement($reportProject)) {
            // set the owning side to null (unless already changed)
            if ($reportProject->getProject() === $this) {
                $reportProject->setProject(null);
            }
        }

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }
}
