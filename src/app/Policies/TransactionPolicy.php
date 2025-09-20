<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Transaction;

class TransactionPolicy
{
    public function view(User $user, Transaction $tx): bool
    {
        return $tx->seller_id === $user->id || $tx->buyer_id === $user->id;
    }
    public function message(User $user, Transaction $tx): bool
    {
        return $this->view($user, $tx);
    }
}
