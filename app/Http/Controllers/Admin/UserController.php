<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    // ================= INDEX =================
    public function index(Request $request)
    {
        $search = $request->search ?? '';
        $role = $request->role ?? '';
        $status = $request->status ?? '';
        $membership = $request->membership ?? '';


        $query = DB::table('users');


        if ($search != '') {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('fullname', 'like', "%$search%");
            });
        }


        if ($role != '') $query->where('role', $role);
        if ($status != '') $query->where('status', $status);
        if ($membership != '') $query->where('membership_level', $membership);


        $users = $query->paginate(10)->appends($request->all());


        $stats = DB::table('users')->selectRaw("
            COUNT(*) as total_users,
            SUM(role='Admin') as admins,
            SUM(role='Customer') as customers,
            SUM(status='Active') as active_count
        ")->first();


        return view('admin.users.index', compact('users','stats','search','role','status'));
    }


    // ================= CREATE =================
    public function create()
    {
        return view('admin.users.create');
    }


    public function store(Request $request)
    {
        if ($request->password != $request->confirm_password) {
            return back()->with('error', 'Mật khẩu không khớp');
        }


        if (strlen($request->password) < 6) {
            return back()->with('error', 'Mật khẩu phải >= 6 ký tự');
        }


        if (DB::table('users')->where('username', $request->username)->exists()) {
            return back()->with('error', 'Username đã tồn tại');
        }


        if (DB::table('users')->where('email', $request->email)->exists()) {
            return back()->with('error', 'Email đã tồn tại');
        }


        DB::table('users')->insert([
            'username' => $request->username,
            'password_hashed' => md5($request->password),
            'password' => $request->password,
            'email' => $request->email,
            'fullname' => $request->fullname,
            'role' => $request->role,
            'status' => $request->status,
            'phone' => $request->phone,
            'birthdate' => $request->birthdate,
            'gender' => $request->gender,
            'points' => $request->points,
            'membership_level' => $request->membership_level,
            'created_at' => now()
        ]);


        return redirect()->route('admin.users.index')->with('success','Thêm thành công');
    }


    // ================= DETAIL =================
    public function show($id)
    {
        $user = DB::table('users')->where('user_id', $id)->first();
        if (!$user) {
            return redirect()->route('admin.users.index')->with('error','Không tồn tại');
        }


        $order_stats = DB::table('orders')
            ->where('user_id',$id)
            ->selectRaw('COUNT(*) total_orders, SUM(total_amount) total_spent, MAX(created_at) last_order')
            ->first();


        $review_stats = DB::table('reviews')
            ->where('user_id',$id)
            ->selectRaw('COUNT(*) total_reviews, AVG(rating) avg_rating')
            ->first();


        // hoạt động gần đây
        $activities = [];


        $orders = DB::table('orders')
            ->where('user_id',$id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();


        foreach ($orders as $o) {
            $activities[] = [
                'type' => 'order',
                'id' => $o->order_id,
                'info' => $o->total_amount,
                'status' => $o->status,
                'created_at' => $o->created_at
            ];
        }


        $reviews = DB::table('reviews as r')
            ->join('books as b','r.book_id','=','b.book_id')
            ->where('r.user_id',$id)
            ->orderByDesc('r.created_at')
            ->limit(5)
            ->get();


        foreach ($reviews as $r) {
            $activities[] = [
                'type' => 'review',
                'id' => $r->review_id,
                'info' => $r->title,
                'status' => $r->rating,
                'created_at' => $r->created_at
            ];
        }


        return view('admin.users.detail', compact('user','order_stats','review_stats','activities'));
    }


    // ================= EDIT =================
    public function edit($id)
    {
        $user = DB::table('users')->where('user_id',$id)->first();
        if (!$user) return redirect()->route('admin.users.index');


        return view('admin.users.edit', compact('user'));
    }


    public function update(Request $request, $id)
    {
        // check trùng username
        if (DB::table('users')
            ->where('username',$request->username)
            ->where('user_id','!=',$id)
            ->exists()) {
            return back()->with('error','Username đã tồn tại');
        }


        // check trùng email
        if (DB::table('users')
            ->where('email',$request->email)
            ->where('user_id','!=',$id)
            ->exists()) {
            return back()->with('error','Email đã tồn tại');
        }


        DB::table('users')->where('user_id',$id)->update([
            'username'=>$request->username,
            'email'=>$request->email,
            'fullname'=>$request->fullname,
            'role'=>$request->role,
            'status'=>$request->status,
            'phone'=>$request->phone,
            'birthdate'=>$request->birthdate,
            'gender'=>$request->gender,
            'points'=>$request->points,
            'membership_level'=>$request->membership_level
        ]);


        return redirect()->route('admin.users.index')->with('success','Cập nhật thành công');
    }


    // ================= DELETE =================
    public function delete($id)
    {
        $user = DB::table('users')->where('user_id',$id)->first();
        if (!$user) return redirect()->route('admin.users.index');


        $order_count = DB::table('orders')->where('user_id',$id)->count();


        return view('admin.users.delete', compact('user','order_count'));
    }


    public function destroy(Request $request, $id)
    {
        if ($request->confirm_text != 'DELETE') {
            return back()->with('error','Sai xác nhận DELETE');
        }


        DB::table('users')->where('user_id',$id)->delete();


        return redirect()->route('admin.users.index')->with('success','Đã xóa');
    }


    // ================= ACTIVATE =================
    public function activate($id)
    {
        $user = DB::table('users')->where('user_id',$id)->first();


        if (!$user) {
            return redirect()->route('admin.users.index')->with('error','Không tồn tại');
        }


        if ($user->status == 'Active') {
            return redirect()->route('admin.users.index')->with('warning','Đã active rồi');
        }


        DB::table('users')->where('user_id',$id)->update([
            'status'=>'Active'
        ]);


        return redirect()->route('admin.users.index')
            ->with('success','Đã kích hoạt: '.$user->username);
    }


    // ================= DEACTIVATE =================
    public function deactivate($id)
    {
        $user = DB::table('users')->where('user_id',$id)->first();


        if (!$user) {
            return redirect()->route('admin.users.index')->with('error','Không tồn tại');
        }


        if ($user->status == 'Inactive') {
            return redirect()->route('admin.users.index')->with('warning','Đã bị khóa rồi');
        }


        DB::table('users')->where('user_id',$id)->update([
            'status'=>'Inactive'
        ]);


        return redirect()->route('admin.users.index')
            ->with('success','Đã khóa: '.$user->username);
    }


    // ================= RESET PASSWORD =================
    public function resetPassword(Request $request, $id)
    {
        $user = DB::table('users')->where('user_id',$id)->first();


        if (!$user) {
            return redirect()->route('admin.users.index')->with('error','Không tồn tại');
        }


        $error = '';


        if ($request->isMethod('post')) {
            $new_password = $request->new_password;
            $confirm_password = $request->confirm_password;


            if (strlen($new_password) < 6) {
                $error = "Mật khẩu phải >= 6 ký tự!";
            } elseif ($new_password != $confirm_password) {
                $error = "Không khớp!";
            } else {
                DB::table('users')->where('user_id',$id)->update([
                    'password_hashed'=>md5($new_password),
                    'password'=>$new_password
                ]);


                return redirect()->route('admin.users.index')
                    ->with('success','Đã reset mật khẩu: '.$user->username);
            }
        }


        return view('admin.users.reset_password', compact('user','error'));
    }
}
