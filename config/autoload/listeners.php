<?php

declare(strict_types=1);
return [
    \App\Listener\CreditAccountListener::class,
    \App\Listener\DebitAccountListener::class,
    \App\Listener\NotifyPayeeListener::class,
    \App\Listener\TransactionFailedListener::class,
];
