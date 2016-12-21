<?php

namespace Inodata\FloraBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Employee.
 *
 * @ORM\Table(name="ino_employee")
 * @ORM\Entity
 */
class Employee
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=45, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=45, nullable=true)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=15, nullable=true)
     */
    private $phone;

    /**
     * @var string
     * @ORM\Column(name="job_position", type="string", columnDefinition="ENUM('Messenger','Collector')")
     */
    private $jobPosition;

    /**
     * @var int
     *
     * @ORM\Column(name="lamps", type="integer", nullable=true)
     */
    private $lamps;

    /**
     * @var int
     *
     * @ORM\Column(name="boxes", type="integer", nullable=true)
     */
    private $boxes;

    /**
     * @var array
     *            Desc Si el Employee es de tipo Messenger guarda los pedidos
     *            que se le han asignado
     */
    private $orders;

    public function __construct()
    {
        $this->boxes = 0;
        $this->lamps = 0;
    }

    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    public function getOrders()
    {
        return $this->orders;
    }

    /**
     *@return string
     */
    public function __toString()
    {
        if (!$this->id) {
            return 'New';
        }

        return $this->name.' '.$this->lastname.' ('.$this->code.')';
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return InoEmployee
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set lastname.
     *
     * @param string $lastname
     *
     * @return InoEmployee
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname.
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set phone.
     *
     * @param string $phone
     *
     * @return InoEmployee
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set code.
     *
     * @param string $code
     *
     * @return Employee
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set jobPosition.
     *
     * @param string $jobPosition
     *
     * @return Employee
     */
    public function setJobPosition($jobPosition)
    {
        $this->jobPosition = $jobPosition;

        return $this;
    }

    /**
     * Get jobPosition.
     *
     * @return string
     */
    public function getJobPosition()
    {
        if ($this->jobPosition == 'Messenger') {
            return 'Repartidor';
        } elseif ($this->jobPosition == 'Collector') {
            return 'Cobrador';
        } else {
            return $this->jobPosition;
        }
    }

    /**
     * Set lamps.
     *
     * @param int $lamps
     *
     * @return Employee
     */
    public function setLamps($lamps)
    {
        $this->lamps = $lamps;

        return $this;
    }

    /**
     * Get lamps.
     *
     * @return int
     */
    public function getLamps()
    {
        return $this->lamps;
    }

    /**
     * Set boxes.
     *
     * @param int $boxes
     *
     * @return Employee
     */
    public function setBoxes($boxes)
    {
        $this->boxes = $boxes;

        return $this;
    }

    /**
     * Get boxes.
     *
     * @return int
     */
    public function getBoxes()
    {
        return $this->boxes;
    }
}
