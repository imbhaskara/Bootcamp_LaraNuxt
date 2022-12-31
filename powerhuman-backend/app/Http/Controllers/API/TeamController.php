<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;

class TeamController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        // Filter team berdasarkan user yang login (Hanya bisa ngambil data sesuai user yang login)
        $teamQuery = Team::query();

        // Datanya satuan based id -> Contoh URL: https://powerhuman-backend.test/api/team?id=1
        if ($id) {
            // Find Team Based ID
            $team = $teamQuery->find($id);

            if ($team) {
                return ResponseFormatter::success($team, 'Team found');
            }
            return ResponseFormatter::error('Team not found', 404);
        }

        // Datanya list team -> Contoh URL: https://powerhuman-backend.test/api/team
        $teams = $teamQuery->where('company_id', $request->company_id);

        // Datanya satuan based name -> Contoh URL: https://powerhuman-backend.test/api/team?name=Developer
        if ($name) {
            $teams->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $teams->paginate($limit),
            'Team data found'
        );
    }
    
    public function create(CreateTeamRequest $request)
    {
        try {
            //Check if team has icon
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            //Create Team
            $team = Team::create([
                'name' => $request->name,
                'icon' => $path,
                'company_id' => $request->company_id,
            ]);

            // Check Team is available / exist
            if (!$team) {
                return ResponseFormatter::error('Team not found', 404);
            }

            //Return response
            return ResponseFormatter::success($team, 'Team has been created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateTeamRequest $request, $id)
    {
        try {
            //Get team data
            $team = Team::find($id);

            //Check if team exists
            if (!$team) {
                return ResponseFormatter::error('Team not found', 404);
            }

            //Check if team has logo
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            //Update team
            $team->update([
                'name' => $request->name,
                'icon' => $path,
                'company_id' => $request->company_id,
            ]);

            //Return response
            return ResponseFormatter::success($team, 'Team updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            //Get team data
            $team = Team::find($id);

            // TODO: Check if team is owned by user

            //Check if team exists
            if (!$team) {
                return ResponseFormatter::error('Team not found', 404);
            }

            //Delete team
            $team->delete();

            //Return response
            return ResponseFormatter::success($team, 'Team deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
