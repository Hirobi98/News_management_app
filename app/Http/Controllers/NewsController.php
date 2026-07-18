<?php

namespace App\Http\Controllers;

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
            $dateStr = date('F j, Y', strtotime($date));
        }

        return [
            'id' => $row->id ?? $row->ID,
            'title' => $row->news_title ?? $row->NEWS_TITLE,
            'content' => $content,
            'author' => $row->author_name ?? $row->AUTHOR_NAME,
            'channel' => $row->category ?? $row->CATEGORY,
            'target_channel_name' => $row->target_channel_name ?? $row->TARGET_CHANNEL_NAME ?? 'Unknown',
            'date' => $dateStr,
            'image' => $row->image ?? $row->IMAGE ?? '',
            'status' => $row->status ?? $row->STATUS ?? 'Draft',
            'comments' => []
        ];
    }

    public function index()
    {
        if (!session('user_logged_in')) {
            return redirect('/login')->with('error', 'Please login to view news.');
        }

        // Query published news from Oracle table using uppercase table name
        $newsRows = DB::select("
    SELECT *
    FROM NEWS_ITEMS
    WHERE LOWER(STATUS)='published'
    ORDER BY ID DESC
");

        $newsList = [];
        foreach ($newsRows as $row) {
            $newsList[] = $this->mapNewsItem($row);
        }

        $userId = session('user_id');
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
        if (!session('user_logged_in')) {
            return redirect('/login')->with('error', 'Please login to view news.');
        }

        $newsRows = DB::select("
            SELECT n.*, u.NAME as TARGET_CHANNEL_NAME
            FROM NEWS_ITEMS n
            LEFT JOIN USERS u ON n.TARGET_CHANNEL = u.ID
            WHERE LOWER(n.STATUS)='published' AND LOWER(n.CATEGORY)=?
            ORDER BY n.ID DESC
        ", [strtolower($category)]);

        $newsList = [];
        foreach ($newsRows as $row) {
            $newsList[] = $this->mapNewsItem($row);
        }

        $userId = session('user_id');
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
        if (!session('user_logged_in')) {
            return redirect('/login')->with('error', 'Please login to view news.');
        }

        // Fetch news article by ID
        $newsRow = DB::select(
            "SELECT * FROM NEWS_ITEMS WHERE ID = ?",
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
        $role = strtolower(session('user_role'));
        if (!session('user_logged_in') || $role === 'reader') {
            return redirect('/home')->with('error', 'You do not have permission to write news.');
        }

        return view('news.create');
    }

    public function store(Request $request)
    {
        $role = strtolower(session('user_role'));
        if (!session('user_logged_in') || $role === 'reader') {
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

        DB::statement(
            "
INSERT INTO NEWS_ITEMS
(
NEWS_TITLE,
AUTHOR_NAME,
NEWS_DESCRIPTION,
CATEGORY,
STATUS,
\"date\",
IMAGE,
TARGET_CHANNEL
)
VALUES
(
?,
?,
?,
?,
?,
SYSDATE,
?,
?
)
",
            [
                $request->input('title'),
                session('user_name', 'Anonymous Writer'),
                $request->input('content'),
                $request->input('category'),
                'Pending_Channel',
                $imagePath,
                $request->input('target_channel')
            ]
        );

        return redirect('/home')->with('success', "Your dispatch '{$request->title}' was successfully sent to the news channel for review!");
    }

    public function storeComment(Request $request, $id)
    {
        if (!session('user_logged_in')) {
            return redirect('/login');
        }

        $request->validate([
            'comment' => 'required|string|max:4000'
        ]);

        // Insert comment. ID is generated by trigger COMMENTS_BIR.
        DB::statement(
            "
INSERT INTO COMMENTS
(
ARTICLE_ID,
USER_NAME,
COMMENT_TEXT,
STATUS,
\"date\"
)
VALUES
(
?,
?,
?,
?,
SYSDATE
)
",
            [
                $id,
                session('user_name', 'Anonymous Reader'),
                $request->input('comment'),
                'Approved'
            ]
        );

        return redirect()->back()->with('success', 'Your letter was added successfully!');
    }

    // Admin Dashboard Logic
    public function adminDashboard()
    {
        $totalArticlesResult = DB::selectOne("SELECT GET_TOTAL_PUBLISHED_NEWS() TOTAL FROM DUAL");
        $totalArticles = $totalArticlesResult->total ?? $totalArticlesResult->TOTAL ?? 0;

        $totalUsers = DB::selectOne("SELECT COUNT(*) TOTAL FROM USERS")->total;

        $totalComments = DB::selectOne("SELECT COUNT(*) TOTAL FROM COMMENTS")->total;

        // Retrieve all articles for admin (especially Pending_Admin)
        $allNewsRows = DB::select("
SELECT n.*, u.NAME as TARGET_CHANNEL_NAME
FROM NEWS_ITEMS n
LEFT JOIN USERS u ON n.TARGET_CHANNEL = u.ID
ORDER BY n.ID DESC
");

        $newsList = [];
        foreach ($allNewsRows as $row) {
            $item = $this->mapNewsItem($row);
            $item['admin_feedback'] = $row->admin_feedback ?? $row->ADMIN_FEEDBACK ?? null;
            $newsList[] = $item;
        }

        // Retrieve audit logs populated by the NEWS_PUBLISH_TRG database trigger
        $auditLogs = DB::select("
        SELECT * FROM (
            SELECT *
            FROM AUDIT_LOGS
            ORDER BY CREATED_AT DESC
        ) WHERE ROWNUM <= 10
        ");

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
            'channelRosters' => $channelRosters,
            'authorRosters' => $authorRosters
        ]);
    }

    // Stored Procedure and Transaction execution for publishing news
    public function publishNews($id)
    {
        DB::transaction(function () use ($id) {
            // Execute the Oracle stored procedure publish_news_proc
            DB::statement("BEGIN publish_news_proc(?); END;", [$id]);
        });

        return redirect()->back()->with('success', 'Dispatch successfully published via Oracle Stored Procedure!');
    }

    // Author Dashboard Logic
    public function authorDashboard()
    {
        $authorName = session('user_name');

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
        $inboxMessages = DB::select("SELECT * FROM INBOX_MESSAGES WHERE USER_ID = ? ORDER BY ID DESC", [session('user_id')]);

        return view('author.dashboard', [
            'newsList' => $newsList,
            'inboxMessages' => $inboxMessages
        ]);
    }

    public function editNews($id)
    {
        $newsRow = DB::select("SELECT * FROM NEWS_ITEMS WHERE ID = ?", [$id]);
        $news = $newsRow[0] ?? null;
        if (!$news || strtolower($news->author_name ?? $news->AUTHOR_NAME) !== strtolower(session('user_name'))) {
            return redirect('/home')->with('error', 'Unauthorized access.');
        }

        $channels = DB::select("
            SELECT u.ID, u.NAME 
            FROM USERS u 
            JOIN CHANNEL_AUTHORS ca ON u.ID = ca.CHANNEL_ID 
            WHERE ca.AUTHOR_ID = ?
        ", [session('user_id')]);
        
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

        return redirect('/author/dashboard')->with('success', 'Dispatch updated and resubmitted for channel review.');
    }

    // Channel Dashboard Logic
    public function channelDashboard()
    {
        $channelId = session('user_id');

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
            SELECT u.NAME, u.EMAIL 
            FROM USERS u 
            JOIN CHANNEL_AUTHORS ca ON u.ID = ca.AUTHOR_ID 
            WHERE ca.CHANNEL_ID = ?
        ", [$channelId]);

        return view('channel.dashboard', ['newsList' => $newsList, 'roster' => $roster]);
    }

    public function addAuthorToChannel(Request $request)
    {
        $request->validate(['author_email' => 'required|email']);
        $channelId = session('user_id');

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
                DB::statement("INSERT INTO INBOX_MESSAGES (USER_ID, MESSAGE) VALUES (?, ?)", [$userId, $msg]);
            }
            return redirect()->back()->with('success', 'Article forwarded to Super Admin for final review.');
        } elseif ($action === 'modify') {
            DB::statement("UPDATE NEWS_ITEMS SET STATUS = 'Modification_Required', ADMIN_FEEDBACK = ? WHERE ID = ?", [$feedback, $id]);
            if ($userId) {
                $msg = "Modification Request for '{$title}': {$feedback}";
                DB::statement("INSERT INTO INBOX_MESSAGES (USER_ID, MESSAGE) VALUES (?, ?)", [$userId, $msg]);
            }
            return redirect()->back()->with('success', 'Modification request sent to the author.');
        } else {
            // Permanent Reject
            DB::statement("UPDATE NEWS_ITEMS SET STATUS = 'Rejected_Permanent', ADMIN_FEEDBACK = 'Permanently Rejected' WHERE ID = ?", [$id]);
            if ($userId) {
                $msg = "Your dispatch '{$title}' was permanently rejected by the channel.";
                DB::statement("INSERT INTO INBOX_MESSAGES (USER_ID, MESSAGE) VALUES (?, ?)", [$userId, $msg]);
            }
            return redirect()->back()->with('error', 'Article permanently rejected.');
        }
    }

    public function adminReview(Request $request, $id)
    {
        $action = $request->input('action'); // 'approve' or 'reject'
        $feedback = $request->input('feedback');
        
        if ($action === 'approve') {
            DB::statement("UPDATE NEWS_ITEMS SET STATUS = 'Published', ADMIN_FEEDBACK = NULL WHERE ID = ?", [$id]);
            return redirect()->back()->with('success', 'Article published successfully to the news feed.');
        } else {
            // Use the PL/SQL Stored Procedure to reject the news
            DB::statement("BEGIN REJECT_NEWS_PROC(:id, :feedback); END;", [
                'id' => $id,
                'feedback' => $feedback
            ]);
            return redirect()->back()->with('error', 'Article rejected via PL/SQL Stored Procedure.');
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
