<?php

namespace Inodata\FloraBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InoMsgCard
 *
 * @ORM\Table(name="ino_msg_card")
 * @ORM\Entity
 */
class InoMsgCard
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="from", type="string", length=100, nullable=true)
     */
    private $from;

    /**
     * @var string
     *
     * @ORM\Column(name="to", type="string", length=100, nullable=true)
     */
    private $to;

    /**
     * @var string
     *
     * @ORM\Column(name="msg", type="text", nullable=true)
     */
    private $msg;

    /**
     * @var \InoOrder
     *
     * @ORM\ManyToOne(targetEntity="InoOrder")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     * })
     */
    private $order;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set from
     *
     * @param string $from
     * @return InoMsgCard
     */
    public function setFrom($from)
    {
        $this->from = $from;
    
        return $this;
    }

    /**
     * Get from
     *
     * @return string 
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to
     *
     * @param string $to
     * @return InoMsgCard
     */
    public function setTo($to)
    {
        $this->to = $to;
    
        return $this;
    }

    /**
     * Get to
     *
     * @return string 
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set msg
     *
     * @param string $msg
     * @return InoMsgCard
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;
    
        return $this;
    }

    /**
     * Get msg
     *
     * @return string 
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * Set order
     *
     * @param \Inodata\FloraBundle\Entity\InoOrder $order
     * @return InoMsgCard
     */
    public function setOrder(\Inodata\FloraBundle\Entity\InoOrder $order = null)
    {
        $this->order = $order;
    
        return $this;
    }

    /**
     * Get order
     *
     * @return \Inodata\FloraBundle\Entity\InoOrder 
     */
    public function getOrder()
    {
        return $this->order;
    }
}