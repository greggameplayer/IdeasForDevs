<?php

namespace App\Entity;

use App\Repository\ReportProjectRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReportProjectRepository::class)
 */
class ReportProject
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="reportProjects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="reportProjects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $account;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateReport;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reason;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isTreated;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

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

    public function getDateReport(): ?\DateTimeInterface
    {
        return $this->dateReport;
    }

    public function setDateReport(\DateTimeInterface $dateReport): self
    {
        $this->dateReport = $dateReport;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIsTreated(): ?bool
    {
        return $this->isTreated;
    }

    public function setIsTreated(bool $isTreated): self
    {
        $this->isTreated = $isTreated;

        return $this;
    }
}
