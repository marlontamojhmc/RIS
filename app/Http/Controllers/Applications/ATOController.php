<?php

namespace App\Http\Controllers\Applications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Locator\ApplicationModel;
use Illuminate\Support\Facades\Auth;
use App\Models\ATO\AtoApplication;
use App\Models\Upload;
use App\Services\UploadService;
use App\Models\Locator\ApplicationForApproval;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Http\Requests\StoreAtoApplicationRequest;
use App\Models\ATO\AtoPricing;

class ATOController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $userId = Auth::id();
      $ato = ApplicationModel::where('user_id', $userId)
              ->where('form_title','ATO')
              ->first();
        return Inertia::render('ATO/Index',[
            'ato' => $ato,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('ATO/Create2',[]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAtoApplicationRequest $request, UploadService $uploadService)
{   
    $userId = auth()->id();
    $ato = AtoApplication::create([
        'application_id' => $request->application_id,
        'application_date' => now(),
        'application_type' => $request->applicationType,
        'business_structure' => $request->businessStructure,
        'trades_name' => $request->businessProfile['businessName'],
        'parent_company' => $request->businessProfile['parentCompany'],
        'taxpayer_name' => $request->businessProfile['taxpayerName'],
        'TIN' => $request->businessProfile['TIN'],
        'PrimaryLine' => $request->pcic['primaryLine'],
        'SecondaryLine' => $request->pcic['secondaryLine'],
        'nature_of_contract' => $request->natureOfContract,
        'pcic_primary_line' => $request->pcic['PCICPrimary'],
        'pcic_secondary_line' => $request->pcic['PCICSecondary'],
        'pcic_Primary_email' => $request->pcic['emailPrimary'],
        'pcic_Secondary_email' => $request->pcic['emailSecondary'],
        'pcic_location' => $request->pcic['location'],
        'pcic_office_address' => $request->pcic['officeAddress'],
        'pcic_contact_person' => $request->pcic['contactPerson'],
        'pcic_contact_number' => $request->pcic['contactNumber'],
        'user_id' => $userId,
        'price'=> $request->price,
        'ato_business_enterprise_classification_id' => $request->Enterprise,
        'ato_business_sector_classification_id' => $request->Sector,

    ]);

    ApplicationForApproval::create([
        'application_id' => $request->application_id,
        'approver_group_id' => $request->approver_group_id,
        'form_number' => $request->application_form_number,
        'status' => 'Pending',
    ]);

    $filesInput = $request->input('files');     // titles
    $filesUpload = $request->file('files');     // UploadedFile objects

$files = [];

foreach ($filesInput as $index => $item) {
    if (!isset($filesUpload[$index]['file'])) {
        continue;
    }

    $files[] = [
        'title' => $item['title'] ?? null,
        'file'  => $filesUpload[$index]['file'],
    ];
}

$folder = 'ATO';
  
    $uploadService->multipleUpload( $files, $request->application_id, $userId, $folder);

    return redirect()->route('ATO.show', $request->application_id);
}
    
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
            $ato = AtoApplication::with('uploads', 'applicationForm')->where('application_id', $id)->first();
    
    // Fix file paths
            if ($ato && $ato->uploads) {
                foreach ($ato->uploads as $upload) {
                    // Convert stored path to a real URL
                    $upload->file_url = Storage::url($upload->file_path);
                }
            }
       
                    return Inertia::render('ATO/Show', [
                                                'ATOapplication' => $ato,
                                            ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    
    public function MyAto()
    {   
        $user = auth()->user(); 

            $atoApplications = $user->applications()
                            ->where('form_title', 'ATO')
                            ->first();
      
       return redirect()->route('ATO.show', $atoApplications->id);
    }
}
