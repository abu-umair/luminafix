<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use Faker\Factory;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

use Google\Cloud\Firestore\FieldValue;
use Google\Cloud\Firestore\FirestoreClient;

class ProjectsController extends Controller
{
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connectdatabase();
        $this->auth = \App\Services\FirebaseService::connectauth();
        $this->connect = \App\Services\FirebaseService::connect();

        
    }
    /**
     * Show data table for users management
     *
     * @return Illuminate\View\View
     */
    public function index(): View
    {

        $connect = $this->connect->createFirestore();
        $newDatabase = $connect->database()->collection('projects');
        // $check =  Invoice::first();

        $check = Project::first();
        

        if ($check) {
            $least_year = (int) (new Carbon(
                Project::orderBy('created_at')
                    ->first()
                    ->created_at
            )
            )->format('Y');
            
            $projects = Project::with(['leader', 'participants'])
                // ->filter(request(['project_id', 'month', 'year']))
                ->filter(request(['status','minProgress','maxProgress','minPaidInvoices','maxPaidInvoices']))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString();
            // foreach ($projects as $project) {
            //     # code...
            //     dd($project->paidInvoices);
            // }
        } else {
            $projects = null;
            $least_year = null;

        }

        

        return view('dashboard.projects', [
            'faker'       => Factory::create(),
            'users'       => User::all(),
            'projects'    => $projects,
            // 'projects'    => Project::with(['leader', 'participants'])->paginate(10),
            'newDatabase' => $newDatabase,

            // 'invoices'    => $invoices,
            'years'       => (int) date('Y') - $least_year,
        ]);
    }

    /**
     * Validates new project creation request and adds new project record in
     * database
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'min:4', 'max:30', 'unique:projects,name'],
            'budget'   => ['required', 'numeric', 'min:1', 'max:9999999999'],
            'status'   => ['required', 'string', Rule::in(array_keys(config('lumina.project.status')))],
            'phase'    => ['required', 'string', 'min:2', 'max:1000'],
            
            // Leader validation
            'leader' => ['numeric', Rule::exists('users', 'id')],
            
            // BAs validation
            'bas' => ['array'],
            'bas.*' => ['numeric', Rule::exists('users', 'id')],
            
            // DAs validation
            'das' => ['array'],
            'das.*' => ['numeric', Rule::exists('users', 'id')],
            
            // LDs validation
            'lds' => ['array'],
            'lds.*' => ['numeric', Rule::exists('users', 'id')],
        ]);

        $id_unique = uniqid('lumina', true); //create id unique and higher entropy

        // create projects firestore
        $connect = $this->connect->createFirestore();
        $newDatabase = $connect->database();
        $testRef = $newDatabase->collection('projects')->document($id_unique);
        $testRef->set([
            'name'                  => $validated['name'],
            'commissioning'         => 0,
            'concept'               => 0,
            'development'           => 0,
            'documentation'         => 0,
            'progress'              => 0,
            'workload'              => 0,
            'status'                => $validated['status'],
            'bas'                   => $validated['bas'] ?? null,
            'das'                   => $validated['das'] ?? null,
            'lds'                   => $validated['lds'] ?? null,
            // 'budget'                => $validated['budget'],
            // 'phase'                 => $validated['phase'],
        ]);

        // create db
        $project = Project::create([
            'id_project_firebase'   => $id_unique,
            'name'                  => $validated['name'],
            'budget'                => $validated['budget'],
            'status'                => $validated['status'],
            'phase'                 => $validated['phase'],
            'leader_id'             => $validated['leader']
        ]);

        // If BAs are passed then associate them with the project
        if ($request->has('bas'))
            $project
                ->bas()
                ->syncWithPivotValues($validated['bas'], ['role' => 'ba']);

        // If BAs are passed then associate them with the project
        if ($request->has('das'))
            $project
                ->das()
                ->syncWithPivotValues($validated['das'], ['role' => 'da']);

        // If BAs are passed then associate them with the project
        if ($request->has('lds'))
            $project
                ->lds()
                ->syncWithPivotValues($validated['lds'], ['role' => 'ld']);

        return redirect()
            ->route('dashboard.projects')
            ->with('created', 'The project was created successfully!');
    }

    /**
     * Validates project data and update the record ind database
     *
     * @param \Illuminate\Http\Request  $request
     * @param \App\Models\Project  $project
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        
        $validated = $request->validate([
            'name'     => ['required', 'string', 'min:4', 'max:30', 'unique:projects,name,'.$project->id],
            'budget'   => ['required', 'numeric', 'min:1', 'max:9999999999'],
            'status'   => ['required', 'string', Rule::in(array_keys(config('lumina.project.status')))],
            'phase'    => ['required', 'string', 'min:2', 'max:1000'],

            // Leader validation
            'leader' => ['numeric', Rule::exists('users', 'id')],

            // BAs validation
            'bas' => ['array'],
            'bas.*' => ['numeric', Rule::exists('users', 'id')],

            // DAs validation
            'das' => ['array'],
            'das.*' => ['numeric', Rule::exists('users', 'id')],

            // LDs validation
            'lds' => ['array'],
            'lds.*' => ['numeric', Rule::exists('users', 'id')],
        ]);
        

        $project->update([
            'name'      => $validated['name'],
            'budget'    => $validated['budget'],
            'status'    => $validated['status'],
            'phase'     => $validated['phase'],
            'leader_id' => $validated['leader']
        ]);

        // update firestre
        $dataFirestore = [
            'name'      => $validated['name'],
            'status'    => $validated['status'],
            'bas'       => $validated['bas'] ?? null,
            'das'       => $validated['das'] ?? null,
            'lds'       => $validated['lds'] ?? null,
            // 'phase'     => $validated['phase'],
        ];

        $connect = $this->connect->createFirestore();
        $newDatabase = $connect->database();
        $testRef = $newDatabase->collection('projects')->document($project->id_project_firebase);
        
        // delete field lds
        // if ($request->has('lds')) {
        //     $testRef->update([
        //         [
        //             'path' => 'lds',
        //             'value' => FieldValue::deleteField()
        //         ]
        //     ]); 
        // }

        // update firestore merge
        $testRef->set($dataFirestore, [
            'merge' => true
        ]);

        // If BAs are passed then associate them with the project
        if ($request->has('bas'))
            $project
                ->bas()
                ->syncWithPivotValues($validated['bas'], ['role' => 'ba']);
        else // Remove all BAs
            $project->bas()->wherePivot('role', 'ba')->detach();


        // If BAs are passed then associate them with the project
        if ($request->has('das'))
            $project
                ->das()
                ->syncWithPivotValues($validated['das'], ['role' => 'da']);
        else // Remove all DAs
            $project->das()->wherePivot('role', 'da')->detach();

        // If BAs are passed then associate them with the project
        if ($request->has('lds'))
            $project
                ->lds()
                ->syncWithPivotValues($validated['lds'], ['role' => 'ld']);
        else // Remove all LDs
            $project->lds()->wherePivot('role', 'ld')->detach();

        return redirect()
            ->route('dashboard.projects')
            ->with('updated', 'The project was updated successfully!');
    }

    /**
     * Deletes the specified project
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Project $project): RedirectResponse
    {
        try {
            // delete firestore user
            $connect = $this->connect->createFirestore();
            $newDatabase = $connect->database();
            $testRef = $newDatabase->collection('projects')->document($project->id_project_firebase)->delete();

            // detele db mysql
            $project->delete();
        } catch (Exception $th) {
            return redirect()
                ->back()
                ->with('message', $th->getMessage());
        }

        return redirect()
            ->route('dashboard.projects')
            ->with('deleted', 'The project was deleted successfully!');
    }
}
