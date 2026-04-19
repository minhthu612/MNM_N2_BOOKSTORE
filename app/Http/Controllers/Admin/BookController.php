<?php
namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class BookController extends Controller
{
    /* ================= INDEX ================= */
    public function index(Request $request)
    {
        $search = '';
        if ($request->has('search')) {
            $search = $request->search;
        }


        $category_id = '';
        if ($request->has('category_id')) {
            $category_id = $request->category_id;
        }


        $status = '';
        if ($request->has('status')) {
            $status = $request->status;
        }


        $where = " WHERE 1=1 ";


        if ($search != '') {
            $where .= " AND (b.title LIKE '%$search%' OR b.author LIKE '%$search%') ";
        }


        if ($category_id != '') {
            $where .= " AND b.category_id = '$category_id' ";
        }


        if ($status != '') {
            if ($status == 'active') {
                $where .= " AND i.stock > 0 ";
            } else {
                if ($status == 'out_of_stock') {
                    $where .= " AND (i.stock <= 0 OR i.stock IS NULL) ";
                }
            }
        }


        $page = 1;
        if ($request->has('page')) {
            $page = (int)$request->page;
        }
        if ($page < 1) { $page = 1; }


        $limit = 10;
        $offset = ($page - 1) * $limit;


        $sql_count = "SELECT COUNT(*) as total FROM books b LEFT JOIN inventory i ON b.book_id = i.book_id $where";
        $total_books = DB::select($sql_count)[0]->total;
        $total_pages = ceil($total_books / $limit);


        $start_page = max(1, $page - 2);
        $end_page = min($total_pages, $page + 2);


        $sql_main = "
            SELECT b.*, c.category_name, i.stock
            FROM books b
            LEFT JOIN categories c ON b.category_id = c.category_id
            LEFT JOIN inventory i ON b.book_id = i.book_id
            $where
            ORDER BY b.book_id ASC
            LIMIT $limit OFFSET $offset
        ";


        $books_list = DB::select($sql_main);


        $categories_data = DB::select("SELECT * FROM categories ORDER BY category_name");
        return view('admin.books.index', compact(
            'books_list','categories_data','search','category_id','status',
            'page','total_pages','total_books','start_page', 'end_page'
        ));
    }


    /* ================= CREATE ================= */
    public function create()
    {
        $categories = DB::select("SELECT * FROM categories ORDER BY category_name");
        return view('admin.books.create', compact('categories'));
    }


    public function store(Request $request)
    {
        $error = '';


        $title = trim($request->title);
        $author = trim($request->author);
        $category_id = (int)$request->category_id;
        $price = (int)$request->price;
        $discount = (int)$request->discount;
        $stock = (int)$request->stock;
        $description = trim($request->description);
        $link_images = trim($request->link_images);


        $image_name = '';


        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = strtolower($file->getClientOriginalExtension());


            if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                $image_name = 'uploads/books/' . time() . '_' . uniqid() . '.' . $ext;
                $file->move(public_path('uploads/books'), basename($image_name));
            } else {
                $error = "Chỉ chấp nhận JPG, PNG, GIF";
            }
        }


        if (!$error) {
            DB::beginTransaction();
            try {
                DB::insert("
                    INSERT INTO books (title, author, category_id, price, discount, description, images, link_images, created_at)
                    VALUES ('$title','$author',$category_id,$price,$discount,'$description','$image_name','$link_images',NOW())
                ");


                $new_id = DB::getPdo()->lastInsertId();


                DB::insert("
                    INSERT INTO inventory (book_id, stock, last_updated)
                    VALUES ($new_id, $stock, NOW())
                ");


                DB::commit();
                return redirect()->route('admin.books.index')->with('success','Thêm thành công');
            } catch (\Exception $e) {
                DB::rollBack();
                $error = $e->getMessage();
            }
        }


        return back()->with('error',$error);
    }


    /* ================= EDIT ================= */
    public function edit($id)
    {
        $book = DB::select("
            SELECT b.*, i.stock
            FROM books b
            LEFT JOIN inventory i ON b.book_id = i.book_id
            WHERE b.book_id = $id
        ");


        if (!$book) return redirect()->route('admin.books.index');


        $book = $book[0];


        $categories = DB::select("SELECT * FROM categories ORDER BY category_name");


        return view('admin.books.edit', compact('book','categories'));
    }


    public function update(Request $request, $id)
    {
        $book = DB::select("SELECT * FROM books WHERE book_id = $id")[0];


        $image_name = $book->images;


        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = strtolower($file->getClientOriginalExtension());


            if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                $image_name = 'uploads/books/' . time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/books'), basename($image_name));
            }
        }


        DB::update("
            UPDATE books SET
            title = '{$request->title}',
            author = '{$request->author}',
            category_id = '{$request->category_id}',
            price = '{$request->price}',
            discount = '{$request->discount}',
            description = '{$request->description}',
            images = '$image_name',
            link_images = '{$request->link_images}'
            WHERE book_id = $id
        ");


        DB::statement("
            INSERT INTO inventory (book_id, stock, last_updated)
            VALUES ($id, {$request->stock}, NOW())
            ON DUPLICATE KEY UPDATE stock = {$request->stock}, last_updated = NOW()
        ");


        return redirect()->route('admin.books.index')->with('success','Cập nhật thành công');
    }


    /* ================= DELETE ================= */
    public function delete($id)
    {
        $book = DB::select("
            SELECT b.*, i.stock
            FROM books b
            LEFT JOIN inventory i ON b.book_id = i.book_id
            WHERE b.book_id = $id
        ");


        if (!$book) return redirect()->route('admin.books.index');


        $book = $book[0];


        $check = DB::select("SELECT COUNT(*) as count FROM order_items WHERE book_id = $id")[0];


        return view('admin.books.delete', compact('book','check'));
    }


    public function destroy($id)
    {
        DB::delete("DELETE FROM books WHERE book_id = $id");
        return redirect()->route('admin.books.index')->with('success','Xóa thành công');
    }


    /* ================= SEARCH ================= */
    public function search(Request $request)
    {
        $search = $request->search ?? '';
        $category_id = $request->category_id ?? '';
        $status = $request->status ?? '';
        $min_price = $request->min_price ?? '';
        $max_price = $request->max_price ?? '';


        $where = " WHERE 1=1 ";


        if ($search != '') {
            $where .= " AND (b.title LIKE '%$search%' OR b.author LIKE '%$search%') ";
        }


        if ($category_id != '') {
            $where .= " AND b.category_id = '$category_id' ";
        }


        if ($min_price != '') {
            $where .= " AND b.price >= '$min_price' ";
        }


        if ($max_price != '') {
            $where .= " AND b.price <= '$max_price' ";
        }


        if ($status == 'active') {
            $where .= " AND i.stock > 0 ";
        } else {
            if ($status == 'out_of_stock') {
                $where .= " AND (i.stock <= 0 OR i.stock IS NULL) ";
            }
        }


        $sql = "
            SELECT b.*, c.category_name, i.stock
            FROM books b
            LEFT JOIN categories c ON b.category_id = c.category_id
            LEFT JOIN inventory i ON b.book_id = i.book_id
            $where
            ORDER BY b.created_at DESC
        ";


        $books_list = DB::select($sql);
        $total_results = count($books_list);


        $categories_data = DB::select("SELECT * FROM categories ORDER BY category_name");


        return view('admin.books.search', compact(
            'books_list','total_results','categories_data',
            'search','category_id','status','min_price','max_price'
        ));
    }
}
