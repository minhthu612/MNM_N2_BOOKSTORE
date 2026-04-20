<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CategoryController extends Controller
{
    // INDEX
    public function index(Request $request)
    {
        $search = $request->search ?? '';


        $where = " WHERE 1=1 ";
        if ($search != '') {
            $where .= " AND (category_name LIKE '%$search%') ";
        }


        // Xóa nhiều
        if ($request->has('delete_selected')) {
            if ($request->selected_ids) {
                foreach ($request->selected_ids as $id) {
                    DB::delete("DELETE FROM categories WHERE category_id = ?", [$id]);
                }
                return redirect()->route('admin.categories.index')->with('success', 'Đã xóa!');
            }
        }


        $page = $request->page ?? 1;
        if ($page < 1) $page = 1;


        $limit = 10;
        $offset = ($page - 1) * $limit;


        $total = DB::selectOne("SELECT COUNT(*) as total FROM categories $where")->total;
        $total_pages = ceil($total / $limit);


        $categories = DB::select("
            SELECT c.*,
            (SELECT COUNT(*) FROM books WHERE category_id = c.category_id) as book_count
            FROM categories c
            $where
            ORDER BY c.category_id ASC
            LIMIT $limit OFFSET $offset
        ");


        return view('admin.categories.index', compact('categories', 'search', 'total', 'total_pages', 'page'));
    }


    // CREATE
    public function create()
    {
        return view('admin.categories.create');
    }


    public function store(Request $request)
    {
        $name = $request->name;
        $description = $request->description;


        $check = DB::select("SELECT category_id FROM categories WHERE category_name = ?", [$name]);


        if (count($check) > 0) {
            return back()->with('error', 'Tên danh mục đã tồn tại');
        }


        DB::insert("INSERT INTO categories (category_name, description) VALUES (?, ?)", [$name, $description]);


        return redirect()->route('admin.categories.index')->with('success', 'Thêm thành công');
    }


    // EDIT
    public function edit($id)
    {
        $category = DB::selectOne("SELECT * FROM categories WHERE category_id = ?", [$id]);


        if (!$category) return redirect()->route('admin.categories.index');


        $book_count = DB::selectOne("SELECT COUNT(*) as total FROM books WHERE category_id = ?", [$id])->total;


        return view('admin.categories.edit', compact('category', 'book_count'));
    }


    public function update(Request $request, $id)
    {
        $name = $request->category_name;
        $description = $request->description;


        $check = DB::select("SELECT category_id FROM categories WHERE category_name = ? AND category_id != ?", [$name, $id]);


        if (count($check) > 0) {
            return back()->with('error', 'Tên đã tồn tại');
        }


        DB::update("UPDATE categories SET category_name=?, description=? WHERE category_id=?", [$name, $description, $id]);


        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật thành công');
    }


    // DELETE
    public function delete($id)
    {
        $category = DB::selectOne("SELECT * FROM categories WHERE category_id = ?", [$id]);


        if (!$category) return redirect()->route('admin.categories.index');


        $book_count = DB::selectOne("SELECT COUNT(*) as count FROM books WHERE category_id = ?", [$id])->count;


        $other_categories = DB::select("SELECT * FROM categories WHERE category_id != ? ORDER BY category_name", [$id]);


        return view('admin.categories.delete', compact('category', 'book_count', 'other_categories'));
    }


    public function destroy(Request $request, $id)
    {
        $book_count = DB::selectOne("SELECT COUNT(*) as count FROM books WHERE category_id = ?", [$id])->count;


        if ($book_count > 0) {
            $action = $request->action;


            if ($action == 'move') {
                $new_id = $request->new_category_id;
                DB::update("UPDATE books SET category_id=? WHERE category_id=?", [$new_id, $id]);
            } else {
                DB::delete("DELETE FROM books WHERE category_id=?", [$id]);
            }
        }


        DB::delete("DELETE FROM categories WHERE category_id=?", [$id]);


        return redirect()->route('admin.categories.index')->with('success', 'Đã xóa');
    }
}
