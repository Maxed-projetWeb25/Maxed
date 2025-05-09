<?php
class Post {
    private ?int $postid;
    private int $userid;
    private string $description;
    private string $media;
    private string $posttype;
    private string $visibility;
    private string $date_posted;

    public function __construct(
        int $userid,
        string $description,
        string $media,
        string $posttype,
        string $visibility,
        string $date_posted = '',
        ?int $postid = null
    ) {
        $this->postid = $postid;
        $this->userid = $userid;
        $this->description = $description;
        $this->media = $media;
        $this->posttype = $posttype;
        $this->visibility = $visibility;
        $this->date_posted = $date_posted ?: date('Y-m-d H:i:s');
    }

    // Getters
    public function getPostid(): ?int { return $this->postid; }
    public function getUserid(): int { return $this->userid; }
    public function getDescription(): string { return $this->description; }
    public function getMedia(): string { return $this->media; }
    public function getPosttype(): string { return $this->posttype; }
    public function getVisibility(): string { return $this->visibility; }
    public function getDatePosted(): string { return $this->date_posted; }

    // Setters
    public function setUserid(int $userid): self {
        $this->userid = $userid;
        return $this;
    }

    public function setDescription(string $description): self {
        $this->description = $description;
        return $this;
    }

    public function setMedia(string $media): self {
        $this->media = $media;
        return $this;
    }

    public function setPosttype(string $posttype): self {
        $this->posttype = $posttype;
        return $this;
    }

    public function setVisibility(string $visibility): self {
        $this->visibility = $visibility;
        return $this;
    }

    public function setDateposted(string $dateposted): self {
        $this->dateposted = $dateposted;
        return $this;
    }
}
?>
