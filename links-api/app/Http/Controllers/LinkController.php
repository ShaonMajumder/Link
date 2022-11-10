<?php

namespace App\Http\Controllers;

use App\Http\Components\ApiTrait;
use App\Models\Link;
use App\Models\Tag;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
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


    public function store(Request $request){
        // Log::info($request->all());
        // return response()->json($request->all());
        // dd($request->all());
        $new_request = $request->except(['_token']);
        $request_result = false;
        foreach ($new_request as $value)
            $request_result = $request_result || ($value != null);
        $request_result = ($request->file && $request->file != 'undefined') || $request_result;
        

        if($request_result ){
            
            if($request->tags){
                if( ! is_array($request->tags) )
                    $request->tags = explode(",",$request->tags);
                
                    
                $tag_values = [];
                foreach($request->tags as $tag){
                    if( ! is_numeric($tag) and ! Tag::where('name',$tag)->first() ){
                        
                        $tagObj = new Tag();
                        $tagObj->name = $tag;
                        $tagObj->causer_id = Auth::user()->id;
                        $tagObj->save();
                        $tag = $tagObj->id;

                        $text = "New Tag '$tag' added";
                    }
                    $tag_values[] = (int)$tag;
                }
                
                
                $request->tags = $tag_values;
                
                if($request->link){
                    $link = Link::where('link',$request->link);
                    if($link->count() > 0){
                        $link->update(['tags' => $request->tags]);
                        $message = "Link updated ...";
                    }else{
                        Link::create($request->only('link','tags'));
                        $message = "New Link created ...";
                    }    
                }

                
            }
            
            if($request->file && $request->file != 'undefined'){
                $validatedData = $request->validate([
                    'file' => 'required|max:2048',
                ]);
                $name = $request->file('file')->getClientOriginalName();
                $path = $request->file('file')->store('public/files');
                
                
                $fileName = auth()->id() . '_' . time() . '.'. $request->file->extension();  
                // dd(public_path(''), $fileName);
                $request->file->move(public_path(''), $fileName);
                

                

                $number = 0;
                $records = [];
                $lines = file($fileName);
                
                foreach($lines as $line){
                    $link = trim(preg_replace('/\s\s+/', ' ', $line));
                    $records[] = $link;
                    // $result = Link::firstOrCreate(['link'=> $link, 'bulkin'=>true ]);
                    // if($result->wasRecentlyCreated)
                    //     $number++;
                }
                
                $rows = $records;
                $matched_result = Link::whereIn('link',$rows)->pluck('link')->toArray();
                foreach($matched_result as $result)
                    $temp[$result] = 1;
                
                $data = [];
                foreach($rows as $row){
                    
                    if(!isset($temp[$row]) and $row != ''){
                        $number++;
                        $data[] = [ 'link' => $row , 'tags' => json_encode($request->tags) ];
                    }
                }
                    
                Link::insert($data);

                $this->apiSuccess();
                return $this->apiOutput(Response::HTTP_OK, $number." Links added ...");
                
            }
            

            // $myfile = fopen("contents.list", "a") or die("Unable to open file!");
            // $txt = $request->link;
            // fwrite($myfile, "\n". $txt);
            // fclose($myfile);

            
            $this->apiSuccess();
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_OK,
                    $message     = $message,
                )
            );
        }else{
            return response()->json(
                ...$this->apiResponseBuilder(
                    $status_code = Response::HTTP_UNPROCESSABLE_ENTITY,
                    $message     = 'Minimum one field is required ...',
                )
            );
        }
        
    }
}
