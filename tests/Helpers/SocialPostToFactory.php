<?php

declare(strict_types=1);

namespace Tests\Helpers;

use DateTime;
use SocialPost\Dto\SocialPostTo;

final class SocialPostToFactory
{
    public function create(
        string $id,
        string $authorId,
        string $authorName,
        string $text,
        string $type,
        DateTime $date
    ): SocialPostTo {
        return (new SocialPostTo())
            ->setId($id)
            ->setAuthorId($authorId)
            ->setAuthorName($authorName)
            ->setText($text)
            ->setType($type)
            ->setDate($date);
    }
}
