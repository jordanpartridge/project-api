<?php

namespace App\Http\Controllers;

use App\Models\Documentation;
use Illuminate\Http\Request;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

class DocumentationController extends Controller
{
    protected MarkdownConverter $markdownConverter;

    public function __construct()
    {
        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $this->markdownConverter = new MarkdownConverter($environment);
    }

    public function index(Request $request)
    {
        $selectedCategory = $request->get('category');
        
        $docs = Documentation::query()
            ->published()
            ->when($selectedCategory, fn($query) => 
                $query->where('category', $selectedCategory)
            )
            ->orderBy('order')
            ->paginate(10);
            
        $categories = Documentation::published()
            ->select('category')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('category')
            ->get();
            
        return view('pages.docs', compact('docs', 'categories', 'selectedCategory'));
    }

    public function show(Documentation $doc)
    {
        $doc->content = $this->markdownConverter->convert($doc->content)->getContent();
        
        return view('pages.docs.show', compact('doc'));
    }
}