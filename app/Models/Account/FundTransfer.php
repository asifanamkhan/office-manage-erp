<?php

namespace App\Models\Account;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundTransfer extends Model
{
    use HasFactory,SoftDeletes;
    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }
    // Relation With User Model
    public function updatedBy() {
        return $this->belongsTo(User::class, 'updated_by');
    }
    // Relation With cash-in Bank-Account Model
    public function cashInBankAccount() {
        return $this->belongsTo(BankAccount::class, 'cash_in_account')->withTrashed();
    }

    // Relation With cash-out Bank-Account Model
    public function cashOutBankAccount() {
        return $this->belongsTo(BankAccount::class, 'cash_out_account')->withTrashed();
    }
}
