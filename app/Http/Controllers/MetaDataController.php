<?php

namespace App\Http\Controllers;
use App\Http\Requests\MetaDataRequest;
use App\Models\Team;
use App\Models\MetaData;

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
        $path = is_null($team->metaData) ? null : $team->metaData->meta['logo'] ?? '';
        if($request->hasFile('logo')){
            $path = $request->file('logo')->store('images', 'public');
        }

        $dbMetaData = MetaData::where('team_id', $team->id)->first();
        $meta = [
            'institution_type' => $request->institution_type,
            'institution_name' => $request->institution_name,
            'department' => $request->department,
            'logo' => $dbMetaData->meta['logo'],
            'faculty' => $request->faculty,
            'school' => $request->school,
            'college' => $request->college,
        ];

        $diff_meta = array_diff($meta, $dbMetaData->meta);
        $meta = array_replace($meta, ['logo' => $path]);
       
        MetaData::updateOrCreate(
            ['team_id' => $team->id],
            ['meta' => $meta]
        );
    
        if($diff_meta){     
            $team->status = 'pending';
            $team->save();
        }
    }

}
