<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    private function mapNewsItem($row)
    {
        // Handle CLOB data type for Oracle news descriptions
        $content = '';

        $description = $row->news_description ?? $row->NEWS_DESCRIPTION ?? null;

        if ($description) {
            if (is_resource($description)) {
                $content = stream_get_contents($description);
            } else {
                $content = (string) $description;
            }
        }

        $dateStr = 'N/A';

        $date = $row->date ?? $row->DATE ?? null;

        if ($date) {
            $cleanDate = preg_replace('/\.(\d{6})/', '', (string)$date);
            $cleanDate = str_replace('.', ':', $cleanDate);
            $timestamp = strtotime($cleanDate);
            if ($timestamp !== false) {
                $dateStr = date('F j, Y, g:i A', $timestamp);
            } else {
                $dateStr = (string)$date;
            }
        }

        return [
            'id' => $row->id ?? $row->ID,
            'title' => $row->news_title ?? $row->NEWS_TITLE,
            'content' => $content,
            'author' => $row->author_name ?? $row->AUTHOR_NAME,
            'channel' => $row->category ?? $row->CATEGORY,
            'target_channel_name' => $row->target_channel_name ?? $row->TARGET_CHANNEL_NAME ?? 'Unknown',
            'target_channel_email' => $row->target_channel_email ?? $row->TARGET_CHANNEL_EMAIL ?? 'N/A',
            'date' => $dateStr,
            'image' => $row->image ?? $row->IMAGE ?? '',
            'status' => $row->status ?? $row->STATUS ?? 'Draft',
            'comments' => []
        ];
    }

    public function fetchLiveNews()
    {
        try {
            $response = Http::get('https://newsapi.org/v2/top-headlines', [
                'language' => 'en',
                'pageSize' => 5,
                'apiKey' => env('NEWS_API_KEY')
            ]);

            if ($response->successful()) {
                $apiData = $response->json();
                
                $formattedArticles = [];
                $articles = $apiData['articles'] ?? [];
                
                foreach ($articles as $article) {
                    $formattedArticles[] = [
                        'title' => $article['title'] ?? 'No Title',
                        'source' => $article['source']['name'] ?? 'Unknown Source',
                        'url' => $article['url'] ?? '#'
                    ];
                }

                return response()->json([
                    'status' => 'ok',
                    'articles' => $formattedArticles
                ], 200);
            }
        } catch (\Exception $e) {
            // handle error below
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Unable to fetch international news at this time.'
        ], 500);
    }

    private function logAudit($action, $details)
    {
        DB::table('audit_logs')->insert([
            'action' => $action,
            'details' => $details,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login to view news.');
        }

        // Query published news using Query Builder
        $newsRows = DB::table('news_items as n')
            ->select('n.*', 'u.name as TARGET_CHANNEL_NAME', 'u.email as TARGET_CHANNEL_EMAIL')
            ->leftJoin('users as u', 'n.target_channel', '=', 'u.id')
            ->whereRaw('LOWER(n.status) = ?', ['published'])
            ->orderByDesc('n.date')
            ->orderByDesc('n.id')
            ->get();

        $newsList = [];
        foreach ($newsRows as $row) {
            $newsList[] = $this->mapNewsItem($row);
        }

        $userId = Auth::id();
        $channels = DB::select("
            SELECT u.ID, u.NAME, u.EMAIL
            FROM USERS u 
            JOIN CHANNEL_AUTHORS ca ON u.ID = ca.CHANNEL_ID 
            WHERE ca.AUTHOR_ID = ?
        ", [$userId]);

        return view('news.index', ['newsList' => $newsList, 'channels' => $channels]);
    }

    public function category($category)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login to view news.');
        }

        $newsRows = DB::table('news_items as n')
            ->select('n.*', 'u.name as TARGET_CHANNEL_NAME', 'u.email as TARGET_CHANNEL_EMAIL')
            ->leftJoin('users as u', 'n.target_channel', '=', 'u.id')
            ->whereRaw('LOWER(n.status) = ?', ['published'])
            ->whereRaw('LOWER(n.category) = ?', [strtolower($category)])
            ->orderByDesc('n.date')
            ->orderByDesc('n.id')
            ->get();

        $newsList = [];
        foreach ($newsRows as $row) {
            $newsList[] = $this->mapNewsItem($row);
        }

        $userId = Auth::id();
        $channels = DB::select("
            SELECT u.ID, u.NAME, u.EMAIL
            FROM USERS u 
            JOIN CHANNEL_AUTHORS ca ON u.ID = ca.CHANNEL_ID 
            WHERE ca.AUTHOR_ID = ?
        ", [$userId]);

        return view('news.index', ['newsList' => $newsList, 'channels' => $channels, 'currentCategory' => ucfirst($category)]);
    }

    public function show($id)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login to view news.');
        }

        // Fetch news article by ID
        // Fetch news article by ID
        $newsRow = DB::select(
            "SELECT n.*, u.NAME as TARGET_CHANNEL_NAME, u.EMAIL as TARGET_CHANNEL_EMAIL 
             FROM NEWS_ITEMS n 
             LEFT JOIN USERS u ON n.TARGET_CHANNEL = u.ID 
             WHERE n.ID = ?",
            [$id]
        );

        $newsRow = $newsRow[0] ?? null;

        if (!$newsRow) {
            return redirect('/home')->with('error', 'News article not found.');
        }

        $news = $this->mapNewsItem($newsRow);

        // Fetch comments for this article
        $commentRows = DB::select(
            "SELECT * FROM COMMENTS
     WHERE ARTICLE_ID=?
     ORDER BY ID ASC",
            [$id]
        );

        foreach ($commentRows as $cRow) {
            /*if($cRow->comment_text=="Shit"){
                    return redirect('/home')->with('error', 'Inappropriate comment detected.');

                }*/
            $news['comments'][] = [
                'user' => $cRow->user_name ?? $cRow->USER_NAME,
                
                'text' => $cRow->comment_text ?? $cRow->COMMENT_TEXT,
                'date' => $cRow->date ?? $cRow->DATE
            ];
        }

        return view('news.show', ['news' => $news]);
    }

    public function create()
    {
        $role = strtolower(Auth::user()->role);
        if (!Auth::check() || $role === 'reader') {
            return redirect('/home')->with('error', 'You do not have permission to write news.');
        }

        return view('news.create');
    }

    public function store(Request $request)
    {
        $role = strtolower(Auth::user()->role);
        if (!Auth::check() || $role === 'reader') {
            return redirect('/home')->with('error', 'You do not have permission to write news.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:50',
            'target_channel' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads'), $imageName);
            $imagePath = 'uploads/' . $imageName;
        }

        DB::table('news_items')->insert([
            'news_title' => $request->input('title'),
            'author_name' => Auth::user()->name,
            'news_description' => $request->input('content'),
            'category' => $request->input('category'),
            'status' => 'Pending_Channel',
            'date' => now(),
            'image' => $imagePath,
            'target_channel' => $request->input('target_channel')
        ]);

        // Auto-mark any pending tasks with this channel as submitted
        DB::table('channel_tasks')
            ->where('author_id', Auth::id())
            ->where('channel_id', $request->input('target_channel'))
            ->where('status', 'Pending')
            ->update(['status' => 'Submitted']);

        $this->logAudit('Author Draft', "Author " . Auth::user()->name . " drafted article '{$request->title}'.");

        return redirect('/home')->with('success', "Your dispatch '{$request->title}' was successfully sent to the news channel for review!");
    }

    public function storeComment(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $request->validate([
            'comment' => 'required|string|max:4000'
        ]);

        // Insert comment
        DB::table('comments')->insert([
            'article_id' => $id,
            'user_name' => Auth::user()->name,
            'comment_text' => $request->input('comment'),
            'status' => 'Approved',
            'date' => now()
        ]);

        return redirect()->back()->with('success', 'Your letter was added successfully!');
    }

    // Admin Dashboard Logic
    public function adminDashboard()
    {
        $totalArticles = DB::table('news_items')->where('status', 'Published')->count();
        $totalUsers = DB::table('users')->count();
        $totalComments = DB::table('comments')->count();

        // Retrieve all articles for admin
        $allNewsRows = DB::table('news_items as n')
            ->select('n.*', 'u.name as TARGET_CHANNEL_NAME')
            ->leftJoin('users as u', 'n.target_channel', '=', 'u.id')
            ->orderByDesc('n.id')
            ->get();

        $newsList = [];
        foreach ($allNewsRows as $row) {
            $item = $this->mapNewsItem($row);
            $item['admin_feedback'] = $row->admin_feedback ?? $row->ADMIN_FEEDBACK ?? null;
            $newsList[] = $item;
        }

        // Retrieve audit logs populated by the database trigger
        $auditLogs = DB::table('audit_logs')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Fetch Global Tasks
        $globalTasks = DB::table('channel_tasks as t')
            ->select('t.*', 'c.name as CHANNEL_NAME', 'a.name as AUTHOR_NAME')
            ->join('users as c', 't.channel_id', '=', 'c.id')
            ->join('users as a', 't.author_id', '=', 'a.id')
            ->orderByDesc('t.id')
            ->get();

        // Universal Directory Fetch
        $allUsers = DB::select("SELECT ID, NAME, EMAIL, ROLE, PASSWORD FROM USERS ORDER BY NAME ASC");
        
        $authors = [];
        $channels = [];
        $readers = [];

        foreach ($allUsers as $u) {
            $r = strtolower($u->role ?? $u->ROLE);
            if ($r === 'author' || $r === 'both') $authors[] = $u;
            if ($r === 'channel') $channels[] = $u;
            if ($r === 'reader' || $r === 'both') $readers[] = $u;
        }

        // Fetch roster relationships
        $rosters = DB::select("
            SELECT ca.CHANNEL_ID, c.NAME as CHANNEL_NAME, ca.AUTHOR_ID, a.NAME as AUTHOR_NAME, a.EMAIL as AUTHOR_EMAIL
            FROM CHANNEL_AUTHORS ca
            JOIN USERS a ON ca.AUTHOR_ID = a.ID
            JOIN USERS c ON ca.CHANNEL_ID = c.ID
        ");

        $channelRosters = [];
        $authorRosters = [];
        foreach ($rosters as $r) {
            $cid = $r->channel_id ?? $r->CHANNEL_ID;
            $aid = $r->author_id ?? $r->AUTHOR_ID;
            
            if (!isset($channelRosters[$cid])) {
                $channelRosters[$cid] = [];
            }
            $channelRosters[$cid][] = $r;
            
            if (!isset($authorRosters[$aid])) {
                $authorRosters[$aid] = [];
            }
            $authorRosters[$aid][] = $r;
        }

        return view('admin.dashboard', [
            'totalArticles' => $totalArticles,
            'totalUsers' => $totalUsers,
            'totalComments' => $totalComments,
            'newsList' => $newsList,
            'auditLogs' => $auditLogs,
            'authors' => $authors,
            'channels' => $channels,
            'readers' => $readers,
            'authorRosters' => $authorRosters,
            'channelRosters' => $channelRosters,
            'globalTasks' => $globalTasks
        ]);
    }

    // PHP implementation for publishing news
    public function publishNews($id)
    {
        DB::transaction(function () use ($id) {
            DB::table('news_items')
                ->where('id', $id)
                ->update(['status' => 'Published', 'date' => now(), 'admin_feedback' => null]);
        });

        return redirect()->back()->with('success', 'Dispatch successfully published!');
    }

    // Author Dashboard Logic
    public function authorDashboard()
    {
        $authorName = Auth::user()->name;

        // Fetch news written by this author
        $authorNewsRows = DB::select(
            "
SELECT *
FROM NEWS_ITEMS
WHERE LOWER(AUTHOR_NAME)=?
ORDER BY ID DESC
",
            [
                strtolower($authorName)
            ]
        );

        $newsList = [];
        foreach ($authorNewsRows as $row) {
            $item = $this->mapNewsItem($row);
            $item['admin_feedback'] = $row->admin_feedback ?? $row->ADMIN_FEEDBACK ?? null;
            $newsList[] = $item;
        }

        // Fetch inbox messages
        $inboxMessages = DB::select("SELECT * FROM INBOX_MESSAGES WHERE USER_ID = ? ORDER BY ID DESC", [Auth::id()]);

        // Fetch unread count to show the highlights
        $unreadCountResult = DB::selectOne("SELECT COUNT(*) AS UNREAD FROM INBOX_MESSAGES WHERE USER_ID = ? AND (IS_READ = 0 OR IS_READ IS NULL)", [Auth::id()]);
        $unreadCount = $unreadCountResult->unread ?? $unreadCountResult->UNREAD ?? 0;

        // Auto-mark all messages as read (Instagram style)
        DB::table('inbox_messages')->where('user_id', Auth::id())->update(['is_read' => 1]);

        // Fetch Tasks assigned to this author
        $myTasks = DB::select("
            SELECT t.*, c.NAME as CHANNEL_NAME 
            FROM CHANNEL_TASKS t
            JOIN USERS c ON t.CHANNEL_ID = c.ID
            WHERE t.AUTHOR_ID = ?
            ORDER BY t.ID DESC
        ", [Auth::id()]);

        // Fetch Channels for the author to select from when composing
        $channels = DB::select("
            SELECT u.ID, u.NAME, u.EMAIL
            FROM USERS u 
            JOIN CHANNEL_AUTHORS ca ON u.ID = ca.CHANNEL_ID 
            WHERE ca.AUTHOR_ID = ?
        ", [Auth::id()]);

        return view('author.dashboard', [
            'newsList' => $newsList,
            'inboxMessages' => $inboxMessages,
            'unreadCount' => $unreadCount,
            'myTasks' => $myTasks,
            'channels' => $channels
        ]);
    }

    public function markInboxRead()
    {
        DB::statement("UPDATE INBOX_MESSAGES SET IS_READ = 1 WHERE USER_ID = ?", [Auth::id()]);
        return redirect()->back();
    }

    public function markTaskCompleted($id)
    {
        DB::statement("UPDATE CHANNEL_TASKS SET STATUS = 'Submitted' WHERE ID = ? AND AUTHOR_ID = ?", [$id, Auth::id()]);
        return redirect()->back()->with('success', 'Task marked as Submitted!');
    }

    public function editNews($id)
    {
        $newsRow = DB::select("SELECT * FROM NEWS_ITEMS WHERE ID = ?", [$id]);
        $news = $newsRow[0] ?? null;
        if (!$news || strtolower($news->author_name ?? $news->AUTHOR_NAME) !== strtolower(Auth::user()->name)) {
            return redirect('/home')->with('error', 'Unauthorized access.');
        }

        $channels = DB::select("
            SELECT u.ID, u.NAME 
            FROM USERS u 
            JOIN CHANNEL_AUTHORS ca ON u.ID = ca.CHANNEL_ID 
            WHERE ca.AUTHOR_ID = ?
        ", [Auth::id()]);
        
        // Handle CLOB content securely
        $content = '';
        $description = $news->news_description ?? $news->NEWS_DESCRIPTION ?? null;
        if ($description) {
            if (is_resource($description)) {
                $content = stream_get_contents($description);
            } else {
                $content = (string) $description;
            }
        }

        $newsItem = [
            'id' => $news->id ?? $news->ID,
            'title' => $news->news_title ?? $news->NEWS_TITLE,
            'content' => $content,
            'category' => $news->category ?? $news->CATEGORY,
            'target_channel' => $news->target_channel ?? $news->TARGET_CHANNEL
        ];

        return view('news.edit', ['news' => $newsItem, 'channels' => $channels]);
    }

    public function updateNews(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:50',
            'target_channel' => 'required|integer'
        ]);

        DB::statement(
            "UPDATE NEWS_ITEMS SET NEWS_TITLE = ?, NEWS_DESCRIPTION = ?, CATEGORY = ?, TARGET_CHANNEL = ?, STATUS = 'Pending_Channel', ADMIN_FEEDBACK = NULL WHERE ID = ?",
            [
                $request->input('title'),
                $request->input('content'),
                $request->input('category'),
                $request->input('target_channel'),
                $id
            ]
        );

        $this->logAudit('Author Update', "Author " . Auth::user()->name . " updated article '{$request->title}'.");

        return redirect('/author/dashboard')->with('success', 'Dispatch updated and resubmitted for channel review.');
    }

    // Channel Dashboard Logic
    public function channelDashboard()
    {
        $channelId = Auth::id();

        $pendingNews = DB::select(
            "SELECT * FROM NEWS_ITEMS WHERE TARGET_CHANNEL = ? AND STATUS = 'Pending_Channel' ORDER BY ID DESC",
            [$channelId]
        );

        $newsList = [];
        foreach ($pendingNews as $row) {
            $newsList[] = $this->mapNewsItem($row);
        }

        // Fetch roster
        $roster = DB::select("
            SELECT u.ID, u.NAME, u.EMAIL 
            FROM USERS u 
            JOIN CHANNEL_AUTHORS ca ON u.ID = ca.AUTHOR_ID 
            WHERE ca.CHANNEL_ID = ?
        ", [$channelId]);

        // Fetch assigned tasks by this channel
        $assignedTasks = DB::select("
            SELECT t.*, a.NAME as AUTHOR_NAME 
            FROM CHANNEL_TASKS t
            JOIN USERS a ON t.AUTHOR_ID = a.ID
            WHERE t.CHANNEL_ID = ?
            ORDER BY t.ID DESC
        ", [$channelId]);

        return view('channel.dashboard', ['newsList' => $newsList, 'roster' => $roster, 'assignedTasks' => $assignedTasks]);
    }

    public function assignTask(Request $request)
    {
        $request->validate([
            'author_id' => 'required|integer',
            'topic' => 'required|string|max:255',
            'resources' => 'nullable|string',
            'deadline' => 'required|date'
        ]);

        $channelName = Auth::user()->name;
        
        $msg = "<strong>📢 TASK ASSIGNMENT from {$channelName}</strong><br>";
        $msg .= "<strong>Topic:</strong> " . htmlspecialchars($request->input('topic')) . "<br>";
        $msg .= "<strong>Deadline:</strong> " . htmlspecialchars($request->input('deadline')) . "<br>";
        if ($request->filled('resources')) {
            $msg .= "<strong>Resources:</strong> " . nl2br(htmlspecialchars($request->input('resources')));
        }

        DB::table('inbox_messages')->insert([
            'user_id' => $request->input('author_id'),
            'message' => $msg,
            'is_read' => 0
        ]);

        DB::table('channel_tasks')->insert([
            'channel_id' => Auth::id(),
            'author_id' => $request->input('author_id'),
            'topic' => $request->input('topic'),
            'resources' => $request->input('resources'),
            'deadline' => $request->input('deadline'),
            'status' => 'Pending'
        ]);

        $this->logAudit('Task Assigned', "Channel " . Auth::user()->name . " assigned a task to Author ID " . $request->input('author_id'));

        return redirect()->back()->with('success', 'Task successfully assigned to the author!');
    }

    public function addAuthorToChannel(Request $request)
    {
        $request->validate(['author_email' => 'required|email']);
        $channelId = Auth::id();

        // Check if author exists
        $authorRow = DB::select("SELECT ID, ROLE FROM USERS WHERE LOWER(EMAIL) = ?", [strtolower($request->author_email)]);
        $author = $authorRow[0] ?? null;

        if (!$author || (strtolower($author->role ?? $author->ROLE) !== 'author' && strtolower($author->role ?? $author->ROLE) !== 'both')) {
            return redirect()->back()->with('error', 'No author with this email ID exists.');
        }

        $authorId = $author->id ?? $author->ID;

        // Check if already in roster
        $existing = DB::select("SELECT * FROM CHANNEL_AUTHORS WHERE CHANNEL_ID = ? AND AUTHOR_ID = ?", [$channelId, $authorId]);
        if (!empty($existing)) {
            return redirect()->back()->with('error', 'Author is already in your roster.');
        }

        DB::statement("INSERT INTO CHANNEL_AUTHORS (CHANNEL_ID, AUTHOR_ID) VALUES (?, ?)", [$channelId, $authorId]);

        return redirect()->back()->with('success', 'Author successfully added to your channel roster.');
    }

    public function channelReview(Request $request, $id)
    {
        $action = $request->input('action'); // 'approve' or 'reject'
        $feedback = $request->input('feedback');

        $newsRow = DB::select("SELECT AUTHOR_NAME, NEWS_TITLE FROM NEWS_ITEMS WHERE ID = ?", [$id]);
        $news = $newsRow[0] ?? null;
        if ($news) {
            $authorName = $news->author_name ?? $news->AUTHOR_NAME;
            $title = $news->news_title ?? $news->NEWS_TITLE;
            $userRow = DB::select("SELECT ID FROM USERS WHERE LOWER(NAME) = ?", [strtolower($authorName)]);
            $userId = $userRow[0]->id ?? $userRow[0]->ID ?? null;
        } else {
            $userId = null;
            $title = 'Unknown Article';
        }

        if ($action === 'approve') {
            DB::statement("UPDATE NEWS_ITEMS SET STATUS = 'Pending_Admin', ADMIN_FEEDBACK = NULL WHERE ID = ?", [$id]);
            if ($userId) {
                $msg = "Your dispatch '{$title}' was approved by the channel and forwarded to the Super Admin.";
                DB::table('inbox_messages')->insert(['user_id' => $userId, 'message' => $msg]);
            }
            $this->logAudit('Channel Approval', "Channel approved article ID {$id}");
            return redirect()->back()->with('success', 'Article forwarded to Super Admin for final review.');
        } elseif ($action === 'modify') {
            DB::statement("UPDATE NEWS_ITEMS SET STATUS = 'Modification_Required', ADMIN_FEEDBACK = ? WHERE ID = ?", [$feedback, $id]);
            if ($userId) {
                $msg = "Modification Request for '{$title}': {$feedback}";
                DB::table('inbox_messages')->insert(['user_id' => $userId, 'message' => $msg]);
            }
            $this->logAudit('Channel Modify Request', "Channel requested modification for article ID {$id}");
            return redirect()->back()->with('success', 'Modification request sent to the author.');
        } else {
            // Permanent Reject
            DB::statement("UPDATE NEWS_ITEMS SET STATUS = 'Rejected_Permanent', ADMIN_FEEDBACK = 'Permanently Rejected' WHERE ID = ?", [$id]);
            if ($userId) {
                $msg = "Your dispatch '{$title}' was permanently rejected by the channel.";
                DB::table('inbox_messages')->insert(['user_id' => $userId, 'message' => $msg]);
            }
            $this->logAudit('Channel Rejection', "Channel rejected article ID {$id}");
            return redirect()->back()->with('error', 'Article permanently rejected.');
        }
    }

    public function adminReview(Request $request, $id)
    {
        $action = $request->input('action'); // 'approve' or 'reject'
        $feedback = $request->input('feedback');
        
        if ($action === 'approve') {
            DB::table('news_items')->where('id', $id)->update([
                'status' => 'Published',
                'date' => now(),
                'admin_feedback' => null
            ]);
            $this->logAudit('Admin Publish', "Super Admin published article ID {$id}");
            return redirect()->back()->with('success', 'Article published successfully to the news feed.');
        } else {
            // Use the PHP logic to reject the news instead of PL/SQL Stored Procedure
            DB::table('news_items')->where('id', $id)->update([
                'status' => 'Rejected_Permanent',
                'admin_feedback' => $feedback
            ]);
            $this->logAudit('Admin Reject', "Super Admin rejected article ID {$id}");
            return redirect()->back()->with('error', 'Article rejected.');
        }
    }

    // Admin action to delete an article
    public function deleteNews($id)
    {
        DB::statement(
            "DELETE FROM NEWS_ITEMS WHERE ID=?",
            [$id]
        );
        return redirect()->back()->with('success', 'Article deleted from archives.');
    }
}

