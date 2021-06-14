<?php

namespace App\Entity;

use App\Repository\ReportCommentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReportCommentRepository::class)
 */
class ReportComment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="reportComments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idAccount;

    /**
     * @ORM\ManyToOne(targetEntity=Commentary::class, inversedBy="reportComments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idComment;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reason;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateReport;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAccount(): ?Account
    {
        return $this->idAccount;
    }

    public function setIdAccount(?Account $idAccount): self
    {
        $this->idAccount = $idAccount;

        return $this;
    }

    public function getIdComment(): ?Commentary
    {
        return $this->idComment;
    }

    public function setIdComment(?Commentary $idComment): self
    {
        $this->idComment = $idComment;

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

    public function getDateReport(): ?\DateTimeInterface
    {
        return $this->dateReport;
    }

    public function setDateReport(\DateTimeInterface $dateReport): self
    {
        $this->dateReport = $dateReport;

        return $this;
    }
}
