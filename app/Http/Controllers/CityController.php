<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\CityResource;
use App\Models\City;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('combo')) {
            return [
                'message' => 'Lista de ciudades',
                'payload' => [
                    'data' => DB::table('cities')->orderBy('id')->get(),
                ],
            ];
        }
        return [
            'message' => 'Lista de ciudades',
            'payload' => [
                'data' => CityResource::collection(DB::table('cities')->orderBy('name')->get()),
            ],
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show(City $city)
    {
        return [
            'message' => 'Datos de la ciudad',
            'payload' => [
                'city' => new CityResource($city),
            ],
        ];
    }
}
