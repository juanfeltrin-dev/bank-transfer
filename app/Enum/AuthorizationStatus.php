<?php

declare(strict_types=1);

namespace App\Enum;

enum AuthorizationStatus: string
{
    case Authorized = 'Autorizado';
    case NotAuthorized = 'Não Autorizado';
    case Error = 'Aconteceu algo inesperado';
}
