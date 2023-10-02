<?php
namespace App;

/**
 * @Entity @Table(name="products")
 **/
class Product
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;
    /** @Column(type="string") **/
    protected $name;
    /** @Column(type="string") **/
    protected $address;
    /** @Column(name="is_encrypted", type="boolean", options={"default":false}) **/
    private $isEncrypted = false;

    public function __construct(
        string $name, 
        string $address, 
        bool $isEncrypted = false
    ) {
        $this->name = $name;
        $this->address = $address;
        $this->isEncrypted = $isEncrypted;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function isEncrypted(): bool
    {
        return $this->isEncrypted;
    }

    public function setIsEncrypted(bool $isEncrypted): self
    {
        $this->isEncrypted = $isEncrypted;
        return $this;
    }
}
