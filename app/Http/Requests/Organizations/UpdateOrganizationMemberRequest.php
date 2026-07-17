<?php

namespace App\Http\Requests\Organizations;

use App\Core\Organizations\Enums\OrganizationMemberRole;
use App\Core\Organizations\Enums\OrganizationMemberStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateOrganizationMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', Rule::enum(OrganizationMemberRole::class)],
            'status' => ['required', Rule::enum(OrganizationMemberStatus::class)],
        ];
    }
}
