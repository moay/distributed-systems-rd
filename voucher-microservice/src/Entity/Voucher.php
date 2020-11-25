<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

/**
 * @ORM\Entity()
 * @ORM\Table(name="vouchers")
 * @ORM\HasLifecycleCallbacks()
 */
class Voucher
{
    const TYPE_RELATIVE = 1;
    const TYPE_ABSOLUTE = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $relatedOrderId;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     *
     * @var string
     */
    private $voucherCode;

    /**
     * @ORM\Column(type="float", precision=2)
     *
     * @var float
     */
    private $value;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $type;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $creationStrategy;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $redeemed;

    /**
     * @ORM\Column(type="datetime_immutable")
     *
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     *
     * @var \DateTimeImmutable|null
     */
    private $updatedAt;

    public function __construct()
    {
        $this->redeemed = false;
        $this->voucherCode = (string) (new Ulid());
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRelatedOrderId(): ?string
    {
        return $this->relatedOrderId;
    }

    public function setRelatedOrderId(?string $relatedOrderId): void
    {
        $this->relatedOrderId = $relatedOrderId;
    }

    public function getVoucherCode(): string
    {
        return $this->voucherCode;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): void
    {
        $this->value = $value;
    }

    public function getPrintableValue(): string
    {
        if (self::TYPE_RELATIVE === $this->type) {
            return sprintf('%s %%', $this->value * 100);
        }

        return sprintf('%s €', $this->value);
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        if (!in_array($type, [
            self::TYPE_RELATIVE,
            self::TYPE_ABSOLUTE,
        ])) {
            throw new \InvalidArgumentException(sprintf('Unknown voucher type %s encountered.', $type));
        }

        $this->type = $type;
    }

    public function getCreationStrategy(): ?string
    {
        return $this->creationStrategy;
    }

    public function setCreationStrategy(?string $creationStrategy): void
    {
        $this->creationStrategy = $creationStrategy;
    }

    public function isRedeemed(): bool
    {
        return $this->redeemed;
    }

    public function setRedeemed(bool $redeemed): void
    {
        $this->redeemed = $redeemed;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function updateUpdatedAt()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
