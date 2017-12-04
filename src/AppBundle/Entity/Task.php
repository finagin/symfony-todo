<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Serializable;

/**
 * @ORM\Table(name="tasks")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 * @Gedmo\Tree(type="nested")
 */
class Task implements Serializable, \JsonSerializable
{
    use TimestampableEntity;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tasks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Task")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Task", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
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
     * Set title.
     *
     * @param string $title
     *
     * @return Task
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get lft.
     *
     * @return int
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Get lvl.
     *
     * @return int
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Get rgt.
     *
     * @return int
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Task
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get root.
     *
     * @return \AppBundle\Entity\Task
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set parent.
     *
     * @param \AppBundle\Entity\Task $parent
     *
     * @return Task
     */
    public function setParent(\AppBundle\Entity\Task $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent.
     *
     * @return \AppBundle\Entity\Task
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children.
     *
     * @param \AppBundle\Entity\Task $children
     *
     * @return Task
     */
    public function addChild(\AppBundle\Entity\Task $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children.
     *
     * @param \AppBundle\Entity\Task $children
     */
    public function removeChild(\AppBundle\Entity\Task $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            $this->id,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->id) = unserialize($serialized);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'parent_id' => is_null($this->getParent()) ? null : $this->getParent()->getId(),
            'user_id' => is_null($this->getUser()) ? null : $this->getUser()->getId(),
            'lft' => $this->getLft(),
            'rgt' => $this->getRgt(),
            'lvl' => $this->getLvl(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    public function __toString()
    {
        return (string) $this->getId();
    }
}
