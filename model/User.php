<?php

namespace EmilieSchott\BlogPHP\Model;

class User extends Hydrate
{
    private $id;
    private $role;
    private $pseudo;
    private $name;
    private $firstName;
    private $email;
    private $password;

    public function getId(): int
    {
        return $this->id;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setRole(string $role)
    {
        $this->role = $role;
    }

    public function setPseudo(string $pseudo)
    {
        if (\strlen($pseudo) <= 45) {
            $this->pseudo = htmlspecialchars($pseudo);
        } else {
            throw new \Exception("Le pseudo doit faire moins de 45 caractères.");
        }
    }

    public function setName(string $name)
    {
        if (\strlen($name) <= 45) {
            $this->name = htmlspecialchars($name);
        } else {
            throw new \Exception("Le nom doit faire moins de 45 caractères.");
        }
    }

    public function setFirstName(string $firstName)
    {
        if (\strlen($firstName) <= 15) {
            $this->firstName = htmlspecialchars($firstName);
        } else {
            throw new \Exception("Le prénom doit faire moins de 15 caractères.");
        }
    }

    public function setEmail(string $email)
    {
        if (\strlen($email) <= 45) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->email = htmlspecialchars($email);
            } else {
                throw new \Exception("L'email n'est pas valide.");
            }
        } else {
            throw new \Exception("L'email doit faire moins de 45 caractères.");
        }
    }

    public function setPassword(string $password)
    {
        if (\strlen($password) <= 255) {
            $this->password = $password;
        } else {
            throw new \Exception("Le mot de passe doit faire moins de 255 caractères.");
        }
    }
}
