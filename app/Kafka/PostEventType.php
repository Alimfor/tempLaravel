<?php

namespace App\Kafka;

enum PostEventType
{

    case CREATED;
    case UPDATED;
    case DELETED;
}
