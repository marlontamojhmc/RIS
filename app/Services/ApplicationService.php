<?php

namespace App\Services;

use App\Services\Contracts\ISezrisService;
use App\Models\Signup\TemporaryUser;
class ApplicationService implements ISezrisService
{
//     public function fetchData(array $filters): array
//     {
//     return [
//         [
//             'id' => 1,
//             'title' => 'SEZRIS Entry A',
//             'status' => 'pending',
//             'created_at' => now()->subDays(3)->toDateTimeString(),
//         ],
//         [
//             'id' => 2,
//             'title' => 'SEZRIS Entry B',
//             'status' => 'approved',
//             'created_at' => now()->subDays(1)->toDateTimeString(),
//         ],
//         [
//             'id' => 3,
//             'title' => 'SEZRIS Entry C',
//             'status' => 'rejected',
//             'created_at' => now()->subDays(7)->toDateTimeString(),
//         ],
//     ];
// }


//     public function store(array $data): bool
//     {
//         // Implementation here
//         return true;
//     }

//     public function getById(int $id): array|null
//     {
//         // Implementation here
//         return null;
//     }
       public function getFormOptionsForUser()
    {
        $user = Auth::user();
         $vendor = TemporaryUser::with(['signupApprovers'])
                 ->where('temporary_user_id', $user->id)->get();
                 dd($vendor);
        $applications = $user->applications()->where('form_title', 'ATO')->first();

        $applicationId = $applications->id ?? null;

        $hasATO = $applicationId
            ? AtoApplication::where('application_id', $applicationId)
                ->where('user_id', $user->id)
                ->first()
            : null;

        if ($hasATO) {
            return Form::all()->except([8]); // remove ATO
        }

        if (!$hasATO || !$applicationId) {
            return Form::where('name', 'ATO')->get();
        }

        return Form::all();
    }
    public function createApplication($type)
    {
        $user = Auth::user();
        
        $application = ApplicationModel::create([
            'form_title' => $type,
            'user_id'    => $user->id,
        ]);

        $approverForm = Form::where('name', $type)->first();
        $sets = ApproverSets::where('approver_group_id', $approverForm->approver_group_id)->get();

        foreach ($sets as $set) {
            ApproverGroupApprover::create([
                'approver_group_id'  => $set->approver_group_id,
                'approver_id'        => $set->user_id,
                'sequence'           => $set->sequence,
                'role'               => $set->role,
                'application_form_id'=> $application->id,
                'status'             => AppConstants::STATUS_PENDING,
            ]);
        }

        return [
            'application'  => $application,
            'approverForm' => $approverForm
        ];
    }
    public function getApplicationDetails($id)
    {
        return ApplicationModel::with(['articleDetails', 'uploads', 'selections'])
            ->findOrFail($id);
    }

    public function getApproverDetails($id)
    {
        $forApproval = ApplicationForApproval::with('approverGroup.approvers')
            ->where('application_id', $id)
            ->first();

        if (!$forApproval) return null;

        // auto approve logic
        if ($forApproval->approverGroup->allApproversStatusApproved()) {
            $forApproval->application->update(['status' => AppConstants::STATUS_APPROVED]);
            $forApproval->update(['status' => AppConstants::STATUS_APPROVED]);
        }

        $approvers = ApproverGroupApprover::with(['approver'])
            ->where('approver_group_id', $forApproval->approverGroup->id)
            ->where('application_form_id', $id)
            ->get();

        return [
            'approverGroup' => $forApproval->approverGroup,
            'approvers'     => $approvers->map(fn($a) => [
                'id'    => $a->approver->id ?? null,
                'name'  => $a->approver->name ?? '(Unknown)',
                'email' => $a->approver->email ?? null,
                'pivot' => [
                    'sequence' => $a->sequence,
                    'role'     => $a->role,
                    'status'   => $a->status,
                    'acted_at' => $a->acted_at,
                    'remark'   => $a->remark,
                ]
            ])
        ];
    }
    public function saveOptionSelection($validated)
    {
        $option = ApplicationOption::find($validated['option_id']);
        $application = ApplicationModel::findOrFail($validated['application_id']);
        $form = Form::where('name', $application->form_title)->first();

        $expired = PermitHelper::computeValidity((int) $option->value);

        UserApplicationSelection::updateOrCreate(
            [
                'application_id' => $validated['application_id'],
                'user_id'        => Auth::id(),
            ],
            [
                'option_id'   => $validated['option_id'],
                'Expired_at'  => $expired,
                'selected_at' => now(),
                'amount'      => $option->price,
            ]
        );

        return [
            'user'             => Auth::user(),
            'expired_at'       => $expired,
            'price'            => $option->price,
            'application'      => $application,
            'options'          => ApplicationOption::select('id', 'name', 'value', 'validity')->get(),
            'approverGroupId'  => $form->approver_group_id,
        ];
    }
    public function getApplicationById(String $id)
    {
        $application = ApplicationModel::with(['articleDetails', 'uploads', 'selections'])
                        ->findOrFail($id);
            $approvers = ApplicationForApproval::with('approverGroup.approvers')
                            ->where('application_id', $id)
                            ->first();
             return [
                        'application' => $application,
                        'approversGroupApprovers' => $approvers,
              ];
    }

}
