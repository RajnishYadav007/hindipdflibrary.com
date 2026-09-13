<?php

namespace App\Enums;

enum AuthorRole: string
{
    case Author = 'author';
    case Translator = 'translator';
    case Editor = 'editor';
    case Illustrator = 'illustrator';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Author => 'Author',
            self::Translator => 'Translator',
            self::Editor => 'Editor',
            self::Illustrator => 'Illustrator',
        };
    }
}
