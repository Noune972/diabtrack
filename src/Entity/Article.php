<?php
namespace App\Entity;

use App\Enum\ArticleStatus;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    private ?string $author = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\Column(enumType: ArticleStatus::class)]
    private ArticleStatus $status = ArticleStatus::DRAFT;

    // Une seule relation vers ArticleCategory (la seconde, dupliquée, a été supprimée)
    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ArticleCategory $category = null;

    /**
     * @var Collection<int, CommentArticle>
     */
    #[ORM\OneToMany(targetEntity: CommentArticle::class, mappedBy: 'article')]
    private Collection $comment_article;

    public function __construct()
    {
        $this->comment_article = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;
        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getStatus(): ArticleStatus
    {
        return $this->status;
    }

    public function setStatus(ArticleStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getCategory(): ?ArticleCategory
    {
        return $this->category;
    }

    public function setCategory(?ArticleCategory $category): static
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Collection<int, CommentArticle>
     */
    public function getCommentArticle(): Collection
    {
        return $this->comment_article;
    }

    public function addCommentArticle(CommentArticle $commentArticle): static
    {
        if (!$this->comment_article->contains($commentArticle)) {
            $this->comment_article->add($commentArticle);
            $commentArticle->setArticle($this);
        }
        return $this;
    }

    public function removeCommentArticle(CommentArticle $commentArticle): static
    {
        if ($this->comment_article->removeElement($commentArticle)) {
            if ($commentArticle->getArticle() === $this) {
                $commentArticle->setArticle(null);
            }
        }
        return $this;
    }
}