<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        // Filter role berdasarkan user yang login (Hanya bisa ngambil data sesuai user yang login)
        $roleQuery = Role::query();

        // Datanya satuan based id -> Contoh URL: https://powerhuman-backend.test/api/role?id=1
        if ($id) {
            // Find Role Based ID
            $role = $roleQuery->find($id);

            if ($role) {
                return ResponseFormatter::success($role, 'Role found');
            }
            return ResponseFormatter::error('Role not found', 404);
        }

        // Datanya list role -> Contoh URL: https://powerhuman-backend.test/api/role
        $roles = $roleQuery->where('company_id', $request->company_id);

        // Datanya satuan based name -> Contoh URL: https://powerhuman-backend.test/api/role?name=Developer
        if ($name) {
            $roles->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $roles->paginate($limit),
            'Role data found'
        );
    }
    
    public function create(CreateRoleRequest $request)
    {
        try {
            //Create Role
            $role = Role::create([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            // Check Role is available / exist
            if (!$role) {
                return ResponseFormatter::error('Role not found', 404);
            }

            //Return response
            return ResponseFormatter::success($role, 'Role has been created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            //Get role data
            $role = Role::find($id);

            //Check if role exists
            if (!$role) {
                return ResponseFormatter::error('Role not found', 404);
            }

            //Update role
            $role->update([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            //Return response
            return ResponseFormatter::success($role, 'Role updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            //Get role data
            $role = Role::find($id);

            // TODO: Check if role is owned by user

            //Check if role exists
            if (!$role) {
                return ResponseFormatter::error('Role not found', 404);
            }

            //Delete role
            $role->delete();

            //Return response
            return ResponseFormatter::success($role, 'Role deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
