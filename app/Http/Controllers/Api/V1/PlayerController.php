<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Player;
use App\Traits\UploadFileTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PlayerResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PlayerCollection;
use App\Http\Requests\PlayerStoreRequest;
use App\Http\Requests\PlayerUpdateRequest;

class PlayerController extends Controller
{
    use UploadFileTrait;

    /**
     * @param \Illuminate\Http\Request $request
     * @return PlayerCollection
     */
    public function index(Request $request)
    {
        $players = Player::with('team')->latest()
            ->paginate();

        return new PlayerCollection($players);
    }

    /**
     * @param \App\Http\Requests\PlayerStoreRequest $request
     * @return PlayerResource|\JsonResponse
     */
    public function store(PlayerStoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['playerImageURI'] = $this->storeUploadedFile($request, 'playerImageURI');

            $player = Player::create($validated);

            DB::commit();
            return new PlayerResource($player);
        } catch (\Throwable $throwable) {
            DB::rollBack();
            logError('Error while creating player', 'Api\V1\PlayerController@store', $throwable);
            return simpleMessageResponse('Server Error', INTERNAL_SERVER);
        }
    }

    /**
     * @param $playerId
     * @return PlayerResource|\JsonResponse
     */
    public function show($playerId)
    {
        try {
            $player = Player::with('team')->findOrFail($playerId);
            return new PlayerResource($player);
        } catch (ModelNotFoundException $e) {
            return simpleMessageResponse('Player not found', NOT_FOUND);
        }catch (\Throwable $throwable) {
            logError('Error while getting player details', 'Api\V1\PlayerController@show', $throwable);
            return simpleMessageResponse('Server Error', INTERNAL_SERVER);
        }
    }

    /**
     * @param \App\Http\Requests\PlayerUpdateRequest $request
     * @param \App\Models\Player $player
     * @return PlayerResource|\JsonResponse
     */
    public function update(PlayerUpdateRequest $request, Player $player)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            if ($request->hasFile('playerImageURI')) {
                if ($player->playerImageURI) {
                    Storage::delete($player->playerImageURI);
                }

                $validated['playerImageURI'] = $this->storeUploadedFile($request, 'playerImageURI');
            }

            $player->update($validated);

            DB::commit();
            return new PlayerResource($player);
        } catch (\Throwable $throwable) {
            DB::rollBack();
            logError('Error while updating player details', 'Api\V1\PlayerController@update', $throwable);
            return simpleMessageResponse('Server Error', INTERNAL_SERVER);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Player $player
     * @return \JsonResponse
     */
    public function destroy(Request $request, Player $player)
    {
        try {
            if ($player->playerImageURI) {
                Storage::delete($player->playerImageURI);
            }

            $player->delete();

            return simpleMessageResponse('Player deleted successfully');
        } catch (\Throwable $throwable) {
            DB::rollBack();
            logError('Error while deleting player', 'Api\V1\PlayerController@delete', $throwable);
            return simpleMessageResponse('Server Error', INTERNAL_SERVER);
        }
    }
}
