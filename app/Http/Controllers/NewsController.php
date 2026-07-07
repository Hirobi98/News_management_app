<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NewsController extends Controller
{
    // Hardcoded dummy data for news
    private $newsData = [
        1 => [
            'id' => 1,
            'title' => 'The Future of Artificial Intelligence',
            'content' => 'AI is revolutionizing the way we interact with technology. From chatbots to self-driving cars, the possibilities are endless. In this article, we explore the potential impact of AI on various industries and the ethical considerations that come with it...',
            'author' => 'Jane Smith',
            'channel' => 'Technology',
            'date' => '2026-07-07',
            'comments' => [
                ['user' => 'Alex', 'text' => 'Great insights! AI is truly the future.'],
                ['user' => 'Sarah', 'text' => 'I worry about the ethical implications mentioned.']
            ]
        ],
        2 => [
            'id' => 2,
            'title' => 'Global Markets Reach Record Highs',
            'content' => 'Stock markets around the world have surged to unprecedented levels, driven by strong economic data and tech sector growth. Investors remain optimistic despite looming inflation concerns...',
            'author' => 'Michael Chen',
            'channel' => 'Finance',
            'date' => '2026-07-06',
            'comments' => [
                ['user' => 'InvestorPro', 'text' => 'Time to take some profits!']
            ]
        ],
        3 => [
            'id' => 3,
            'title' => 'Breakthrough in Renewable Energy',
            'content' => 'Scientists have developed a highly efficient solar panel that could significantly reduce the cost of renewable energy. The new technology utilizes advanced materials to capture a wider spectrum of sunlight...',
            'author' => 'Dr. Elena Rossi',
            'channel' => 'Science',
            'date' => '2026-07-05',
            'comments' => []
        ],
    ];

    public function index()
    {
        if (!session('user_logged_in')) {
            return redirect('/login')->with('error', 'Please login to view news.');
        }

        return view('news.index', ['newsList' => $this->newsData]);
    }

    public function show($id)
    {
        if (!session('user_logged_in')) {
            return redirect('/login')->with('error', 'Please login to view news.');
        }

        $news = $this->newsData[$id] ?? null;

        if (!$news) {
            return redirect('/home')->with('error', 'News article not found.');
        }

        return view('news.show', ['news' => $news]);
    }

    public function create()
    {
        if (!session('user_logged_in') || session('user_role') === 'reader') {
            return redirect('/home')->with('error', 'You do not have permission to write news.');
        }

        return view('news.create');
    }

    public function store(Request $request)
    {
        if (!session('user_logged_in') || session('user_role') === 'reader') {
            return redirect('/home')->with('error', 'You do not have permission to write news.');
        }

        // Mock store action
        $title = $request->input('title');
        
        return redirect('/home')->with('success', "Your news article '{$title}' was successfully published!");
    }

    public function storeComment(Request $request, $id)
    {
        if (!session('user_logged_in')) {
            return redirect('/login');
        }

        // Mock comment store
        return redirect()->back()->with('success', 'Your comment was added successfully!');
    }
}
