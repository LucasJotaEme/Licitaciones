<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;
    
    /**
     * @ORM\Column(type="string",nullable=true, length=180)
     */
    
    private $repetirPassword;
    
    /**
     * @ORM\Column(type="string", length=180)
     */
    
    private $rolForm;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $ultimoAcceso;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sistema", mappedBy="usuarios")
     */
    private $sistemas;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $estado;


        public function __construct()
    {
        $this->sistemas = new ArrayCollection();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    
    
    function getRolForm() {
        return $this->rolForm;
    }

    function setRolForm($rolForm) {
        $this->rolForm = $rolForm;
    }


    
        /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUltimoAcceso(): ?\DateTimeInterface
    {
        return $this->ultimoAcceso;
    }

    public function setUltimoAcceso(?\DateTimeInterface $ultimoAcceso): self
    {
        $this->ultimoAcceso = $ultimoAcceso;

        return $this;
    }

    /**
     * @return Collection|Sistema[]
     */
    public function getSistemas(): Collection
    {
        return $this->sistemas;
    }

    public function addSistema(Sistema $sistema): self
    {
        if (!$this->sistemas->contains($sistema)) {
            $this->sistemas[] = $sistema;
            $sistema->addUsuario($this);
        }

        return $this;
    }
    
    
    public function removeSistema(Sistema $sistema): self
    {
        if ($this->sistemas->contains($sistema)) {
            $this->sistemas->removeElement($sistema);
        }

        return $this;
    }
    
    public function updateSistemas($sistema){
        if (!$this->sistemas->contains($sistema)) {
            
            $this->sistemas[] = $sistema;
            $sistema->addUsuario($this);
            
        }
        else{
            
            $this->sistemas->removeElement($sistema);
            
        }
    }
    
    function getRepetirPassword() {
        return $this->repetirPassword;
    }

    function setRepetirPassword($repetirPassword) {
        $this->repetirPassword = $repetirPassword;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }


}
