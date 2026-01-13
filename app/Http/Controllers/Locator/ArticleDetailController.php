<?php

namespace App\Http\Controllers\Locator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateArticleDetail;
use App\Http\Requests\StoreArticleDetail;
use App\Models\Locator\ArticleDetail;
use Inertia\Inertia;

class ArticleDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $article= ArticleDetail::all();
        return;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleDetail $request)
{  
    $validated = $request->validated();
      
    $article = ArticleDetail::create([
        'application_form_id' => $validated['application_form_id'],
        'marks_and_number' => $validated['marks_and_number'],
        'qty' => $validated['qty'],
        'detailed_description_of_article' => $validated['detailed_description_of_article'],
        'gross_weight' => $validated['gross_weight'] ?? null,
        'user_id' => auth()->id(),
    ]);

    return response()->json(['article' => $article]);
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleDetail $request, $id)
{   
    $article = ArticleDetail::findOrFail($id);
    if($article){

    }
    $validated = $request->$request->validated();

    $article->update($validated);

    return response()->json(['article' => $article]);
}

    /**
     * Remove the specified resource from storage.
     */
 public function destroy($id)
{
    $article = ArticleDetail::findOrFail($id);
    $article->delete();

    
    return response()->noContent(); 
}
public function verifyArticle(Request $request, $id)
{      dd($id);
    
    $article = ArticleDetail::find($id);

    if (!$article) {
        return response()->json(['message' => 'Article not found'], 404);
    }

    
    $article->status = 'Verified';
    $article->verified_at = now(); // optional timestamp
    $article->save();

    
    return redirect()->back()->with('success', 'Article verified successfully');
}
}
