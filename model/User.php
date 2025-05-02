<?php
class Utilisateur{
    private ?int $id;
    private string $nom;
    private string $prenom;
    private int $age;
    private string $tel;
    private string $role;
    private string $email;
    private string $pwd;

    public function __construct(
        string $nom,
        string $prenom,
        int $age,
        string $tel,
        string $role,
        string $email,
        string $pwd,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->age = $age;
        $this->tel = $tel;
        $this->role = $role;
        $this->email = $email;
        $this->pwd = $pwd;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getAge(): int { return $this->age; }
    public function getTel(): string { return $this->tel; }
    public function getRole(): string { return $this->role; }
    public function getEmail(): string { return $this->email; }
    public function getPwd(): string { return $this->pwd; }

    // Setters
    public function setNom(string $nom): self { 
        $this->nom = $nom; 
        return $this; 
    }
    
    public function setPrenom(string $prenom): self { 
        $this->prenom = $prenom; 
        return $this; 
    }
    
    public function setAge(int $age): self { 
        $this->age = $age; 
        return $this; 
    }
    
    public function setTel(string $tel): self { 
        $this->tel = $tel;
        return $this; 
    }
    
    public function setRôle(string $role): self { 
        $this->role = $role; 
        return $this; 
    }
    
    public function setEmail(string $email): self { 
        $this->email = $email; 
        return $this; 
    }
    
    public function setPwd(string $pwd): self { 
        $this->pwd = $pwd; 
        return $this; 
    }
}
?>