<?php

namespace Myerscode\Utilities\Web\Data;

enum ResponseFrom: string
{
    case CURL = 'curl';
    case HEADERS = 'headers';
    case HTTP = 'http';
}
