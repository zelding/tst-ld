<?php

namespace App\Model;

enum InviteStatus : int
{
    case INIT = 0;
    case SENT = 1;
    case ACCEPTED = 2;
    case REJECTED = 4;
    case DELETED = 8;
}
