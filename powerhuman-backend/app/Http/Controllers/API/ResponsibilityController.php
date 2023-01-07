<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Models\Responsibility;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResponsibilityRequest;

class ResponsibilityController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        // Filter responsibility berdasarkan user yang login (Hanya bisa ngambil data sesuai user yang login)
        $responsibilityQuery = Responsibility::query();

        // Datanya satuan based id -> Contoh URL: https://powerhuman-backend.test/api/responsibility?id=1
        if ($id) {
            // Find Responsibility Based ID
            $responsibility = $responsibilityQuery->find($id);

            if ($responsibility) {
                return ResponseFormatter::success($responsibility, 'Responsibility found');
            }
            return ResponseFormatter::error('Responsibility not found', 404);
        }

        // Datanya list responsibility -> Contoh URL: https://powerhuman-backend.test/api/responsibility
        $responsibilities = $responsibilityQuery->where('role_id', $request->role_id);

        // Datanya satuan based name -> Contoh URL: https://powerhuman-backend.test/api/responsibility?name=Developer
        if ($name) {
            $responsibilities->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $responsibilities->paginate($limit),
            'Responsibility data found'
        );
    }
    
    public function create(CreateResponsibilityRequest $request)
    {
        try {
            //Create Responsibility
            $responsibility = Responsibility::create([
                'name' => $request->name,
                'role_id' => $request->role_id,
            ]);

            // Check Responsibility is available / exist
            if (!$responsibility) {
                return ResponseFormatter::error('Responsibility not found', 404);
            }

            //Return response
            return ResponseFormatter::success($responsibility, 'Responsibility has been created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            //Get responsibility data
            $responsibility = Responsibility::find($id);

            // TODO: Check if responsibility is owned by user

            //Check if responsibility exists
            if (!$responsibility) {
                return ResponseFormatter::error('Responsibility not found', 404);
            }

            //Delete responsibility
            $responsibility->delete();

            //Return response
            return ResponseFormatter::success($responsibility, 'Responsibility deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
