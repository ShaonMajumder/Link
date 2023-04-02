<?php

namespace App\Http\Controllers;

use App\Http\Components\Message;
use App\Models\Link;
use App\Models\Tag;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LinkController extends Controller
{
    use Message;

    public function tagCount(Request $request){
        $tags = Tag::all();
        foreach($tags as $tag){
            $tag->link_count = 0;
        }

        DB::transaction(function () use ($tags) {
            $tags->each(function ($item) {
                $item->save();
            });
        });

        $links = Link::all();
        foreach($links as $link){
            
            $tags = $link->tags;
            if($tags){
                try{
                    foreach($tags as $tag){
                        // $count_n = Link::whereJsonContains('tags', $tag)->count();
                        Tag::where('id',$tag)
                            ->update([
                                'link_count'=> DB::raw('link_count+1'), 
                              ]);
                    }
                }catch(Exception $e){
                    // error at
                    dd($link);
                }
            }
        }
    }

    public function listIndex($message=null){
        $columns=[];
        $query = "SHOW COLUMNS FROM links";
        $results = DB::select($query);
        foreach($results as $result){
            array_push($columns,$result->Field);
        }
            
    
        $links = Link::leftJoin('tags', function ($join) {
            $join->on(DB::raw("JSON_CONTAINS(links.tags, CONCAT('[', tags.id, ']'))"), '=', DB::raw('1'));
        })
        ->select('links.id',
                'links.link',
                'links.description',
                'links.meta',
                'links.title',
                    DB::raw('GROUP_CONCAT(CONCAT(\'<a href="/links/tags/\', tags.id, \'">\',
                          tags.name, \'</a>\') SEPARATOR ", ") as tag_names'),
                'links.bulkin',
                'links.created_at',
                'links.updated_at',
                'links.total_open_number',
                )
        ->groupBy('links.id')
        ->paginate(10);
        
        if($message){
            return view('link.list',compact('links','columns'))->with('message','New People added ...');
        }else{
            return view('link.list',compact('links','columns'));
        }
    }

    public function edit(Link $link){
        dd($link);
    }

    public function showUser(User $user){
        dd($user);
    }

    public function selectAllParents($tag){
        if(is_numeric($tag)){
            $tagObj = Tag::where('id',$tag);
        }else{
            $tagObj = Tag::where('name',$tag);
        }
          
        if( $tagObj->count() > 0 ){
            $parents = [];
            $current = $tagObj->first();
            if($current->parent_id){
                if( is_int($current->parent_id) ){
                    $parent_id_arr =[$current->parent_id];
                    $current->update(['parent_id' => $parent_id_arr ]); // correction code
                }else{
                    $parent_id_arr =$current->parent_id;
                }
                $parent_tags = Tag::whereIn('id',$parent_id_arr)->get();
                foreach( $parent_tags as $tag ){
                    $parents[] = $tag;
                }
            }
            
            $this->apiSuccess();
            $this->data = $parents;
            $message = "Parent tags got successfully";
            return $this->apiOutput(Response::HTTP_OK, $message);
        }else{
            $message = $tag." is a new tag ...";
            return $this->apiOutput(Response::HTTP_OK, $message);
        }
    }

    public function tagUpdate(Tag $tag,Request $request){
        // fix adding child tags
        if($request->tags){
            foreach ($request->tags as $key) { 
                if (!(is_numeric($key))) {
                    return $this->apiOutput(Response::HTTP_OK,"Adding new tags are not allowed here, added them in link entry ...");
                } 
                $tagA = Tag::find($key);
                $tag_ids = $tagA->parent_id;
                $tag_ids[] = $tag->id;
                $tagA->update(['parent_id'=> array_unique($tag_ids) ]);
            }

            $this->apiSuccess();
            return $this->apiOutput(Response::HTTP_OK,"Child Tags Updated ...");
        }else{
            // empty from all child tags
            $tags =Tag::where('parent_id',$tag->id)->update(['parent_id' => null]);
            $this->apiSuccess();
            return $this->apiOutput(Response::HTTP_OK,"Removed as parent from all child tags ...");
        }
        
        
        

        

        
    }

    public function tagEditPage(Tag $tag){
        $tags = Tag::all();
        $child_tags = Tag::whereJsonContains('parent_id',$tag->id)->pluck('id')->toArray();
        return view('link.tags.edit',compact('tags','tag','child_tags'));
    }

    public function tagsIndex(){
        $tags = Tag::all();
        $tags = Tag::leftJoin('links', function ($join) {
            $join->on(DB::raw('JSON_CONTAINS(links.tags, CAST(tags.id as JSON))'), '=', DB::raw('1'));
        })
        ->select('tags.*', DB::raw('COUNT(links.id) as links_count'))
        ->groupBy('tags.id')
        ->get();
        
        return view('link.tags.index',compact('tags'));
    }

    public function checkUniqueLink(Request $request){
        $link = Link::where('link',$request->link);

        $check_unique = false;
        if($link->count() > 0){
            $check_unique = true;
            $link = $link->first();
            $link_tags = ( is_array($link->tags) ? $link->tags : explode(',', $link->tags) ) ?? [] ;
            $selected_tags = Tag::whereIn('id',$link_tags)->get();
            $unselected_tags = Tag::whereNotIn('id',$link_tags)->get();
            $check_unique = true;
            $this->data = [
                'selected_tags' => $selected_tags->toJson(),
                'unselected_tags' => $unselected_tags->toJson(),
                'check_unique' => $check_unique
            ]; 
            return $this->apiOutput(Response::HTTP_OK, "Link exists ...");
        }else{
            $this->apiSuccess();
            $this->data = [
                'check_unique' => $check_unique
            ]; 
            return $this->apiOutput(Response::HTTP_OK, "Unique Link ...");
        }

    }

    public function bulkInput(Request $request)
    {
         
        
 
    }

    public function getLinkDetails(Request $request){
        $url = $request->link;
        // Extract HTML using curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        // Load HTML to DOM object
        $dom = new \DOMDocument();
        @$dom->loadHTML($data);
        // Parse DOM to get Title data
        $nodes = $dom->getElementsByTagName('title');
        $title = $nodes->item(0)->nodeValue;
        
        // Parse DOM to get meta data
        $metas = $dom->getElementsByTagName('meta');
        $description = '';
        for($i=0; $i<$metas->length; $i++){
            $meta = $metas->item($i);
            print_r($meta);
            
            if($meta->getAttribute('name') == 'description'){
                $description = $meta->getAttribute('content');
            }

        }
        
        return [
            'title' => $title,
            'description' => $description,
            // 'meta' => $meta
        ];
    }
    
    public function store(Request $request){
        $validated = Validator::make($request->all(),[
            'link' => 'required', //unique:links
            'tags' => 'required',
        ]);

        if($validated->fails()){
            return $this->apiOutput(Response::HTTP_BAD_REQUEST, $this->getValidationError($validated) );
        }

        $request->merge([
            'tags' => explode(",",$request->tags)
        ]);

        // dd($request->all());
        // dd($this->getLinkDetails($request));
        // $request->request->remove('_token');

        $tag_values = [];
        foreach($request->tags as $tag){
            if( !is_numeric($tag) && Tag::where('name',$tag)->count() == 0 ){
                $tagObj = Tag::create([
                    'name' => $tag,
                    'causer_id' => Auth::user()->id
                ]);
                $tag = $tagObj->id;
            }
            $tag_values[] = (int)$tag;
        }
            
        $request->tags = $tag_values;

            
        $link = Link::where('link',$request->link);
        if($link->count() > 0){
            $link->update(['tags' => $request->tags]);
            $message = "Link updated ...";
        }else{
            Link::create($request->only('link','tags'));
            $message = "New Link created ...";
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
        return $this->apiOutput(Response::HTTP_OK, $message  ?? " Links added ...");  
        // return $this->listLinks('New People added ...');
        
        
    }

    public function listTags(){
        $tags = Tag::get();
        $this->data = $tags->toJson();
        $this->apiSuccess();
        return $this->apiOutput(Response::HTTP_OK, "All properties listed ...");
    }

    public function lineCount($file){
        $linecount = 0;
        $handle = fopen($file, "r");
        while(!feof($handle)){
        $line = fgets($handle);
        $linecount++;
        }
        fclose($handle);
        return $linecount;
    }

    public function randomPage(){
        return view('link.random');
    }

    public function array_equal($a, $b) {
        return (
             is_array($a) 
             && is_array($b) 
             && count($a) == count($b) 
             && array_diff($a, $b) === array_diff($b, $a)
        );
    }

    public function randomChoose(Request $request,$file="input.list"){
        $tags = explode(',',$request->tags);

        $array_not_initialized = (!Session::has('random_links')) or (!Session::has('random_keys'));
        $random_keys = Session::get('random_keys');
        $array_visited_all_links =  !$this->array_equal($random_keys, $tags);

        // on initial hit of application run
        if($array_not_initialized or $array_visited_all_links){
            Session::put('random_links',[]);
            Session::put('random_keys',$tags);
        }

        $link = Link::where(function($query) use($tags){
            $qpiece = [];
            foreach( $tags as $tag) {
                $qpiece[] = "JSON_CONTAINS(tags, '$tag')";
            }
            $qpiece_string = implode(" AND ",$qpiece);
            return $query->WhereRaw($qpiece_string);
        })->get();

        if($link->count()){
            $previous_random_links = Session::get('random_links');
            if( count($previous_random_links) == $link->count() ){
                $previous_random_links = [end($previous_random_links)];
            }
            while( in_array($random_link = $link->random()->link, $previous_random_links) ){}
            array_push($previous_random_links, $random_link);
            Session::put('random_links',$previous_random_links);
            Session::put('random_keys',$tags);
            
            $this->apiSuccess();
            $this->data = [
                "goto" => $random_link,
                "count" => $link->count()
            ];
            return $this->apiOutput(Response::HTTP_OK, "Random Link picked ...");
        }else{
            return $this->apiOutput(Response::HTTP_NOT_FOUND, "No Link picked ...");
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
}
