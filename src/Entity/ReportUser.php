<?php

namespace App\Entity;

use App\Repository\ReportUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReportUserRepository::class)
 */
class ReportUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="reporters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idReporter;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="reportedUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idReported;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdReporter(): ?Account
    {
        return $this->idReporter;
    }

    public function setIdReporter(?Account $idReporter): self
    {
        $this->idReporter = $idReporter;

        return $this;
    }

    public function getIdReported(): ?Account
    {
        return $this->idReported;
    }

    public function setIdReported(?Account $idReported): self
    {
        $this->idReported = $idReported;

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
}
