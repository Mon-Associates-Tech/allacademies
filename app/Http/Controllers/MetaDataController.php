<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\MetaData;
use App\Http\Requests\MetaDataRequest;

class MetaDataController extends Controller
{
    /**
     * Update the specified resource in storage if it already exist
     * create a new resource if does not already exist
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team
     */
    public function updateOrCreate(MetaDataRequest $request, Team $team)
    {
        abort_unless($team->owner_id === auth()->id(), 403, 'You can not edit this team');

        //create or update meta data
        $metaDataAll = is_null($team->metaData) ? null : $team->metaData->meta ?? null;
        $metaData = is_null($metaDataAll) ? null : $metaDataAll[count($metaDataAll) - 1] ?? null;

        $path = is_null($metaData) ? null : $metaData['logo'] ?? '';
        $db_logo = $path;

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('images');
        }

        $meta = [
            'type' => $request->type,
            'name' => $request->institution,
            'department' => $request->department,
            'logo' => $db_logo,
            'faculty' => $request->faculty,
            'school' => $request->school,
            'college' => $request->college,
        ];

        $diff_meta = array_diff($meta, $metaData ?? []);
        $meta = array_replace($meta, ['logo' => $path]);

        if ($metaDataAll) {
            if (count($metaDataAll) == 5) {
                unset($metaDataAll[0]);
                $metaDataAll = array_values($metaDataAll);
            }
            $data = $metaDataAll;
            $data[] = $meta;
        } else {
            $data[]  = $meta;
        }

        MetaData::updateOrCreate(
            ['team_id' => $team->id],
            ['meta' => $data]
        );

        if ($diff_meta) {
            $team->status = 'pending';
            $team->save();
        }
    }
}
