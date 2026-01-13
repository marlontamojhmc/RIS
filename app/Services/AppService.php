<?php

namespace App\Services;

use App\Models\Locator\ApplicationModel;
use App\Models\Locator\ApplicationOption;
use App\Models\Locator\UserApplicationSelection;
use App\Models\Locator\Form;
use App\Models\Locator\ApproverGroupApprover;
use App\Models\Locator\ApplicationForApproval;
use App\Models\ATO\AtoApplication;
use App\Helpers\PermitHelper;
use App\Helpers\AppConstants;
use App\Models\ApproverSets;
use Illuminate\Support\Facades\Auth;

class AppService
{
    /** -------------------------
     * INDEX
     * ------------------------*/
    public function getLatestApplication()
    {
        $application = ApplicationModel::with(['selections.option', 'selections.user'])
            ->latest()
            ->first();

        if (!$application) {
            return null;
        }

        $selections = $application->selections->map(function ($selection) {
            return [
                'option_name' => $selection->option->name,
                'amount'      => $selection->amount,
                'validity'    => $selection->option->validity,
                'selected_at' => $selection->selected_at,
                'user_name'   => $selection->user->name,
            ];
        });

        return [
            'id'           => $application->id,
            'form_title'   => $application->form_title,
            'control_no'   => $application->control_number,
            'form_number'  => $application->form_number,
            'selections'   => $selections,
        ];
    }


    /** -------------------------
     * CREATE
     * ------------------------*/
    public function getFormOptionsForUser()
    {
        $user = Auth::user();

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


    /** -------------------------
     * STORE
     * ------------------------*/
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


    /** -------------------------
     * SHOW
     * ------------------------*/
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


    /** -------------------------
     * SAVE OPTION SELECTION
     * ------------------------*/
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
    public function getApplicationData(string $id)
    {
        // Fetch the application with relations
        $application = ApplicationModel::with([
            'articleDetails',
            'uploads',
            'selections'
        ])->findOrFail($id);

        // Fetch approvers
        $approversForApproval = ApplicationForApproval::with('approverGroup.approvers')
            ->where('application_id', $id)
            ->first();

        // Approver group users
        $approverList = ApproverGroupApprover::with(['approver', 'approverGroup'])
            ->where('approver_group_id', $approversForApproval->approverGroup->id)
            ->where('application_form_id', $id)
            ->get(['id', 'approver_id', 'sequence', 'role', 'remark', 'status', 'acted_at', 'approver_group_id']);

        // Auto update status if all approved
        if ($approversForApproval->approverGroup->allApproversStatusApproved()) {
            $application->status = AppConstants::STATUS_APPROVED;
            $application->save();

            $approversForApproval->status = AppConstants::STATUS_APPROVED;
            $approversForApproval->save();
        }

        // Return packed data (controller-ready)
        return [
            'application'     => $application,
            'approverGroup'   => $approversForApproval?->approverGroup,
            'approvers'       => $approverList->map(function ($item) {
                return [
                    'id' => $item->approver->id ?? null,
                    'name' => $item->approver->name ?? '(Unknown)',
                    'email' => $item->approver->email ?? null,
                    'pivot' => [
                        'role' => $item->role,
                        'sequence' => $item->sequence,
                        'status' => $item->status,
                        'acted_at' => $item->acted_at,
                        'remark' => $item->remark,
                    ],
                ];
            }),
        ];
    }
}
