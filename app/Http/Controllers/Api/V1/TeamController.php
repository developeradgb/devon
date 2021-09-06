<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\TeamShowResource;
use App\Models\Team;
use App\Http\Resources\TeamResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeamStoreRequest;
use App\Traits\UploadFileTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\TeamUpdateRequest;

class TeamController extends Controller
{
    use UploadFileTrait;

    /**
     * @return TeamResource|\JsonResponse
     */
    public function index()
    {
        try {
            $teams = Team::latest()->get();

            return new TeamResource($teams);
        } catch (\Throwable $throwable) {
            DB::rollBack();
            logError('Error while getting team list', 'Api\V1\TeamController@index', $throwable);
            return simpleMessageResponse('Error while getting team list', INTERNAL_SERVER);
        }

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
            $validated['logoURI'] = $this->storeUploadedFile($request, 'logoURI');

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
     * @return TeamShowResource|\JsonResponse
     */
    public function show($team)
    {
        try {
            $team = Team::with('players')->findOrFail($team);
            return new TeamShowResource($team);
        } catch (ModelNotFoundException $e) {
            return simpleMessageResponse('Team not found', NOT_FOUND);
        } catch (\Throwable $throwable) {
            logError('Error while showing team', 'Api\V1\TeamController@show', $throwable);
            return simpleMessageResponse('Server Error', INTERNAL_SERVER);
        }
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

                $validated['logoURI'] = $this->storeUploadedFile($request, 'logoURI');
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
