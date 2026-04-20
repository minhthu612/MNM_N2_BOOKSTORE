<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class BookSetController extends Controller
{
    // ================= INDEX =================
    public function index(Request $request)
    {
        $search = '';
        if ($request->has('search')) {
            $search = $request->search;
        }


        $status = '';
        if ($request->has('status')) {
            $status = $request->status;
        }


        $where = " WHERE 1=1 ";
        if ($search != '') {
            // Nếu người dùng nhập số, mình ưu tiên tìm đúng cái ID đó luôn
            if (is_numeric($search)) {
                $where .= " AND (bs.set_id = '$search' OR bs.name LIKE '%$search%') ";
            } else {
                $where .= " AND (bs.name LIKE '%$search%' OR bs.description LIKE '%$search%') ";
            }
        }

        if ($status != '') {
            if ($status == 'active') {
                $where .= " AND (SELECT COUNT(*) FROM book_set_items WHERE set_id = bs.set_id) > 0 ";
            } else {
                if ($status == 'empty') {
                    $where .= " AND (SELECT COUNT(*) FROM book_set_items WHERE set_id = bs.set_id) = 0 ";
                }
            }
        }


        $page = 1;
        if ($request->has('page')) {
            $page = (int)$request->page;
        }
        if ($page < 1) $page = 1;


        $limit = 10;
        $offset = ($page - 1) * $limit;


        $count = DB::select("SELECT COUNT(*) as total FROM book_sets bs $where");
        $total_sets = $count[0]->total;


        $total_pages = ceil($total_sets / $limit);


        $start_page = max(1, $page - 2);
        $end_page = min($total_pages, $page + 2);


        $sets_list = DB::select("
            SELECT bs.*,
            (SELECT COUNT(*) FROM book_set_items WHERE set_id = bs.set_id) as book_count,
            (SELECT SUM(b.price * bsi.quantity) FROM book_set_items bsi
             JOIN books b ON bsi.book_id = b.book_id WHERE bsi.set_id = bs.set_id) as total_price
            FROM book_sets bs
            $where
            ORDER BY bs.created_at DESC
            LIMIT $limit OFFSET $offset
        ");


        return view('admin.book_sets.index', compact(
            'sets_list','search','status','page','total_pages','total_sets', 'start_page','end_page'
        ));
    }


    // ================= CREATE =================
    public function create()
    {
        $books_list = DB::select("SELECT book_id, title, author, price, link_images FROM books ORDER BY title");
        return view('admin.book_sets.create', compact('books_list'));
    }


    public function store(Request $request)
    {
        $error = '';
        $image_name = '';


        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = strtolower($file->getClientOriginalExtension());
            $allowed = ['jpg','jpeg','png','gif','webp'];


            if (in_array($ext, $allowed)) {
                $name = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads/book_sets'), $name);
                $image_name = 'uploads/book_sets/'.$name;
            } else {
                $error = "Định dạng ảnh không hợp lệ.";
            }
        }


        if ($error == '') {
            DB::insert("
                INSERT INTO book_sets (name, description, images, link_images, price, discount, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ", [
                $request->name,
                $request->description,
                $image_name,
                $request->link_images,
                $request->price,
                $request->discount
            ]);


            $set_id = DB::getPdo()->lastInsertId();


            if ($request->has('book_ids')) {
                foreach ($request->book_ids as $book_id) {
                    $qty = 1;
                    if ($request->has('quantity_'.$book_id)) {
                        $qty = $request->input('quantity_'.$book_id);
                    }


                    DB::insert("
                        INSERT INTO book_set_items (set_id, book_id, quantity)
                        VALUES (?, ?, ?)
                    ", [$set_id,$book_id,$qty]);
                }
            }


            return redirect()->route('admin.book_sets.index')
                ->with('success','Đã thêm bộ sách!');
        }


        return back()->with('error',$error);
    }


    // ================= EDIT =================
    public function edit($id)
    {
        $set_id = $id;


        $book_set = DB::select("SELECT * FROM book_sets WHERE set_id = '$set_id'");
        if (!$book_set) return redirect()->route('admin.book_sets.index');


        $book_set = $book_set[0];


        $items_in_set = DB::select("
            SELECT bsi.*, b.title, b.author, b.price, b.link_images
            FROM book_set_items bsi
            JOIN books b ON bsi.book_id = b.book_id
            WHERE bsi.set_id = '$set_id'
        ");


        $books_to_add = DB::select("
            SELECT * FROM books WHERE book_id NOT IN
            (SELECT book_id FROM book_set_items WHERE set_id = '$set_id')
        ");


        return view('admin.book_sets.edit', compact(
            'book_set','items_in_set','books_to_add'
        ));
    }


    public function update(Request $request, $id)
    {
        $set_id = $id;

        // 1. Lấy thông tin ảnh cũ
        $book_set = DB::select("SELECT * FROM book_sets WHERE set_id = '$set_id'")[0];
        $image_name = $book_set->images;

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $name = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/book_sets'), $name);
            $image_name = 'uploads/book_sets/'.$name;
        }

        // 2. Cập nhật thông tin chính của bộ sách
        DB::update("
            UPDATE book_sets SET
            name=?, description=?, images=?, link_images=?, price=?, discount=?
            WHERE set_id=?
        ",[
            $request->name,
            $request->description,
            $image_name,
            $request->link_images,
            $request->price,
            $request->discount,
            $set_id
        ]);

        // 3. XỬ LÝ XÓA SÁCH (Phần 1 trong View)
        if ($request->has('remove_items')) {
            foreach ($request->remove_items as $book_id) {
                DB::delete("DELETE FROM book_set_items WHERE set_id = ? AND book_id = ?", [$set_id, $book_id]);
            }
        }

        // 4. CẬP NHẬT SỐ LƯỢNG SÁCH ĐANG CÓ (Nếu người dùng chỉnh ô input số lượng)
        // Lấy tất cả các input có dạng quantity_ID
        foreach ($request->all() as $key => $value) {
            if (str_contains($key, 'quantity_') && !str_contains($key, 'new_quantity_')) {
                $book_id = str_replace('quantity_', '', $key);
                DB::update("UPDATE book_set_items SET quantity = ? WHERE set_id = ? AND book_id = ?", [$value, $set_id, $book_id]);
            }
        }

        // 5. XỬ LÝ THÊM SÁCH MỚI (Phần 2 trong View)
        if ($request->has('new_book_ids')) {
            foreach ($request->new_book_ids as $book_id) {
                $qty = $request->input('new_quantity_'.$book_id, 1);
                
                // Dùng insert để thêm vào bộ sách
                DB::insert("
                    INSERT INTO book_set_items (set_id, book_id, quantity)
                    VALUES (?, ?, ?)
                ", [$set_id, $book_id, $qty]);
            }
        }

        return redirect()->back()->with('success', 'Đã cập nhật toàn bộ thay đổi!');
    }


    // ================= DELETE =================
    public function delete($id)
    {
        $set_id = $id;


        $book_set = DB::select("SELECT * FROM book_sets WHERE set_id = '$set_id'");
        if (!$book_set) return redirect()->route('admin.book_sets.index');


        $book_set = $book_set[0];


        $count = DB::select("SELECT COUNT(*) as total FROM book_set_items WHERE set_id='$set_id'");
        $book_count = $count[0]->total;


        return view('admin.book_sets.delete', compact('book_set','book_count'));
    }


    public function destroy($id)
    {
        $set_id = $id;


        DB::delete("DELETE FROM book_set_items WHERE set_id='$set_id'");
        DB::delete("DELETE FROM book_sets WHERE set_id='$set_id'");


        return redirect()->route('admin.book_sets.index')
            ->with('success','Đã xóa!');
    }


    // ================= ITEMS =================
public function items($id)
{
    $set_id = (int)$id;


    // Lấy thông tin bộ sách
    $book_set = DB::select("SELECT * FROM book_sets WHERE set_id = '$set_id'");
    if (!$book_set) {
        return redirect()->route('admin.book_sets.index');
    }
    $book_set = $book_set[0];


    // Lấy sách trong bộ
    $items = DB::select("
        SELECT bsi.*, b.title, b.author, b.price, b.link_images
        FROM book_set_items bsi
        JOIN books b ON bsi.book_id = b.book_id
        WHERE bsi.set_id = '$set_id'
        ORDER BY b.title
    ");


    // Lấy ID sách đã có
    $excluded = array();
    foreach ($items as $it) {
        $excluded[] = $it->book_id;
    }


    // Lấy sách chưa có trong bộ
    if (count($excluded) > 0) {
        $ids = implode(',', $excluded);
        $all_books = DB::select("SELECT * FROM books WHERE book_id NOT IN ($ids) ORDER BY title");
    } else {
        $all_books = DB::select("SELECT * FROM books ORDER BY title");
    }


    // Tính tổng
    $total_price = 0;
    $total_books = 0;


    foreach ($items as $it) {
        $total_price += $it->price * $it->quantity;
        $total_books += $it->quantity;
    }


    return view('admin.book_sets.items', compact(
        'book_set','items','all_books','total_price','total_books','set_id'
    ));
}


    public function itemsAction(Request $request, $id)
{
    $set_id = (int)$id;


    // ================= ADD BOOKS =================
    if ($request->has('book_ids')) {


        $book_ids = $request->book_ids;


        foreach ($book_ids as $book_id) {


            $book_id = (int)$book_id;


            $quantity = 1;
            if ($request->has('quantity_'.$book_id)) {
                $quantity = (int)$request->input('quantity_'.$book_id);
            }


            // Check tồn tại
            $check = DB::select("
                SELECT * FROM book_set_items
                WHERE set_id = '$set_id' AND book_id = '$book_id'
            ");


            if (count($check) == 0) {
                DB::insert("
                    INSERT INTO book_set_items (set_id, book_id, quantity)
                    VALUES ('$set_id','$book_id','$quantity')
                ");
            }
        }


        return redirect()->back()->with('success','Đã thêm sách vào bộ!');
    }


    // ================= REMOVE BOOK =================
    if ($request->has('remove_item')) {


        $book_id = (int)$request->remove_item;


        DB::delete("
            DELETE FROM book_set_items
            WHERE set_id = '$set_id' AND book_id = '$book_id'
        ");


        return redirect()->back()->with('success','Đã xóa sách!');
    }


    return redirect()->back();
}
}
