<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

final class ReceiptPartyProfile extends Model
{
    /** @var list<string> */
    protected $fillable = ['user_id', 'party_type', 'label', 'name', 'document_type', 'document'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['document' => 'encrypted'];
    }
}
