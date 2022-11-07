<?php

namespace App\Http\Controllers;

use App\Http\Components\ApiTrait;
use App\Models\Link;
use App\Models\Tag;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LinkController extends Controller
{
    use ApiTrait;

    /**
     * List Links with Pagination
     */
    public function listLinks(Request $request){        
        $this->apiSuccess();
        $this->data = [
            'links' => Link::paginate(10)//new UserResource($user)
        ];
        return response()->json(
            ...$this->apiResponseBuilder(
                $status_code = Response::HTTP_OK,
                $message     = 'Link List populated Successfully',
            )
        );
    }
    

    public function getLink(Request $request,Link $id){        
        $this->apiSuccess();
        $this->data = $id;
        
        return response()->json(
            ...$this->apiResponseBuilder(
                $status_code = Response::HTTP_OK,
                $message     = 'Got link Successfully',
            )
        );
    }





    /**
     * Adds Link
     */
    public function addLink(Request $request){
        $validator = Validator::make($request->all(),[
            'title' => ['required'],
            'author' => ['required'],
            'image' => ['image']
        ]);

        if($validator->fails()){
            $this->data = $validator->errors(); //->first();
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_UNPROCESSABLE_ENTITY,
                    $message = 'Here is an error occured !'
                )
            );
        }

        try{
            // dd($request->post());
            // $imageName = Str::random().'.'.$request->image->getClientOriginalExtension();
            // Storage::disk('public')->putFileAs('product/image', $request->image,$imageName);
            // Product::create($request->post()+['image'=>$imageName]);

            Link::insert([
                'title' => $request->title,
                'author' => $request->author
            ]);

            $this->data = Link::orderBy('id', 'desc')->first();
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_OK,
                    $message = 'Links added successfully !'
                )
            );
            
        }catch(Exception $e){
            $this->data = $this->getExceptionError($e); //->first();
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_UNPROCESSABLE_ENTITY,
                    $message = 'Link is not added !'
                )
            );
        }

    }

    /**
     * Update Link
     */
    public function updateLink(Request $request, Link $id){
        // dd($request->except('_method'));
        $validator = Validator::make($request->all(),[
            'title' => ['required'],
            'author' => ['required'],
            'image' => ['image']
        ]);

        if($validator->fails()){
            $this->data = $validator->errors(); //->first();
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_UNPROCESSABLE_ENTITY,
                    $message = 'Payload Validation is failed !'
                )
            );
        }

        try{
            $id->title = $request->title;
            $id->author = $request->author;
            $id->save();
            $this->apiSuccess();
            $this->data = $id;
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_OK,
                    $message = 'Links updated successfully !'
                )
            );
            
        }catch(Exception $e){
            $this->data = $this->getExceptionError($e); //->first();
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_UNPROCESSABLE_ENTITY,
                    $message = 'Link was not updated !'
                )
            );
        }
    }

    /**
     * Adds Link
     */
    public function deleteLink(Link $id){
        try{
            $id->delete();
            $this->apiSuccess();
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_OK,
                    $message = 'Links deleted successfully !'
                )
            );
            
        }catch(Exception $e){
            $this->data = $this->getExceptionError($e); //->first();
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_UNPROCESSABLE_ENTITY,
                    $message = 'Link is not deleted !'
                )
            );
        }
    }

    /**
     * List all tags
     */
    public function listTags(Request $request){
        try{
            $tags = Tag::select('name','causer_id')->get();
            $this->data = $tags; //->toJson();
            $this->apiSuccess();
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_OK,
                    $message = 'All tags listed ...!'
                )
            );
            
        }catch(Exception $e){
            $this->data = $this->getExceptionError($e); //->first();
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_SERVICE_UNAVAILABLE,
                    $message = 'All tags are not listed ...!'
                )
            );
        }
       
    }
}
