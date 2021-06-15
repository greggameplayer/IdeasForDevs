<?php

namespace App\Entity;

use App\Repository\JobRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JobRepository::class)
 */
class Job
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
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=JobsAccount::class, mappedBy="job", orphanRemoval=true)
     */
    private $jobsAccounts;

    public function __construct()
    {
        $this->jobsAccounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $jobsAccount->setJob($this);
        }

        return $this;
    }

    public function removeJobsAccount(JobsAccount $jobsAccount): self
    {
        if ($this->jobsAccounts->removeElement($jobsAccount)) {
            // set the owning side to null (unless already changed)
            if ($jobsAccount->getJob() === $this) {
                $jobsAccount->setJob(null);
            }
        }

        return $this;
    }
}
