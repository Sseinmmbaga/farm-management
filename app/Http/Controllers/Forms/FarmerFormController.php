<?php

namespace App\Http\Controllers\Forms;

use App\Http\Controllers\Controller;
use App\Models\FarmerForm;
use App\Models\Farmers\Farmer;
use App\Models\Farms\Farm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FarmerFormController extends Controller
{
    /**
     * Display a listing of all forms for the current user's role.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $roleValue = $user->getRoleValue();

        // Get forms accessible by this role
        $accessibleForms = FarmerForm::getFormsByRole($roleValue);
        $formTypes = array_keys($accessibleForms);

        $forms = FarmerForm::with(['farmer', 'farm', 'submittedBy'])
            ->whereIn('form_type', $formTypes)
            ->when($request->form_type, fn($q, $type) => $q->where('form_type', $type))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->farmer_id, fn($q, $id) => $q->where('farmer_id', $id))
            ->when($request->search, function($q, $search) {
                $q->whereHas('farmer', function($fq) use ($search) {
                    $fq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere('registration_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20);

        return view('forms.index', compact('forms', 'accessibleForms'));
    }

    /**
     * Show farmer selection page (first step before selecting form type).
     */
    public function selectFarmer(Request $request)
    {
        $farmers = Farmer::with('village')
            ->where('status', 'active')
            ->when($request->search, function($q, $search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%");
            })
            ->orderBy('first_name')
            ->paginate(20);

        return view('forms.select-farmer', compact('farmers'));
    }

    /**
     * Show the form selection page (after farmer is selected).
     */
    public function select(Farmer $farmer)
    {
        $user = Auth::user();
        $roleValue = $user->getRoleValue();
        $accessibleForms = FarmerForm::getFormsByRole($roleValue);

        return view('forms.select', compact('accessibleForms', 'farmer'));
    }

    /**
     * Show the form for creating a specific form type.
     */
    public function create(Farmer $farmer, string $formType)
    {
        $user = Auth::user();
        $roleValue = $user->getRoleValue();
        $accessibleForms = FarmerForm::getFormsByRole($roleValue);

        if (!isset($accessibleForms[$formType])) {
            abort(403, 'You do not have permission to access this form.');
        }

        $formInfo = $accessibleForms[$formType];
        $farms = Farm::where('farmer_id', $farmer->id)->orderBy('name')->get();

        // Pass farmers collection for form compatibility (with the selected farmer)
        $farmers = Farmer::where('status', 'active')->orderBy('first_name')->get();

        return view("forms.types.{$formType}", compact('formInfo', 'formType', 'farmer', 'farms', 'farmers'));
    }

    /**
     * Store a newly created form in storage.
     */
    public function store(Request $request, string $formType)
    {
        $user = Auth::user();
        $roleValue = $user->getRoleValue();
        $accessibleForms = FarmerForm::getFormsByRole($roleValue);

        if (!isset($accessibleForms[$formType])) {
            abort(403, 'You do not have permission to submit this form.');
        }

        $formInfo = $accessibleForms[$formType];

        $validated = $request->validate([
            'farmer_id' => 'nullable|exists:farmers,id',
            'farm_id' => 'nullable|exists:farms,id',
            'season' => 'nullable|string|max:50',
            'form_date' => 'nullable|date',
            'form_data' => 'required|array',
            'status' => 'in:draft,submitted',
        ]);

        $form = FarmerForm::create([
            'form_type' => $formType,
            'form_name' => $formInfo['name'],
            'form_name_sw' => $formInfo['name_sw'],
            'farmer_id' => $validated['farmer_id'] ?? null,
            'farm_id' => $validated['farm_id'] ?? null,
            'submitted_by' => $user->id,
            'form_data' => $validated['form_data'],
            'status' => $validated['status'] ?? 'submitted',
            'season' => $validated['season'] ?? null,
            'form_date' => $validated['form_date'] ?? now(),
        ]);

        return redirect()
            ->route('farmer-forms.show', $form)
            ->with('success', 'Form submitted successfully.');
    }

    /**
     * Display the specified form.
     */
    public function show(FarmerForm $farmerForm)
    {
        $user = Auth::user();
        $roleValue = $user->getRoleValue();
        $accessibleForms = FarmerForm::getFormsByRole($roleValue);

        // Check if user has access to this form type
        if (!isset($accessibleForms[$farmerForm->form_type]) && !$user->isAdmin() && !$user->isSupervisor()) {
            abort(403, 'You do not have permission to view this form.');
        }

        $farmerForm->load(['farmer', 'farm', 'submittedBy', 'reviewedBy']);

        return view('forms.show', compact('farmerForm'));
    }

    /**
     * Show the form for editing the specified form.
     */
    public function edit(FarmerForm $farmerForm)
    {
        $user = Auth::user();

        // Check if the form can be edited
        if (!$farmerForm->canBeEdited()) {
            return redirect()
                ->route('farmer-forms.show', $farmerForm)
                ->with('error', 'This form cannot be edited in its current status.');
        }

        // Check if user is the submitter or has admin/supervisor access
        if ($farmerForm->submitted_by !== $user->id && !$user->isAdmin() && !$user->isSupervisor()) {
            abort(403, 'You do not have permission to edit this form.');
        }

        $formInfo = FarmerForm::getFormTypes()[$farmerForm->form_type] ?? null;
        $farmers = Farmer::where('status', 'active')->orderBy('first_name')->get();
        $farms = Farm::with('farmer')->orderBy('name')->get();

        return view("forms.types.{$farmerForm->form_type}", compact('farmerForm', 'formInfo', 'farmers', 'farms'));
    }

    /**
     * Update the specified form in storage.
     */
    public function update(Request $request, FarmerForm $farmerForm)
    {
        $user = Auth::user();

        // Check if the form can be edited
        if (!$farmerForm->canBeEdited()) {
            return redirect()
                ->route('farmer-forms.show', $farmerForm)
                ->with('error', 'This form cannot be edited in its current status.');
        }

        // Check if user is the submitter or has admin/supervisor access
        if ($farmerForm->submitted_by !== $user->id && !$user->isAdmin() && !$user->isSupervisor()) {
            abort(403, 'You do not have permission to edit this form.');
        }

        $validated = $request->validate([
            'farmer_id' => 'nullable|exists:farmers,id',
            'farm_id' => 'nullable|exists:farms,id',
            'season' => 'nullable|string|max:50',
            'form_date' => 'nullable|date',
            'form_data' => 'required|array',
            'status' => 'in:draft,submitted',
        ]);

        $farmerForm->update([
            'farmer_id' => $validated['farmer_id'] ?? null,
            'farm_id' => $validated['farm_id'] ?? null,
            'form_data' => $validated['form_data'],
            'status' => $validated['status'] ?? 'submitted',
            'season' => $validated['season'] ?? null,
            'form_date' => $validated['form_date'] ?? now(),
        ]);

        return redirect()
            ->route('farmer-forms.show', $farmerForm)
            ->with('success', 'Form updated successfully.');
    }

    /**
     * Remove the specified form from storage.
     */
    public function destroy(FarmerForm $farmerForm)
    {
        $user = Auth::user();

        // Only allow deletion if status is draft or user is admin
        if ($farmerForm->status !== 'draft' && !$user->isAdmin()) {
            return redirect()
                ->route('farmer-forms.show', $farmerForm)
                ->with('error', 'Only draft forms can be deleted.');
        }

        // Check if user is the submitter or has admin access
        if ($farmerForm->submitted_by !== $user->id && !$user->isAdmin()) {
            abort(403, 'You do not have permission to delete this form.');
        }

        $farmerForm->delete();

        return redirect()
            ->route('farmer-forms.index')
            ->with('success', 'Form deleted successfully.');
    }

    /**
     * Review a submitted form (for supervisors/admins).
     */
    public function review(Request $request, FarmerForm $farmerForm)
    {
        $user = Auth::user();

        // Only supervisors and admins can review
        if (!$user->isAdmin() && !$user->isSupervisor()) {
            abort(403, 'You do not have permission to review forms.');
        }

        if (!$farmerForm->canBeReviewed()) {
            return redirect()
                ->route('farmer-forms.show', $farmerForm)
                ->with('error', 'This form cannot be reviewed in its current status.');
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'reviewer_notes' => 'nullable|string|max:1000',
        ]);

        $farmerForm->update([
            'status' => $validated['action'] === 'approve' ? 'approved' : 'rejected',
            'reviewed_by' => $user->id,
            'reviewer_notes' => $validated['reviewer_notes'],
        ]);

        $message = $validated['action'] === 'approve' ? 'Form approved successfully.' : 'Form rejected.';

        return redirect()
            ->route('farmer-forms.show', $farmerForm)
            ->with('success', $message);
    }

    /**
     * Print a form.
     */
    public function print(FarmerForm $farmerForm)
    {
        $user = Auth::user();
        $roleValue = $user->getRoleValue();
        $accessibleForms = FarmerForm::getFormsByRole($roleValue);

        if (!isset($accessibleForms[$farmerForm->form_type]) && !$user->isAdmin() && !$user->isSupervisor()) {
            abort(403, 'You do not have permission to print this form.');
        }

        $farmerForm->load(['farmer', 'farm', 'submittedBy', 'reviewedBy']);

        return view("forms.print.{$farmerForm->form_type}", compact('farmerForm'));
    }
}
