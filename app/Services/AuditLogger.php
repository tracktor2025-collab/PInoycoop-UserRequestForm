<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogger
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public static function log(
        Request $request,
        string $action,
        string $description,
        ?string $subjectType = null,
        ?int $subjectId = null,
        array $metadata = [],
        ?string $actorLabelOverride = null,
    ): void {
        if ($actorLabelOverride !== null && $actorLabelOverride !== '') {
            $adminLabel = $actorLabelOverride;
            $adminId = null;
        } else {
            $id = (int) $request->session()->get('admin_id');
            $admin = $id > 0 ? Admin::query()->find($id) : null;
            $adminLabel = $admin !== null
                ? trim((string) $admin->name).' <'.$admin->email.'>'
                : 'System';
            $adminId = $admin?->id;
        }

        AuditLog::query()->create([
            'admin_id' => $adminId,
            'admin_label' => $adminLabel,
            'action' => $action,
            'description' => $description,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'metadata' => $metadata !== [] ? $metadata : null,
        ]);
    }
}
