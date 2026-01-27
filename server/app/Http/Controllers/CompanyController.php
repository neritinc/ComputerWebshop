<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CompanyController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $this->authorize('viewAny', Company::class);

            $rows = Company::all();

            return response()->json([
                'message' => 'OK',
                'data' => $rows
            ], 200, options: JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error ' . $e->getCode(),
                'data' => null
            ], 500, options: JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        try {
            $this->authorize('create', Company::class);

            $company = Company::create($request->validated());

            return response()->json([
                'message' => 'Created successfully',
                'data' => $company
            ], 201, options: JSON_UNESCAPED_UNICODE);

        } catch (QueryException $e) {
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'message' => 'Insert error: company already exists',
                    'data' => [
                        'name' => $request->input('name')
                    ]
                ], 409, options: JSON_UNESCAPED_UNICODE);
            }

            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'message' => "Not found id: $id",
                'data' => null
            ], 404, options: JSON_UNESCAPED_UNICODE);
        }

        $this->authorize('view', $company);

        return response()->json([
            'message' => 'OK',
            'data' => $company
        ], 200, options: JSON_UNESCAPED_UNICODE);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, int $id)
    {
        try {
            $company = Company::find($id);

            if (!$company) {
                return response()->json([
                    'message' => "Patch error. Not found id: $id",
                    'data' => null
                ], 404, options: JSON_UNESCAPED_UNICODE);
            }

            $this->authorize('update', $company);

            $company->update($request->validated());

            return response()->json([
                'message' => 'OK',
                'data' => $company
            ], 200, options: JSON_UNESCAPED_UNICODE);

        } catch (QueryException $e) {
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'message' => 'Update error: company name already exists',
                    'data' => [
                        'name' => $request->input('name')
                    ]
                ], 409, options: JSON_UNESCAPED_UNICODE);
            }

            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $company = Company::find($id);

            if (!$company) {
                return response()->json([
                    'message' => 'The company could not be found.',
                    'data' => null
                ], 404, options: JSON_UNESCAPED_UNICODE);
            }

            $this->authorize('delete', $company);

            $company->delete();

            return response()->json([
                'message' => 'The deletion was successful.',
                'data' => null
            ], 200, options: JSON_UNESCAPED_UNICODE);

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'The deletion failed.',
                'data' => null
            ], 409, options: JSON_UNESCAPED_UNICODE);
        }
    }
}
