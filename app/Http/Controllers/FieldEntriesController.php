<?php

namespace App\Http\Controllers;

use App\Models\FieldEntry;
use App\Forms\FormManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FieldEntriesController extends Controller
{
    protected FormManager $formManager;

    public function __construct(FormManager $formManager)
    {
        $this->formManager = $formManager;
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $entries = FieldEntry::with(['field', 'farm', 'farmer'])
            ->orderBy('entry_date', 'desc')
            ->paginate(20);

        return view('field-entries.index', compact('entries'));
    }

    public function create()
    {
        return view('field-entries.create');
    }

    public function store(Request $request)
    {
        // Basic implementation - will be expanded
        return redirect()->route('field-entries.index')
            ->with('success', 'Field entry created successfully.');
    }

    public function show(FieldEntry $entry)
    {
        return view('field-entries.show', compact('entry'));
    }
}
