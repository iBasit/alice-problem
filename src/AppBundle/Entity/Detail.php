<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Class Detail
 * @package AppBundle\Entity
 *
 * @ORM\Table(name="detail")
 * @ORM\Entity
 */
class Detail
{
    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", mappedBy="detail")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(name="address", type="string", length=150, nullable=true)
     */
    protected $address;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }
}