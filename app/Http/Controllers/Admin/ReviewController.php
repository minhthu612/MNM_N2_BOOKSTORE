<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ReviewController extends Controller
{
    // ================= INDEX =================
    public function index(Request $request)
    {
        $search = $request->search ?? '';
        $rating_filter = $request->rating ?? '';
        $date_from = $request->date_from ?? '';
        $date_to = $request->date_to ?? '';


        $query = DB::table('reviews as r')
            ->leftJoin('books as b', 'r.book_id', '=', 'b.book_id')
            ->leftJoin('users as u', 'r.user_id', '=', 'u.user_id')
            ->select(
                'r.*',
                'b.title as book_title',
                'b.author',
                'b.link_images',
                'u.username',
                'u.fullname',
                'u.email'
            );


        if ($search != '') {
            $query->where(function ($q) use ($search) {
                $q->where('r.comment', 'like', "%$search%")
                  ->orWhere('b.title', 'like', "%$search%")
                  ->orWhere('u.username', 'like', "%$search%");
            });
        }


        if ($rating_filter != '') $query->where('r.rating', $rating_filter);
        if ($date_from != '') $query->whereDate('r.created_at', '>=', $date_from);
        if ($date_to != '') $query->whereDate('r.created_at', '<=', $date_to);


        $reviews_list = $query->orderBy('r.created_at', 'desc')->paginate(10);


        // stats
        $stats = DB::table('reviews')->selectRaw("
            COUNT(*) as total_reviews,
            AVG(rating) as avg_rating,
            SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END) as positive,
            SUM(CASE WHEN rating <= 2 THEN 1 ELSE 0 END) as negative
        ")->first();


        return view('admin.reviews.index', compact(
            'reviews_list','stats','search','rating_filter','date_from','date_to'
        ));
    }


    // ================= EDIT =================
    public function edit($id)
    {
        $review = DB::table('reviews as r')
            ->leftJoin('books as b', 'r.book_id','=','b.book_id')
            ->leftJoin('users as u','r.user_id','=','u.user_id')
            ->select('r.*','b.title as book_title','u.username','u.fullname','u.email')
            ->where('r.review_id',$id)
            ->first();


        if (!$review) {
            return redirect()->route('admin.reviews.index')->with('error','Không tồn tại');
        }


        return view('admin.reviews.edit', compact('review'));
    }


    public function update(Request $request, $id)
    {
        $rating = (int)$request->rating;
        $comment = $request->comment;


        if ($rating < 1 || $rating > 5) {
            return back()->with('error','Sai số sao');
        }


        if ($comment == '') {
            return back()->with('error','Không được để trống');
        }


        DB::table('reviews')->where('review_id',$id)->update([
            'rating'=>$rating,
            'comment'=>$comment
        ]);


        return redirect()->route('admin.reviews.index')->with('success','Đã cập nhật');
    }


    // ================= DELETE =================
    public function delete($id)
    {
        $review = DB::table('reviews as r')
            ->leftJoin('books as b', 'r.book_id','=','b.book_id')
            ->leftJoin('users as u','r.user_id','=','u.user_id')
            ->select('r.*','b.title as book_title','u.username','u.fullname')
            ->where('r.review_id',$id)
            ->first();


        if (!$review) {
            return redirect()->route('admin.reviews.index')->with('error','Không tồn tại');
        }


        return view('admin.reviews.delete', compact('review'));
    }


    public function destroy(Request $request, $id)
    {
        if ($request->confirm_text != 'DELETE') {
            return back()->with('error','Sai xác nhận');
        }


        DB::table('reviews')->where('review_id',$id)->delete();


        return redirect()->route('admin.reviews.index')->with('success','Đã xóa');
    }
}
