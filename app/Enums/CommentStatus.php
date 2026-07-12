<?php

namespace App\Enums;

enum CommentStatus: string
{
    case Published = 'published';
    case Review = 'review';
    case Spam = 'spam';
    case Rejected = 'rejected';
}
