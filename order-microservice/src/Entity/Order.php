<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

/**
 * @ORM\Entity()
 * @ORM\Table(name="orders")
 * @ORM\HasLifecycleCallbacks()
 */
class Order
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    private $ulid;

    /**
     * @ORM\Column(type="float", precision=2)
     *
     * @var float
     */
    private $total;

    /**
     * @ORM\Column(type="float", precision=2)
     *
     * @var float
     */
    private $vatRate;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $deliverySent;

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
        $this->deliverySent = false;
        $this->ulid = (string) (new Ulid());
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \Symfony\Component\Uid\AbstractUid|Ulid
     */
    public function getUlid()
    {
        return Ulid::fromString($this->ulid);
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    public function getVatRate(): float
    {
        return $this->vatRate;
    }

    public function setVatRate(float $vatRate): void
    {
        $this->vatRate = $vatRate;
    }

    public function isDeliverySent(): bool
    {
        return $this->deliverySent;
    }

    public function setDeliverySent(bool $deliverySent): void
    {
        $this->deliverySent = $deliverySent;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
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
