<?php

namespace App\Entity;

use App\Enum\Gender;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;


    #[ORM\Column(enumType: Gender::class)]
    private ?Gender $gender = null;

    #[ORM\Column]
    private ?int $weight = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateOfBirth = null;
    #[ORM\Column]
    private bool $isVerified = false;

    /**
     * @var Collection<int, BloodSugar>
     */
    #[ORM\OneToMany(targetEntity: BloodSugar::class, mappedBy: 'patient')]
    private Collection $bloodSugars;

    /**
     * @var Collection<int, SportingActivity>
     */
    #[ORM\OneToMany(targetEntity: SportingActivity::class, mappedBy: 'patient')]
    private Collection $sportingActivitys;

    /**
     * @var Collection<int, Insuline>
     */
    #[ORM\OneToMany(targetEntity: Insuline::class, mappedBy: 'patient')]
    private Collection $insuline;

    /**
     * @var Collection<int, Meal>
     */
    #[ORM\OneToMany(targetEntity: Meal::class, mappedBy: 'patient')]
    private Collection $meal;

    /**
     * @var Collection<int, Hba1c>
     */
    #[ORM\OneToMany(targetEntity: Hba1c::class, mappedBy: 'patient')]
    private Collection $HBA1C;

    /**
     * @var Collection<int, Reminder>
     */
    #[ORM\OneToMany(targetEntity: Reminder::class, mappedBy: 'patient')]
    private Collection $reminder;

    /**
     * @var Collection<int, Topic>
     */
    #[ORM\OneToMany(targetEntity: Topic::class, mappedBy: 'patient')]
    private Collection $topic;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'patient')]
    private Collection $comment;

    /**
     * @var Collection<int, CommentArticle>
     */
    #[ORM\OneToMany(targetEntity: CommentArticle::class, mappedBy: 'patient')]
    private Collection $comment_article;

    public function __construct()
    {
        $this->bloodSugars = new ArrayCollection();
        $this->sportingActivitys = new ArrayCollection();
        $this->insuline = new ArrayCollection();
        $this->meal = new ArrayCollection();
        $this->HBA1C = new ArrayCollection();
        $this->reminder = new ArrayCollection();
        $this->topic = new ArrayCollection();
        $this->comment = new ArrayCollection();
        $this->comment_article = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
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
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(Gender $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTime $date_of_birth): static
    {
        $this->dateOfBirth = $date_of_birth;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, BloodSugar>
     */
    public function getBloodSugars(): Collection
    {
        return $this->bloodSugars;
    }

    public function addBloodSugar(BloodSugar $bloodSugar): static
    {
        if (!$this->bloodSugars->contains($bloodSugar)) {
            $this->bloodSugars->add($bloodSugar);
            $bloodSugar->setPatient($this);
        }

        return $this;
    }

    public function removeBloodSugar(BloodSugar $bloodSugar): static
    {
        if ($this->bloodSugars->removeElement($bloodSugar)) {
            // set the owning side to null (unless already changed)
            if ($bloodSugar->getPatient() === $this) {
                $bloodSugar->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SportingActivity>
     */
    public function getSportingActivitys(): Collection
    {
        return $this->sportingActivitys;
    }

    public function addSportingActivity(SportingActivity $sportingActivity): static
    {
        if (!$this->sportingActivitys->contains($sportingActivity)) {
            $this->sportingActivitys->add($sportingActivity);
            $sportingActivity->setPatient($this);
        }

        return $this;
    }

    public function removeSportingActivity(SportingActivity $sportingActivity): static
    {
        if ($this->sportingActivitys->removeElement($sportingActivity)) {
            // set the owning side to null (unless already changed)
            if ($sportingActivity->getPatient() === $this) {
                $sportingActivity->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Insuline>
     */
    public function getInsuline(): Collection
    {
        return $this->insuline;
    }

    public function addInsuline(Insuline $insuline): static
    {
        if (!$this->insuline->contains($insuline)) {
            $this->insuline->add($insuline);
            $insuline->setPatient($this);
        }

        return $this;
    }

    public function removeInsuline(Insuline $insuline): static
    {
        if ($this->insuline->removeElement($insuline)) {
            // set the owning side to null (unless already changed)
            if ($insuline->getPatient() === $this) {
                $insuline->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Meal>
     */
    public function getMeal(): Collection
    {
        return $this->meal;
    }

    public function addMeal(Meal $meal): static
    {
        if (!$this->meal->contains($meal)) {
            $this->meal->add($meal);
            $meal->setPatient($this);
        }

        return $this;
    }

    public function removeMeal(Meal $meal): static
    {
        if ($this->meal->removeElement($meal)) {
            // set the owning side to null (unless already changed)
            if ($meal->getPatient() === $this) {
                $meal->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Hba1c>
     */
    public function getHBA1C(): Collection
    {
        return $this->HBA1C;
    }

    public function addHBA1C(Hba1c $hBA1C): static
    {
        if (!$this->HBA1C->contains($hBA1C)) {
            $this->HBA1C->add($hBA1C);
            $hBA1C->setPatient($this);
        }

        return $this;
    }

    public function removeHBA1C(Hba1c $hBA1C): static
    {
        if ($this->HBA1C->removeElement($hBA1C)) {
            // set the owning side to null (unless already changed)
            if ($hBA1C->getPatient() === $this) {
                $hBA1C->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reminder>
     */
    public function getReminder(): Collection
    {
        return $this->reminder;
    }

    public function addReminder(Reminder $reminder): static
    {
        if (!$this->reminder->contains($reminder)) {
            $this->reminder->add($reminder);
            $reminder->setPatient($this);
        }

        return $this;
    }

    public function removeReminder(Reminder $reminder): static
    {
        if ($this->reminder->removeElement($reminder)) {
            // set the owning side to null (unless already changed)
            if ($reminder->getPatient() === $this) {
                $reminder->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Topic>
     */
    public function getTopic(): Collection
    {
        return $this->topic;
    }

    public function addTopic(Topic $topic): static
    {
        if (!$this->topic->contains($topic)) {
            $this->topic->add($topic);
            $topic->setPatient($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): static
    {
        if ($this->topic->removeElement($topic)) {
            // set the owning side to null (unless already changed)
            if ($topic->getPatient() === $this) {
                $topic->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComment(): Collection
    {
        return $this->comment;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comment->contains($comment)) {
            $this->comment->add($comment);
            $comment->setPatient($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comment->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPatient() === $this) {
                $comment->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CommentArticle>
     */
    public function getCommentArticle(): Collection
    {
        return $this->comment_article;
    }

    public function addCommentArticle(commentArticle $commentArticle): static
    {
        if (!$this->comment_article->contains($commentArticle)) {
            $this->comment_article->add($commentArticle);
            $commentArticle->setPatient($this);
        }

        return $this;
    }

    public function removeCommentArticle(commentArticle $commentArticle): static
    {
        if ($this->comment_article->removeElement($commentArticle)) {
            // set the owning side to null (unless already changed)
            if ($commentArticle->getPatient() === $this) {
                $commentArticle->setPatient(null);
            }
        }

        return $this;
    }
}