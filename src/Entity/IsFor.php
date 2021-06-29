<?php

namespace App\Entity;

use App\Repository\IsForRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IsForRepository::class)
 */
class IsFor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="isFors")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idAccount;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="isFors")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idProject;

    /**
     * @ORM\Column(type="boolean")
     */
    private $evaluation;

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

    public function getIdProject(): ?Project
    {
        return $this->idProject;
    }

    public function setIdProject(?Project $idProject): self
    {
        $this->idProject = $idProject;

        return $this;
    }

    public function getEvaluation(): ?bool
    {
        return $this->evaluation;
    }

    public function setEvaluation(bool $evaluation): self
    {
        $this->evaluation = $evaluation;

        return $this;
    }
}
