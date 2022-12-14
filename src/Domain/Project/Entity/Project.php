<?php

declare(strict_types=1);

namespace App\Domain\Project\Entity;

use App\Core\Doctrine\Dbal\Types\Types as AppTypes;
use App\Core\Entity\Behavior\IdentifiableInterface;
use App\Core\Entity\Behavior\IdentifiableTrait;
use App\Core\Entity\Behavior\SluggableInterface;
use App\Core\Entity\Behavior\SluggableTrait;
use App\Core\Entity\Behavior\TimestampableInterface;
use App\Core\Entity\Behavior\TimestampableTrait;
use App\Domain\Project\Enum\ProjectType;
use App\Domain\Project\Model\MetadataInterface;
use App\Domain\Project\Repository\ProjectRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'project')]
#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(columns: ['title'])]
class Project implements IdentifiableInterface, SluggableInterface, TimestampableInterface, \Stringable
{
    use IdentifiableTrait;
    use SluggableTrait;
    use TimestampableTrait;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $subTitle = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, enumType: ProjectType::class)]
    private ProjectType $type = ProjectType::Customer;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $url = null;

    /**
     * @var \App\Domain\Project\Model\MetadataInterface<\App\Domain\Project\Model\GitHubProject>|null
     */
    #[ORM\Column(type: AppTypes::PROJECT_METADATA, nullable: true, options: ['jsonb' => true, 'default' => '{}'])]
    private ?MetadataInterface $metadata = null;

    public function __toString(): string
    {
        return $this->title ?? '';
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSubTitle(): ?string
    {
        return $this->subTitle;
    }

    public function setSubTitle(string $subTitle): self
    {
        $this->subTitle = $subTitle;

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

    public function getType(): ProjectType
    {
        return $this->type;
    }

    public function setType(ProjectType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isCustomer(): bool
    {
        return ProjectType::Customer === $this->type;
    }

    public function isGitHub(): bool
    {
        return ProjectType::GitHub === $this->type;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return \App\Domain\Project\Model\MetadataInterface<\App\Domain\Project\Model\GitHubProject>|null
     */
    public function getMetadata(): ?MetadataInterface
    {
        return $this->metadata;
    }

    /**
     * @param \App\Domain\Project\Model\MetadataInterface<\App\Domain\Project\Model\GitHubProject>|null $metadata
     */
    public function setMetadata(?MetadataInterface $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function getSluggableFields(): array
    {
        return ['title'];
    }
}
