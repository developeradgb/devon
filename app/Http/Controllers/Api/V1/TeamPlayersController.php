<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\PlayerCollection;

class TeamPlayersController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Team $team
     * @return \Illuminate\Http\Response
     */
    public function index(Team $team)
    {
        $players = $team
            ->players()
            ->latest()
            ->paginate();

        return new PlayerCollection($players);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Team $team
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Team $team)
    {
        $validated = $request->validate([
            'firstName' => ['required', 'max:255', 'string'],
            'lastName' => ['required', 'max:255', 'string'],
            'playerImageURI' => ['required', 'image'],
        ]);

        if ($request->hasFile('playerImageURI')) {
            $validated['playerImageURI'] = $request
                ->file('playerImageURI')
                ->store('public');
        }

        $player = $team->players()->create($validated);

        return new PlayerResource($player);
    }
}
