<?php

namespace App\Http\Controllers;

use App\Http\Components\Message;
use App\Models\People;
use App\Models\Property;
use App\Models\Value;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PeopleController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(){
        return view('link.create');
    }

    public function listLinks($message=null){
        $columns=[];
        $query = "SHOW COLUMNS FROM people";
        $results = DB::select($query);
        foreach($results as $result)
            array_push($columns,$result->Field);
        $peoples = People::latest()->paginate(10);
        
        if($message){
            return view('link.list',compact('peoples','columns'))->with('message','New People added ...');
        }else{
            return view('link.list',compact('peoples','columns'));
        }
    }

    public function showAddPeopleInformationForm(People $people){
        // $properties = Property::
        // $people->values
        $values = People::join('values', 'values.people_id', '=', 'people.id')
                        ->join('properties', 'properties.id', '=', 'values.property_id')
                        // ->select('property_id')
                        ->select('*','people.name as name','properties.name as property_name',)
                        ->get()
                        ->toArray();
        // dd($values);
        // $property_ids = array_column($values, 'property_id');

                        
                            
        
        return view('people.add_people_info',compact('people','values'));
    }

    public function insert(Request $request){
        $new_request = $request->except(['_token']);
        $request_result = false;
        foreach ($new_request as $value)
            $request_result = $request_result || ($value != null);

        if($request_result ){
            People::create($new_request);
            // $this->apiSuccess();
            // return $this->apiOutput(Response::HTTP_OK, "New People added ...");  
            return $this->listLinks('New People added ...');
        }else{
            return $this->apiOutput(Response::HTTP_OK, "Minimum one field is required ...");
        }
        
    }

    public function addInfo(Request $request){
        // people_id
        // dd($request->all());
        $text = null;
        $property_id = null;
        if( ! is_numeric($request->property) and ! Property::where('name',$request->property)->first() ){
            $property = new Property();
            $property->name = $request->property;
            $property->causer_id = Auth::user()->id;
            $property->save();
            $property_id = $property->id;
            $text = "New Property '$request->property' added";
        }

         
        $value = new Value();
        $value->people_id = $request->people_id;
        $value->property_id = $property_id ?? $request->property;
        $value->value = $request->value;
        $value->save();

        $text = $text ? $text." and data added ..." : "Data added ...";
        
        $this->apiSuccess();
        return $this->apiOutput(Response::HTTP_OK, $text);
        
    }

    public function listProperties(){
        $properties = Property::get();
        $this->data = $properties->toJson();
        $this->apiSuccess();
        return $this->apiOutput(Response::HTTP_OK, "All properties listed ...");
    }
}
