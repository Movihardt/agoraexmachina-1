<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="username.alreadyexists")
 * @UniqueEntity(fields={"email"}, message="email.alreadyexists")
 * @Vich\Uploadable
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=40, unique=true)
     */
    private $username;

    public function __toString()
    {
        return $this->username;
    }


    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    private $plainPassword;

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Proposal", mappedBy="user")
     */
    private $proposals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="user", orphanRemoval=true)
     */
    private $votes;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email(
     *    message = "email.notvalid"
     * )
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Forum", mappedBy="user", orphanRemoval=true)
     */
    private $forums;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Delegation", mappedBy="userFrom", orphanRemoval=true)
     */
    private $delegationsFrom;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Delegation", mappedBy="userTo", orphanRemoval=true)
     */
    private $delegationsTo;

    /**
     * @ORM\OneToMany(targetEntity=Workshop::class, mappedBy="user",orphanRemoval=true)
     */
    private $workshops;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="users",cascade={"persist"})
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="user", orphanRemoval=true)
     */
    private $notifications;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAllowedEmails;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $image;
    /**
     * @Vich\UploadableField(mapping="users_images", fileNameProperty="image")
     * @var File
     */
    private $imageFile;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $lastName;

    /**
     * @ORM\OneToMany(targetEntity=Request::class, mappedBy="user", orphanRemoval=true)
     */
    private $requests;

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }


    public function __construct()
    {
        $this->proposals = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->forums = new ArrayCollection();
        $this->delegationsFrom = new ArrayCollection();
        $this->delegationsTo = new ArrayCollection();
        $this->workshops = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->requests = new ArrayCollection();
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
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Proposals[]
     */
    public function getProposals(): Collection
    {
        return $this->proposals;
    }

    public function addProposal(Proposal $proposal): self
    {
        if (!$this->proposals->contains($proposal)) {
            $this->proposals[] = $proposal;
            $proposal->setUser($this);
        }

        return $this;
    }

    public function removeProposal(Proposal $proposal): self
    {
        if ($this->proposals->contains($proposal)) {
            $this->proposals->removeElement($proposal);
            // set the owning side to null (unless already changed)
            if ($proposal->getUser() === $this) {
                $proposal->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Votes[]
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setUser($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            $this->votes->removeElement($vote);
            // set the owning side to null (unless already changed)
            if ($vote->getUser() === $this) {
                $vote->setUser(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Forums[]
     */
    public function getForums(): Collection
    {
        return $this->forums;
    }

    public function addForum(Forum $forum): self
    {
        if (!$this->forums->contains($forum)) {
            $this->forums[] = $forum;
            $forum->setUser($this);
        }

        return $this;
    }

    public function removeForum(Forum $forum): self
    {
        if ($this->forums->contains($forum)) {
            $this->forums->removeElement($forum);
            // set the owning side to null (unless already changed)
            if ($forum->getUser() === $this) {
                $forum->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DelegationFrom[]
     */
    public function getDelegationsFrom(): Collection
    {
        return $this->delegationsFrom;
    }

    /**
     * @return Collection|DelegationTo[]
     */
    public function getDelegationsTo(): Collection
    {
        return $this->delegationsTo;
    }

    public function addDelegationFrom(Delegation $delegationFrom): self
    {
        if (!$this->delegationsFrom->contains($delegationFrom)) {
            $this->delegationsFrom[] = $delegationFrom;
            $delegationFrom->setUserFrom($this);
        }

        return $this;
    }

    public function addDelegationTo(Delegation $delegationTo): self
    {
        if (!$this->delegationsTo->contains($delegationTo)) {
            $this->delegationsTo[] = $delegationTo;
            $delegationTo->setUserFrom($this);
        }

        return $this;
    }

    public function removeDelegationFrom(Delegation $delegationFrom): self
    {
        if ($this->delegationsFrom->contains($delegationFrom)) {
            $this->delegationsFrom->removeElement($delegationFrom);
            // set the owning side to null (unless already changed)
            if ($delegationFrom->getUserFrom() === $this) {
                $delegationFrom->setUserFrom(null);
            }
        }

        return $this;
    }


    public function removeDelegationTo(Delegation $delegationTo): self
    {
        if ($this->delegationsTo->contains($delegationTo)) {
            $this->delegationsTo->removeElement($delegationTo);
            // set the owning side to null (unless already changed)
            if ($delegationTo->getUserFrom() === $this) {
                $delegationTo->setUserFrom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Workshop[]
     */
    public function getWorkshops(): Collection
    {
        return $this->workshops;
    }

    public function addWorkshop(Workshop $workshop): self
    {
        if (!$this->workshops->contains($workshop)) {
            $this->workshops[] = $workshop;
            $workshop->setUser($this);
        }

        return $this;
    }

    public function removeWorkshop(Workshop $workshop): self
    {
        if ($this->workshops->contains($workshop)) {
            $this->workshops->removeElement($workshop);
            // set the owning side to null (unless already changed)
            if ($workshop->getUser() === $this) {
                $workshop->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    public function getIsAllowedEmails(): ?bool
    {
        return $this->isAllowedEmails;
    }

    public function setIsAllowedEmails(bool $isAllowedEmails): self
    {
        $this->isAllowedEmails = $isAllowedEmails;

        return $this;
    }

    public function prepareNotification($subject): Notification
    {
        $notification = new Notification();
        $notification
            ->setDate(new \DateTime('now'))
            ->setIsRead(false)
            ->setSubject($subject)
            ->setUser($this);
        return $notification;
    }

    public function numberUnreadNotifications(): int
    {
        $count = 0;
        $notifications = $this->getNotifications();
        foreach ($notifications as $notification) {
            if (!$notification->getIsRead()) {
                $count++;
            }
        }
        return $count;
    }
    /*
        public function __serialize(): array
        {
            return [
                'id' => $this->id,
                'username' => $this->username,
                'roles' => $this->roles,
                'password' => $this->password,
                'proposals' => $this->proposals,
                'votes' => $this->votes,
                'email' => $this->email,
                'forums' => $this->forums,
                'delegationsFrom' => $this->delegationsFrom,
                'delegationsTo' => $this->delegationsTo,
                'workshops' => $this->workshops,
                'categories' => $this->categories,
                'notifications' => $this->notifications,
                'isAllowedEmails' => $this->isAllowedEmails,
                'image' => $this->image,
                'imageFile' => $this->imageFile,
                'updatedAt' => $this->updatedAt,
            ];
        }

        public function __unserialize(array $data): User
        {
            $this->id = $data['id'];
            $this->username = $data['username'];
            $this->roles = $data['roles'];
            $this->password = $data['password'];
            $this->proposals = $data['proposals'];
            $this->votes = $data['votes'];
            $this->email = $data['email'];
            $this->forums = $data['forums'];
            $this->delegationsFrom= $data['delegationsFrom'];
            $this->delegationsTo = $data['delegationsTo'];
            $this->workshops = $data['workshops'];
            $this->categories = $data['categories'];
            $this->notifications = $data['notifications'];
            $this->isAllowedEmails = $data['isAllowedEmails'];
            $this->image = $data['image'];
            $this->imageFile = $data['imageFile'];
            $this->updatedAt = $data['updatedAt'];

            return $this;
        }*/

    public function serialize()
    {
        return serialize($this->id);
    }

    public function unserialize($serialized)
    {
        $this->id = unserialize($serialized);

    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection|Request[]
     */
    public function getRequests(): Collection
    {
        return $this->requests;
    }

    public function addRequest(Request $request): self
    {
        if (!$this->requests->contains($request)) {
            $this->requests[] = $request;
            $request->setUser($this);
        }

        return $this;
    }

    public function removeRequest(Request $request): self
    {
        if ($this->requests->removeElement($request)) {
            // set the owning side to null (unless already changed)
            if ($request->getUser() === $this) {
                $request->setUser(null);
            }
        }

        return $this;
    }


}
