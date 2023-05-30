<?php

namespace Myerscode\Utilities\Web\Data;

enum CheckWith: string
{
    case CURL = 'curl';
    case HEADERS = 'headers';

    case HTTP = 'http';
}
