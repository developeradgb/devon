<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\TeamShowResource;
use App\Models\Team;
use App\Http\Resources\TeamResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeamStoreRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\TeamUpdateRequest;

class TeamController extends Controller
{
    /**
     * @return TeamResource
     */
    public function index()
    {
        $teams = Team::latest()->get();

        return new TeamResource($teams);
    }

    /**
     * @param \App\Http\Requests\TeamStoreRequest $request
     * @return TeamResource|\JsonResponse
     */
    public function store(TeamStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->all();
            if ($request->hasFile('logoURI')) {
                $validated['logoURI'] = $request->file('logoURI')->store('public');
            }
            $team = Team::create($validated);
            DB::commit();

            return new TeamResource($team);
        } catch (\Throwable $throwable) {
            DB::rollBack();
            logError('Error while creating team', 'Api\V1\TeamController@store', $throwable);
            return simpleMessageResponse('Server Error', INTERNAL_SERVER);
        }
    }


    /**
     * @param Team $team
     * @return TeamResource
     */
    public function show($team)
    {
        $team  = Team::with('players')->find($team);
        return new TeamShowResource($team);
    }

    /**
     * @param \App\Http\Requests\TeamUpdateRequest $request
     * @param \App\Models\Team $team
     * @return TeamResource|\JsonResponse
     */
    public function update(TeamUpdateRequest $request, Team $team)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            if ($request->hasFile('logoURI')) {
                if ($team->logoURI) {
                    Storage::delete($team->logoURI);
                }

                $validated['logoURI'] = $request->file('logoURI')->store('public');
            }

            $team->update($validated);

            DB::commit();
            return new TeamResource($team);
        } catch (\Throwable $throwable) {
            DB::rollBack();
            logError('Error while updating team', 'Api\V1\TeamController@update', $throwable);
            return simpleMessageResponse('Server Error', INTERNAL_SERVER);
        }
    }

    /**
     * @param \App\Models\Team $team
     * @return \JsonResponse
     */
    public function destroy(Team $team)
    {
        try {
            if ($team->logoURI) {
                Storage::delete($team->logoURI);
            }

            $team->delete();

            return simpleMessageResponse('Team deleted successfully');
        } catch (\Throwable $throwable) {
            logError('Error while creating team', 'Api\V1\TeamController@store', $throwable);
            return simpleMessageResponse('Server Error', INTERNAL_SERVER);
        }
    }
}
