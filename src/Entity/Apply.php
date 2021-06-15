<?php

namespace App\Entity;

use App\Repository\ApplyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApplyRepository::class)
 */
class Apply
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="applies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idAccount;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="applies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idProject;


    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=RoleProject::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $roleProject;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRoleProject(): ?RoleProject
    {
        return $this->roleProject;
    }

    public function setRoleProject(?RoleProject $roleProject): self
    {
        $this->roleProject = $roleProject;

        return $this;
    }
}
