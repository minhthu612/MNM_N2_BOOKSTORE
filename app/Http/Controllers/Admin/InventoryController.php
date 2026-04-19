<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class InventoryController extends Controller
{
    // ================= INDEX =================
   public function index(Request $request)
{
    $search = $request->search ?? '';
    $status = $request->status ?? '';
    $stock_filter = $request->stock_filter ?? '';


    $page = 1;
    if (isset($request->page)) {
        $page = (int)$request->page;
    }
    if ($page < 1) {
        $page = 1;
    }


    $limit = 10;
    $offset = ($page - 1) * $limit;


    $query = DB::table('inventory as i')
        ->join('books as b', 'i.book_id', '=', 'b.book_id');


    if ($search != '') {
        $query->where(function ($q) use ($search) {
            $q->where('b.title', 'like', "%$search%")
              ->orWhere('b.author', 'like', "%$search%");
        });
    }


    if ($status != '' && $status != 'all') {
        $query->where('i.stock_status', $status);
    }


    if ($stock_filter != '') {
        if ($stock_filter == 'negative') {
            $query->where('i.stock', '<', 0);
        } elseif ($stock_filter == 'zero') {
            $query->where('i.stock', 0);
        } elseif ($stock_filter == 'low') {
            $query->whereColumn('i.stock', '<', 'i.reorder_level')
                  ->where('i.stock', '>', 0);
        } elseif ($stock_filter == 'good') {
            $query->whereColumn('i.stock', '>=', 'i.reorder_level');
        }
    }


    // đếm tổng
    $total_items = (clone $query)->count();
    $total_pages = ceil($total_items / $limit);


    // lấy data
    $inventory_list = $query
        ->select('i.*','b.title','b.author','b.price')
        ->orderByDesc('i.stock')
        ->orderBy('i.last_updated')
        ->limit($limit)
        ->offset($offset)
        ->get();


    $stats = DB::table('inventory')->selectRaw("
        COUNT(*) as total_items,
        SUM(CASE WHEN stock < 0 THEN 1 ELSE 0 END) as negative,
        SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as zero,
        SUM(CASE WHEN stock < reorder_level AND stock > 0 THEN 1 ELSE 0 END) as low,
        SUM(CASE WHEN stock >= reorder_level THEN 1 ELSE 0 END) as good
    ")->first();


    return view('admin.inventory.index', compact(
        'inventory_list',
        'stats',
        'search',
        'status',
        'stock_filter',
        'page',
        'total_pages',
        'total_items'
    ));
}


    // ================= HISTORY =================
    public function history(Request $request)
{
    $search = $request->search ?? '';
    $status_filter = $request->status ?? '';


    $page = 1;
    if (isset($request->page)) {
        $page = (int)$request->page;
    }
    if ($page < 1) {
        $page = 1;
    }


    $limit = 10;
    $offset = ($page - 1) * $limit;


    $query = DB::table('inventory as i')
        ->join('books as b','i.book_id','=','b.book_id');


    if ($search != '') {
        $query->where(function($q) use ($search){
            $q->where('b.title','like',"%$search%")
              ->orWhere('b.author','like',"%$search%");
        });
    }


    if ($status_filter != '') {
        $query->where('i.stock_status',$status_filter);
    }


    // tổng bản ghi
    $total_items = (clone $query)->count();
    $total_pages = ceil($total_items / $limit);


    $inventory_list = $query
        ->select('i.*','b.title','b.author')
        ->orderByDesc('i.last_updated')
        ->limit($limit)
        ->offset($offset)
        ->get();


    $stats = DB::table('inventory')->selectRaw("
        SUM(CASE WHEN stock > 0 THEN 1 ELSE 0 END) as in_stock,
        SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END) as out_of_stock,
        SUM(stock) as total_qty
    ")->first();


    return view('admin.inventory.history', compact(
        'inventory_list',
        'stats',
        'search',
        'status_filter',
        'page',
        'total_pages',
        'total_items'
    ));
}


    // ================= UPDATE =================
    public function edit($id)
    {
        $inventory = DB::table('inventory as i')
            ->join('books as b','i.book_id','=','b.book_id')
            ->where('i.inventory_id',$id)
            ->select('i.*','b.title','b.author')
            ->first();


        if (!$inventory) return redirect()->route('admin.inventory.history');


        return view('admin.inventory.update', compact('inventory'));
    }


    public function update(Request $request, $id)
    {
        $inventory = DB::table('inventory')->where('inventory_id',$id)->first();
        if (!$inventory) return back();


        $adjustment = (int)$request->adjustment;
        $note = $request->note;


        $old_stock = $inventory->stock;
        $new_stock = $old_stock + $adjustment;


        $stock_status = 'ACTIVE';


        if ($new_stock <= 0) {
            $stock_status = 'OUT_OF_STOCK';
            $new_stock = 0;
        } elseif ($new_stock < $inventory->reorder_level) {
            $stock_status = 'LOW_STOCK';
        }


        DB::table('inventory')->where('inventory_id',$id)->update([
            'stock'=>$new_stock,
            'stock_status'=>$stock_status,
            'last_updated'=>now()
        ]);


        DB::table('inventory_history')->insert([
            'inventory_id'=>$id,
            'old_stock'=>$old_stock,
            'new_stock'=>$new_stock,
            'adjustment'=>$adjustment,
            'note'=>$note,
            'created_by'=>session('user_id'),
            'created_at'=>now()
        ]);


        return redirect()->route('admin.inventory.history')
            ->with('success','Đã cập nhật tồn kho');
    }
}
